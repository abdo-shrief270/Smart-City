# Firebase Realtime Database — Schema Reference

This document describes every node the Smart City platform reads from or writes to
in the Firebase Realtime Database, how those nodes map to local database tables, and
how synchronization is wired.

> Source of truth: `app/Services/FirebaseService.php` and the Filament pages/widgets
> under `app/Filament/`. Field aliases listed below are the exact keys the code
> accepts (the code is intentionally lenient and supports legacy key names).

---

## 1. Connection & configuration

Firebase credentials are **not** stored in `.env`. They live in the Spatie settings
store under the `firebase` group and are editable from the admin UI
(*Admin → Manage Firebase Settings*, page `App\Filament\Pages\ManageFirebaseSettings`).

| Setting key                  | Type   | Used by REST calls? | Notes                                       |
| ---------------------------- | ------ | ------------------- | ------------------------------------------- |
| `firebase.api_key`           | string | ✅ (`?auth=`)        | Appended as the auth query param            |
| `firebase.database_url`      | string | ✅ (base URL)        | Required; if empty all reads/writes no-op   |
| `firebase.auth_domain`       | string | ❌                   | Stored for completeness / client SDK parity |
| `firebase.project_id`        | string | ❌                   | "                                           |
| `firebase.storage_bucket`    | string | ❌                   | "                                           |
| `firebase.messaging_sender_id` | string | ❌                 | "                                           |
| `firebase.app_id`            | string | ❌                   | "                                           |

Defined in `app/Settings/FirebaseSettings.php`; seeded by the settings migration
`database/migrations/2026_02_07_064500_add_firebase_settings.php`.

### REST access pattern

`FirebaseService` talks to Firebase over the plain REST API — no Admin SDK:

```
GET  {database_url}/{path}.json?auth={api_key}
PUT  {database_url}/{path}.json?auth={api_key}     (body = raw JSON)
```

- `FirebaseService::get(string $path)` → decoded JSON or `null` on any failure.
- `FirebaseService::set(string $path, $value): bool` → `true` if HTTP 2xx.

Both methods silently return `null` / `false` when `database_url` is empty, so the
app degrades gracefully when Firebase is not configured.

---

## 2. Node map (top level)

```
{database_url}/
├── gate-logs/        ← read-only (device → app, see gate-logs-api.md)
├── smart-tank/       ↔ read + write
├── smart_farm/       ↔ read + write   (note: underscore, not hyphen)
├── smart-parking/    ↔ read + write
├── fire-alarm/       ↔ read + write
├── smart-lighting/   ↔ read + write
└── smart-traffic/    ↔ read + write
```

Data-flow legend: **device → app** values are produced by IoT hardware and consumed
by the dashboard; **app → device** values are commands written by an admin in the UI.

---

## 3. `smart-tank/`

Single object (latest snapshot).

| Field      | Type    | Direction      | Read by                                   | Written by                          |
| ---------- | ------- | -------------- | ----------------------------------------- | ----------------------------------- |
| `level`    | int     | device → app   | `FirebaseService::syncToDatabase`, `SmartTankWidget`, `SystemAlertsWidget` | — |
| `status`   | string  | device → app   | sync (derived if missing/`Unknown`)       | —                                   |
| `isPumpOn` | bool    | app → device   | sync                                       | `SmartTank::togglePump()` → `smart-tank/isPumpOn` |

`status` derivation when absent: `level < 20` → `Low`, `level > 80` → `Critical`,
else `Normal`.

**Mirrored to:** table `smart_tank_data` (`level`, `status`, `is_pump_on`) — a new
row is inserted on every sync (time series).

Example:

```json
{
  "smart-tank": { "level": 64, "status": "Normal", "isPumpOn": false }
}
```

---

## 4. `smart_farm/`

Accepts either a flat object **or** a nested `sensors` child. The reader tries
`smart_farm/sensors/*` first, then falls back to `smart_farm/*`.

| Field (aliases)            | Type | Direction    | Notes                                  |
| -------------------------- | ---- | ------------ | -------------------------------------- |
| `temperature` / `temp`     | int  | device → app | °C                                     |
| `humidity`                 | int  | device → app | %                                      |
| `pump` / `isPumpOn`        | bool | app → device | command write target: `smart_farm/sensors/pump` |

**Mirrored to:** table `smart_farm_data` (`temp`, `humidity`, `is_pump_on`) — new
row per sync. The `SmartFarm` page reads display values from this DB table, not
directly from Firebase; only the pump command is written to Firebase.

Example (nested form, as written by the app):

```json
{
  "smart_farm": { "sensors": { "temperature": 28, "humidity": 55, "pump": true } }
}
```

---

## 5. `smart-parking/`

A map keyed by slot identifier `"{AREA}-{NUMBER}"`, e.g. `A-1`, `B-12`. Legacy keys
like `slot_A_1` are normalised (`slot_` stripped, `_` → `-`).

| Field             | Type   | Direction    | Notes                                              |
| ----------------- | ------ | ------------ | -------------------------------------------------- |
| `area`            | string | app → device | `A` or `B`                                          |
| `slot_number`     | int    | app → device |                                                    |
| `status`          | string | ↔            | `available` \| `occupied` \| `reserved`            |
| `occupied` / `isOccupied` | bool | device → app | IoT sensor truth; drives status when no active reservation |
| `has_reservation` | bool   | app → device |                                                    |
| `cost_per_hour`   | number | app → device |                                                    |
| `updated_at`      | string | app → device | ISO-8601                                            |

Sync rule (`syncToDatabase`): the sensor's `occupied` value updates the matching
`parking_slots` row **only when that slot has no active reservation** — reservations
take precedence over the raw sensor.

Write paths:
- `FirebaseService::syncParkingToFirebase()` — replaces the whole `smart-parking` node.
- `FirebaseService::updateSlotInFirebase($slot)` — writes a single `smart-parking/{A-1}` child.

**Mirrored to / from:** tables `parking_slots` and `parking_reservations`.

Example:

```json
{
  "smart-parking": {
    "A-1": { "area": "A", "slot_number": 1, "status": "occupied",
             "occupied": true, "has_reservation": false,
             "cost_per_hour": 10, "updated_at": "2026-05-17T10:00:00+00:00" }
  }
}
```

---

## 6. `fire-alarm/`

| Field         | Type    | Direction    | Notes                                          |
| ------------- | ------- | ------------ | ---------------------------------------------- |
| `flameValue`  | int     | device → app | Raw flame ADC, 0–4095                           |
| `fireDetected`| bool    | device → app |                                                |
| `gasValue`    | int     | device → app | Raw gas ADC, 0–4095                             |
| `gasDetected` | bool    | device → app | Optional; if absent, derived as `gasValue ≥ 2000` |
| `pumpActive`  | bool    | app → device | Command write target: `fire-alarm/pumpActive`  |

`pumpActive` is intentionally **not** read back during polling (the admin button is
the source of truth to avoid a write/poll race). Read by `FireAlarm` page and
`SystemAlertsWidget`. Not mirrored to a DB table.

Example:

```json
{
  "fire-alarm": { "flameValue": 120, "fireDetected": false,
                  "gasValue": 350, "gasDetected": false, "pumpActive": false }
}
```

---

## 7. `smart-lighting/`

| Field       | Type              | Direction    | Notes                                  |
| ----------- | ----------------- | ------------ | -------------------------------------- |
| `mode`      | string            | ↔            | `manual` \| `auto`                     |
| `lights`    | array[8] of bool  | ↔            | Indices 0–7; `true` = on               |

Write paths: `smart-lighting/mode` (whole), `smart-lighting/lights/{index}` (single
light). Manual light toggles are blocked in `auto` mode. Not mirrored to a DB table.

Example:

```json
{
  "smart-lighting": { "mode": "manual",
                       "lights": [true,false,false,false,false,false,false,false] }
}
```

---

## 8. `smart-traffic/`

| Field    | Type   | Direction | Notes                                            |
| -------- | ------ | --------- | ------------------------------------------------ |
| `mode`   | string | ↔         | `manual` \| `auto`                               |
| `lights` | object | ↔         | Keys `north`/`south`/`east`/`west` → `red`\|`yellow`\|`green` |

Write paths: `smart-traffic/mode`, `smart-traffic/lights/{direction}`. Manual
control blocked in `auto` mode. Not mirrored to a DB table.

Example:

```json
{
  "smart-traffic": { "mode": "manual",
                      "lights": { "north": "green", "south": "red",
                                  "east": "red", "west": "red" } }
}
```

---

## 9. `gate-logs/`

Append-only list of vehicle gate events keyed by Firebase push IDs. Documented in
detail (including the device-side write contract and the pull/sync API) in
[`gate-logs-api.md`](./gate-logs-api.md).

---

## 10. Synchronization pipeline

`FirebaseService::syncToDatabase()` runs, in order:

1. `syncGateLogsFromFirebase()` — `gate-logs` → `gate_logs` table (non-fatal on error).
2. `smart-tank`  → insert `smart_tank_data` row.
3. `smart_farm`  → insert `smart_farm_data` row.
4. `smart-parking` → update `parking_slots` rows (reservation-aware).

It is triggered by any of:

| Trigger                                   | Where                                                            |
| ----------------------------------------- | ---------------------------------------------------------------- |
| Scheduled job `SyncFirebaseData`          | `routes/console.php` — `Schedule::job(...)->everySecond()`       |
| Webhook `POST /api/webhooks/firebase-sync`| `routes/api.php` → `WebhookController::handleFirebaseSync`        |
| Dashboard action "Sync Firebase Now"      | `App\Filament\Pages\Dashboard`                                   |
| Gate Logs page action "Sync from Firebase"| `ListGateLogs` (gate logs only — calls `syncGateLogsFromFirebase`)|

> The `everySecond()` schedule requires a running scheduler/worker. In production
> use `php artisan schedule:work` (or a cron entry calling `schedule:run`) plus a
> queue worker, since `SyncFirebaseData` is a queued job.

---

## 11. Table mirror summary

| Firebase node    | Local table            | Cardinality              |
| ---------------- | ---------------------- | ------------------------ |
| `gate-logs`      | `gate_logs`            | 1 row per event (dedup)  |
| `smart-tank`     | `smart_tank_data`      | 1 row per sync (history) |
| `smart_farm`     | `smart_farm_data`      | 1 row per sync (history) |
| `smart-parking`  | `parking_slots`        | updated in place         |
| `fire-alarm`     | — (live read only)     | not persisted            |
| `smart-lighting` | — (live read/write)    | not persisted            |
| `smart-traffic`  | — (live read/write)    | not persisted            |
