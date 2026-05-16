# Gate Logs — API & Endpoint Usage

How vehicle gate events flow from the IoT gate device, into Firebase, and finally
into the `gate_logs` table that powers the **Gate Logs** admin resource.

Related: [`firebase-schema.md`](./firebase-schema.md) for the full database schema.

---

## 1. Data flow overview

```
 Gate device (camera/RFID)
        │  pushes one JSON child per event
        ▼
 Firebase RTDB:  /gate-logs/{pushId}
        │  pulled & deduplicated
        ▼
 FirebaseService::syncGateLogsFromFirebase()
        │  validated + normalised
        ▼
 Laravel DB:  gate_logs table
        │
        ▼
 Filament admin UI:  /admin/gate-logs
```

There is **no public ingest endpoint in Laravel** for individual gate events. The
device writes directly to Firebase; Laravel pulls on a trigger. The only HTTP
endpoint involved is the sync webhook (Section 4).

---

## 2. Device-side write contract (Firebase REST)

The gate device pushes one child per event under `gate-logs` using a Firebase
push ID (auto-generated, time-ordered key):

```
POST {database_url}/gate-logs.json?auth={api_key}
Content-Type: application/json
```

### Accepted body fields

| Field                         | Required | Type            | Notes                                              |
| ----------------------------- | -------- | --------------- | -------------------------------------------------- |
| `plate` *(or)* `plate_number` | ✅       | string          | Non-empty after trim; stored upper-cased           |
| `gate` *(or)* `gate_number`   | ✅       | int             | Must be `≥ 1`                                       |
| `direction`                   | ✅       | string          | `in` or `out` (case-insensitive)                   |
| `timestamp` *(or)* `logged_at`| ⬜       | int \| string   | Unix seconds (int) or parseable datetime string; defaults to "now" if missing/invalid |

Any child failing the required-field rules is **silently skipped** by the importer
(no error raised), so malformed device writes never block the sync.

### Example device write

```bash
curl -X POST \
  "https://YOUR-DB.firebaseio.com/gate-logs.json?auth=YOUR_API_KEY" \
  -H "Content-Type: application/json" \
  -d '{ "plate": "ABC-1234", "gate": 1, "direction": "in", "timestamp": 1712345678 }'
```

Resulting Firebase state:

```json
{
  "gate-logs": {
    "-NabcXYZ123": {
      "plate": "ABC-1234",
      "gate": 1,
      "direction": "in",
      "timestamp": 1712345678
    }
  }
}
```

---

## 3. Import / sync logic

`App\Services\FirebaseService::syncGateLogsFromFirebase(): int`

1. `GET /gate-logs.json` — fetch all children.
2. Load existing `gate_logs.firebase_key` values into a lookup set.
3. For each child:
   - Skip if the push ID already exists in `gate_logs` (**dedup by `firebase_key`**).
   - Resolve `plate` / `gate` / `direction` (with the aliases above).
   - Validate: non-empty plate, `gate ≥ 1`, direction ∈ {`in`,`out`}.
   - Resolve timestamp: numeric → `Carbon::createFromTimestamp`; string →
     `Carbon::parse`; otherwise `now()`.
   - Insert a `gate_logs` row (`plate_number` upper-cased).
4. Return the count of newly inserted rows.

The operation is **idempotent** — re-running it imports only new push IDs. Firebase
children are never modified or deleted by Laravel.

---

## 4. Triggering a sync

| Method | Endpoint / location | Scope | Auth |
| ------ | ------------------- | ----- | ---- |
| **Webhook** | `POST /api/webhooks/firebase-sync` | Full sync (gate logs **+** tank/farm/parking) | None (public) |
| **Scheduler** | `routes/console.php` → `SyncFirebaseData` job, `->everySecond()` | Full sync | n/a |
| **Admin UI** | *Admin → Gate Logs → "Sync from Firebase"* button | Gate logs only | Filament auth |
| **Admin UI** | *Admin → Dashboard → "Sync Firebase Now"* button | Full sync | Filament auth |

### Webhook endpoint detail

`routes/api.php`:

```
POST /api/webhooks/firebase-sync   →  WebhookController@handleFirebaseSync
```

- **Request body:** none required.
- **Success:** `200` `{"message":"Sync triggered successfully"}`
- **Failure:** `500` `{"error":"<exception message>"}`

```bash
curl -X POST https://your-app.test/api/webhooks/firebase-sync
```

> ⚠️ This endpoint is currently **unauthenticated** and runs the full sync
> (`syncToDatabase()`), not just gate logs. If exposing it publicly, put it behind
> a signed URL, shared secret header, or middleware before production use.

---

## 5. `gate_logs` table schema

Migration: `database/migrations/2026_04_22_000000_create_gate_logs_table.php`
Model: `App\Models\GateLog`

| Column         | Type                  | Constraints              | Meaning                                  |
| -------------- | --------------------- | ------------------------ | ---------------------------------------- |
| `id`           | bigint                | PK                       |                                          |
| `firebase_key` | string, nullable      | **unique** (dedup key)   | Firebase push ID; `null` = manual entry  |
| `plate_number` | string(32)            | indexed                  | Upper-cased plate                        |
| `gate_number`  | unsignedTinyInteger   | indexed                  | Gate the vehicle passed                  |
| `direction`    | enum(`in`,`out`)      | indexed                  | `in` = entering, `out` = leaving         |
| `logged_at`    | timestamp             | indexed                  | Event time (from device or import time)  |
| `created_at` / `updated_at` | timestamps | —                     | Row "synced at" time                     |

Model `$fillable`: `firebase_key`, `plate_number`, `gate_number`, `direction`,
`logged_at`. Casts: `logged_at` → datetime, `gate_number` → integer.

---

## 6. Admin UI (Filament resource)

`App\Filament\Resources\GateLogs\GateLogResource` — navigation label **"Gate Logs"**,
truck icon, sort 7. These are admin panel routes (Livewire pages, not a JSON API):

| Page   | Route                          |
| ------ | ------------------------------ |
| index  | `/admin/gate-logs`             |
| create | `/admin/gate-logs/create`     |
| edit   | `/admin/gate-logs/{record}/edit` |

Table (`GateLogsTable`): default sort `logged_at desc`; columns Time, Plate
(searchable/copyable), Gate #, Status (`in` → "→ Entering" green, `out` →
"← Leaving" warning), Source (Firebase vs Manual), Synced at. Filters: direction,
gate number, "Today only". Row actions: edit, delete + bulk delete.

- **"Sync from Firebase"** action → `syncGateLogsFromFirebase()`, then a
  notification: *"Imported {n} new gate log(s)"*.
- **"Manual entry"** → create a row by hand; `firebase_key` stays `null`, so the
  Source column shows **Manual** and it is excluded from dedup.

---

## 7. Quick test recipe

```bash
# 1. Seed a fake gate event in Firebase
curl -X POST "https://YOUR-DB.firebaseio.com/gate-logs.json?auth=YOUR_API_KEY" \
  -H "Content-Type: application/json" \
  -d '{"plate":"test-9","gate":2,"direction":"out"}'

# 2. Trigger the import (full sync webhook)
curl -X POST https://your-app.test/api/webhooks/firebase-sync
# → {"message":"Sync triggered successfully"}

# 3. Verify
php artisan tinker --execute="echo App\Models\GateLog::latest()->first();"
```

Re-running step 2 will **not** create a duplicate (same Firebase push ID).
