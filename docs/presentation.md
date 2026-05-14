# Smart City Platform — Graduation Project Presentation

> Speaker notes are written in the first person, as if delivered by the student.
> Each `##` heading is a slide; the bullet points under it are what goes on the slide.
> Paragraphs under "Speaker script" are what you say out loud (keep what you like, drop the rest).

---

## Slide 1 — Title

- **Smart City Platform**
- An integrated IoT management system
- Your Name · Team Member Names · Supervisor

**Speaker script:**
Good morning. I'd like to present our graduation project — a Smart City platform that connects six different IoT subsystems into a single web-based admin panel. I'll walk you through the idea, the pages, the Firebase schema behind each feature, the backend work we did, and what the system can do overall.

---

## Slide 2 — The Idea

- Modern cities rely on many disconnected IoT systems (parking meters, fire alarms, street lamps, farm sensors, water tanks, traffic signals).
- Each of these usually has its own app or dashboard — operators juggle many tools.
- **Our goal:** unify them into one role-aware admin panel with live sensor data and remote control.

**Speaker script:**
The idea started from a simple observation. A modern city has dozens of IoT systems running in parallel, and each one tends to come with its own separate dashboard. For an operator or a municipality that's hard to manage. We wanted to build a single platform where every subsystem is observable and controllable from one place, while keeping the hardware side cheap and reproducible.

---

## Slide 3 — What the system does in one picture

- Edge devices (ESP32) → Firebase Realtime Database → Laravel admin panel
- Operators watch live data and push commands (pumps, lights, signals) back to the devices.
- Regular customers can register, reserve parking slots, and pay from their balance.

**Speaker script:**
The data flow is the same for every subsystem. Low-cost ESP32 devices read sensors and publish their values into Firebase. Our Laravel application reads those same Firebase paths in near real-time and renders them on purpose-built pages. The operator can also push commands back through Firebase — for example toggling a pump or switching traffic lights — and the edge device picks up that change.

---

## Slide 4 — Tech Stack

- **Backend:** Laravel 12 (PHP 8.2+)
- **Admin UI:** Filament 5 (Livewire + Alpine + Tailwind)
- **Realtime:** Firebase Realtime Database (REST API)
- **Auth / RBAC:** Laravel Sanctum + Spatie Permission + Filament Shield
- **Settings:** Spatie Laravel-Settings
- **Build tooling:** Vite + Tailwind 4
- **Hardware side (sister project):** ESP32 + sensors (DHT, flame, MQ gas, ultrasonic, LDR, IR, etc.)

---

## Slide 5 — System architecture

- **ESP32 firmware** publishes sensor readings to Firebase nodes every second.
- **Firebase Realtime DB** is the single source of truth for live state.
- **Laravel app** does three jobs:
  1. Polls Firebase for live views (`wire:poll` on Filament pages).
  2. Writes control commands back (pumps, lights, traffic mode).
  3. Periodically **syncs to MySQL** so we keep a historical log for analytics.
- **MySQL** stores users, roles, parking reservations, and time-series snapshots.

**Speaker script:**
Architecturally we kept three tiers. The hardware edge publishes data. Firebase holds the live state. Our Laravel backend does all the human-facing work, plus a background job that snapshots Firebase into MySQL once a second so we can build charts and history later without hammering the database at request time.

---

## Slide 6 — Public pages

- **`/` Landing page** — hero, feature cards (one per subsystem), theme toggle, QR code that links to `/about`.
- **`/about`** — project title, abstract, project idea, team members, supervisors. Lives outside the admin panel so it can be scanned/visited by anyone (this is what the QR code on every page encodes).

**Speaker script:**
We have two public-facing pages. The landing page is an attractive entry point with six cards, one for each subsystem. Clicking a card jumps straight into the admin page for that feature. The about page is what our QR code points to — judges or reviewers can scan the code on the navbar and immediately see the project idea, team and supervisors without needing to log in.

---

## Slide 7 — Admin panel at a glance

- Built on **Filament 5**, customized with:
  - **Custom Dashboard** with header quick-actions (New User, New Parking Slot, Firebase Settings, Sync Firebase Now, Landing Page).
  - **User menu** extended with a "Landing Page" link and our **QR code** (scan → opens `/about`).
  - **Widgets** auto-discovered from `app/Filament/Widgets`.
  - **Global search disabled** to keep the topbar clean.

**Speaker script:**
The admin panel is built on Filament. We customized the default Filament experience with our own dashboard — it has quick-action buttons at the top of the page so operators can jump to common tasks with one click. We also added a QR code in the navbar and a user-menu shortcut that opens the about page in a new tab.

---

## Slide 8 — Dashboard widgets

- `StatsOverviewWidget` — total users, active reservations, slot availability, today's revenue.
- `SystemAlertsWidget` — live Firebase-driven alerts for fire, gas, tank and farm temperature (polls every 10 s).
- `RecentReservationsWidget` — table of the 5 latest parking reservations.
- `ParkingOccupancyChart`, `RevenueChartWidget`, `ReservationsChartWidget` — trend charts.
- `SmartFarmWidget`, `SmartTankWidget`, `SmartParkingWidget` — compact per-feature gauges.
- `IoTSensorChart`, `UserGrowthChart` — long-term analytics.

**Speaker script:**
On the dashboard we combined live sensor alerts with business metrics. The System Alerts widget reads straight from Firebase every ten seconds and color-codes fire, gas, tank and farm conditions. Around it we have revenue charts, parking occupancy charts, and a live table of the latest reservations. Each widget is permission-gated so non-admin roles only see what they're allowed to see.

---

## Slide 9 — Feature 1 · Smart Farm

- **Purpose:** monitor crop environment (temperature, humidity) and run the irrigation pump remotely.
- **Page:** `/admin/smart-farm` — live temperature gauge, humidity gauge, pump toggle.
- **Firebase path:** `smart_farm/`
  ```
  smart_farm/
    sensors/
      temperature : int    (°C)
      humidity    : int    (%)
      pump        : bool   (irrigation pump on/off)
  ```
- **Page actions:** `fetchData()` polls every 5 s · `togglePump()` writes `smart_farm/sensors/pump`.
- **Backend model:** `SmartFarmData` — historical snapshot (temp, humidity, is_pump_on) written by the sync job.

---

## Slide 10 — Feature 2 · Smart Parking

- **Purpose:** manage parking slots as a marketplace — users reserve, the IoT sensor confirms occupancy, revenue is tracked.
- **Page:** `/admin/smart-parking` — stats (total/available/occupied/revenue), grid of slots, reserve/release actions.
- **Firebase path:** `smart-parking/`
  ```
  smart-parking/
    A-1/
      area            : string ("A")
      slot_number     : int
      status          : "available" | "occupied"
      occupied        : bool    (raw sensor reading)
      has_reservation : bool
      cost_per_hour   : float
      updated_at      : ISO-8601
    A-2/ …
    B-1/ …
  ```
- **DB tables:** `parking_slots`, `parking_reservations` (with soft status: pending/active/completed).
- **Page actions:** `reserveSlotAction()` — charges user balance and marks slot active; `releaseSlotAction()` — calculates cost × duration, closes reservation, returns slot to "available".
- **Two-way sync:** IoT sensor `occupied` flag updates DB; DB changes push back to Firebase for an IoT display board.

---

## Slide 11 — Feature 3 · Smart Traffic

- **Purpose:** simulate and control a 4-way traffic intersection (N-S vs. E-W phases with yellow transition).
- **Page:** `/admin/smart-traffic` — animated lights, countdown timer (Alpine.js), manual/auto mode, configurable green-timer duration, manual direction override.
- **Firebase path:** `smart-traffic/`
  ```
  smart-traffic/
    mode             : "manual" | "auto"
    direction        : "ns_green" | "ew_green"
    greenTimer       : int (seconds)
    nextSwitchTime   : int (unix timestamp — used for countdown)
    pendingDirection : string | null
    transitionUntil  : int (unix timestamp — yellow phase end)
  ```
- **Page actions:** `pollData()` · `toggleMode()` · `saveTimers()` · `setDirection()`.
- **Backend logic:** in auto mode the server computes `nextSwitchTime = now + greenTimer` and flips direction when the clock crosses zero; yellow transition is a 2-second window that ESP32 respects.

---

## Slide 12 — Feature 4 · Smart Lighting

- **Purpose:** control 8 street lamps arranged as a crossroad (2 lamps per road section).
- **Page:** `/admin/smart-lighting` — top-down map view with glowing lamp buttons, manual/auto mode.
- **Firebase path:** `smart-lighting/`
  ```
  smart-lighting/
    mode    : "manual" | "auto"
    lights/
      0: bool   (N-1)
      1: bool   (N-2)
      2: bool   (E-1)
      3: bool   (E-2)
      4: bool   (S-1)
      5: bool   (S-2)
      6: bool   (W-1)
      7: bool   (W-2)
  ```
- **Page actions:** `refreshData()` polls every 2 s · `toggleMode()` · `toggleLight(int $index)`.
- **Note:** in auto mode the ESP32 drives the lamps from an LDR (light-dependent resistor) reading, and the manual toggle is disabled in the UI to avoid conflicts.

---

## Slide 13 — Feature 5 · Fire Alarm

- **Purpose:** detect fire (flame sensor) and gas leaks (MQ-series gas sensor), fire a water-pump suppression system.
- **Page:** `/admin/fire-alarm` — status cards, flame sensor bar, gas sensor bar with LEAK badge, animated building visual that catches fire when triggered, manual pump control.
- **Firebase path:** `fire-alarm/`
  ```
  fire-alarm/
    flameValue   : int  (0–4095 ADC)
    fireDetected : bool
    gasValue     : int  (0–4095 ADC)
    gasDetected  : bool (optional — derived from threshold if missing)
    pumpActive   : bool
  ```
- **Page actions:** `pollData()` every 3 s · `togglePump()` — writes Firebase first, only flips the UI if the write succeeds and blocks polling from overwriting the choice for 10 s.
- **Design decision:** poll does **not** overwrite `pumpActive` — the admin button is the source of truth to avoid a race where a slow write gets reverted by the next poll.

---

## Slide 14 — Feature 6 · Smart Tank

- **Purpose:** monitor water-tank level and control the fill/drain pump.
- **Page:** `/admin/smart-tank` — level gauge, status (Normal / Low / Critical), pump toggle, 20-point history chart.
- **Firebase path:** `smart-tank/`
  ```
  smart-tank/
    level    : int  (0–100 %)
    status   : "Normal" | "Low" | "Critical"
    isPumpOn : bool
  ```
- **DB table:** `smart_tank_data` — level + status + is_pump_on time-series, written by the sync job so the chart is backed by real history.
- **Page actions:** `fetchData()` reads from MySQL (fast) · `togglePump()` writes `smart-tank/isPumpOn`.
- **Backend logic:** if Firebase `status` is missing, we derive it: `<20% = Low`, `>80% = Critical`, else `Normal`.

---

## Slide 15 — Authentication & authorization

- Laravel Sanctum for API tokens.
- **Spatie Permission** + **Filament Shield** for role-based access control.
- Roles: `super_admin`, `admin`, `moderator`, `user`, `panel_user`.
- Each Filament page uses `HasPageShield` trait → permissions auto-generated.
- `super_admin` defined via `Gate::before` so it has unrestricted access.
- Users list shows a **Role** column + role filter; the user form includes a multi-select role picker.

**Speaker script:**
Security is handled with Spatie's permission package plus Filament Shield. Every page and every resource gets its own permission string, and roles are just groupings of those permissions. Super admins bypass all checks thanks to a Gate::before hook. This means only someone with the fire_alarm permission can see or interact with the Fire Alarm page — which makes the platform suitable for real municipal use where different departments have different responsibilities.

---

## Slide 16 — User-facing balance & reservations

- Each `User` has a `balance` column (money they've loaded).
- Reserving a slot checks the balance up-front; releasing the slot deducts actual-duration cost.
- `UserResource` in the admin panel lets staff top-up balances, assign roles, and verify emails.
- `ParkingReservationRelationManager` surfaces each user's reservation history inline.

---

## Slide 17 — Firebase service layer

- Single `App\Services\FirebaseService` class wraps the **Firebase REST API**.
  - `get($path)` — HTTP GET on `<database_url>/<path>.json`.
  - `set($path, $value)` — HTTP PUT with an explicit JSON body.
  - `syncToDatabase()` — pulls tank/farm/parking snapshots and writes them to MySQL.
  - `syncParkingToFirebase()` — pushes the DB slot state back so an IoT display knows which slot has a reservation.
- Credentials live in a **settings record**, not `.env`, so ops can update them from the Filament page `/admin/manage-firebase-settings` without a redeploy.

---

## Slide 18 — Background jobs & scheduler

- `App\Jobs\SyncFirebaseData` — wraps `FirebaseService::syncToDatabase()`.
- Registered in `routes/console.php`:
  ```php
  Schedule::job(new SyncFirebaseData)->everySecond();
  ```
- Triggered by Laravel's `schedule:work` / `schedule:run` every second.
- Result: the dashboard is fast (reads from MySQL) while the live pages remain real-time (read from Firebase directly).

---

## Slide 19 — Console commands we added

- `php artisan parking:trim --max=4` — reduces a deployed site's slot count safely, skipping slots with active reservations.
- `php artisan shield:super-admin` (Filament Shield built-in) — bootstraps the first super_admin.
- `php artisan app:sync-super-admin-permissions` — ensures `super_admin` has every permission assigned when the role is *not* using the Gate hook.

---

## Slide 20 — Feature highlights summary

- **Six IoT subsystems** in one app, each with its own purpose-built page.
- **Two-way live sync** with Firebase — observe and control.
- **Role-based permissions** on every page, resource, and widget.
- **Historical analytics** via background job → MySQL time-series.
- **Custom dashboard** with quick-action buttons and an at-a-glance System Alerts widget.
- **Public landing + about page**, QR code in the navbar linking straight to the project description.
- **Settings UI** for Firebase credentials — no server access needed.
- **Responsive / dark-mode** admin UI out of the box.

---

## Slide 21 — Technical challenges we solved

1. **Pump button race condition** — Livewire polling was overwriting the operator's command. We separated "truth" (admin click) from "observation" (poll refresh) and only write-then-flip.
2. **Filament CSS missing Tailwind utilities** — our initial QR modal used utility classes that Filament's own CSS didn't compile. We rewrote the modal with plain CSS so it works regardless of theme.
3. **`wire:loading` firing on every poll** — scoped the loading states with `wire:target="togglePump"` so the button doesn't flicker every 3 s.
4. **Firebase REST PUT** — Laravel's `Http::put()` needed explicit JSON encoding (`withBody(json_encode(...), 'application/json')`) to reliably send a boolean value.
5. **Super admin seeing nothing** — Filament Shield's `define_via_gate` was off; turning it on lets the role bypass all gate checks.

---

## Slide 22 — Limitations & future work

- Firebase is fine for this demo scale but an enterprise deployment would want an MQTT broker + time-series DB.
- The traffic simulation is single-intersection; scaling to a grid means a coordination layer (wave/green-wave algorithm).
- Authentication for external apps uses API keys; a production version would add OAuth2 / per-device certificates.
- Reservation payment is balance-based; integrating a real payment gateway (Stripe/Paymob) is a natural next step.
- Historical analytics could power ML: predicting traffic congestion, water consumption patterns, farm yield.

---

## Slide 23 — Live Demo

- Landing page → click a card → admin panel.
- Dashboard tour (widgets, quick-actions).
- Trigger a fire event from Firebase → watch Fire Alarm page animate.
- Toggle a street light → confirm ESP32 responds.
- Show the QR code → scan → About page.

---

## Slide 24 — Thank you · Q&A

- Repo: (your repo link)
- Demo: (your demo URL)
- Team: (names)
- Supervisor: (name)

---

## Appendix A — File map (one-liner to impress)

```
app/
  Filament/
    Pages/                Dashboard + 6 IoT pages + Firebase settings
    Resources/            Users, Roles, ParkingSlots (with RelationManagers)
    Widgets/              10 widgets (stats, charts, tables, alerts)
  Models/                 User, ParkingSlot, ParkingReservation, SmartTankData, SmartFarmData
  Services/FirebaseService.php     <- wraps Firebase REST
  Jobs/SyncFirebaseData.php        <- scheduled every second
  Console/Commands/
    TrimParkingSlots.php           <- php artisan parking:trim
    SyncSuperAdminPermissions.php
  Settings/FirebaseSettings.php    <- editable from /admin
resources/views/
  welcome.blade.php                <- landing
  about.blade.php                  <- QR destination
  filament/pages/                  <- 6 custom IoT views
  filament/navbar/qr-code.blade.php
routes/
  web.php                          <- /, /about
  api.php                          <- /api/webhooks/firebase-sync
  console.php                      <- schedule
```

---

## Appendix B — All Firebase paths at a glance

| Feature         | Path                     | Direction                 |
|-----------------|--------------------------|---------------------------|
| Smart Farm      | `smart_farm/sensors/*`   | read + write (pump)       |
| Smart Parking   | `smart-parking/{area-n}` | read (sensor) + write (DB) |
| Smart Traffic   | `smart-traffic/*`        | read + write              |
| Smart Lighting  | `smart-lighting/*`       | read + write              |
| Fire Alarm      | `fire-alarm/*`           | read + write (pump)       |
| Smart Tank      | `smart-tank/*`           | read + write (pump)       |
