<?php

namespace App\Services;

use App\Settings\FirebaseSettings;
use Illuminate\Support\Facades\Http;

class FirebaseService
{
    protected FirebaseSettings $settings;

    public function __construct(FirebaseSettings $settings)
    {
        $this->settings = $settings;
    }

    /**
     * Get data from Firebase Realtime Database
     * 
     * @param string $path Path to the data node
     * @return mixed
     */
    public function get(string $path)
    {
        if (empty($this->settings->database_url)) {
            return null;
        }

        $url = rtrim($this->settings->database_url, '/') . '/' . ltrim($path, '/') . '.json';

        // Append auth param if needed, but usually for read-only public data or 
        // using a service account is better. For simplicity with API Key:
        if (!empty($this->settings->api_key)) {
            $url .= '?auth=' . $this->settings->api_key; // Note: This might need proper Auth token exchange
        }

        try {
            $response = Http::get($url);
            return $response->json();
        } catch (\Exception $e) {
            return null;
        }
    }
    /**
     * Set data to Firebase Realtime Database
     *
     * @param string $path Path to the data node
     * @param mixed $value Value to set
     * @return bool
     */
    public function set(string $path, $value): bool
    {
        if (empty($this->settings->database_url)) {
            return false;
        }

        $url = rtrim($this->settings->database_url, '/') . '/' . ltrim($path, '/') . '.json';

        if (!empty($this->settings->api_key)) {
            $url .= '?auth=' . $this->settings->api_key;
        }

        try {
            $response = Http::withBody(json_encode($value), 'application/json')->put($url);
            return $response->successful();
        } catch (\Exception $e) {
            return false;
        }
    }

    /**
     * Pull new gate-log entries from Firebase into the gate_logs table.
     * Deduplicated by firebase_key. Returns the number of rows inserted.
     *
     * Expected Firebase shape (each child uses a Firebase push-ID as key):
     * gate-logs/
     *   -NabcXYZ…/
     *     plate     : "ABC-1234"
     *     gate      : 1
     *     direction : "in" | "out"
     *     timestamp : 1712345678      (unix seconds; optional — falls back to now)
     */
    public function syncGateLogsFromFirebase(): int
    {
        $logs = $this->get('gate-logs');

        if (!is_array($logs)) {
            return 0;
        }

        $existingKeys = \App\Models\GateLog::query()
            ->whereNotNull('firebase_key')
            ->pluck('firebase_key')
            ->all();

        $existing = array_flip($existingKeys);
        $inserted = 0;

        foreach ($logs as $key => $entry) {
            if (!is_array($entry) || isset($existing[$key])) {
                continue;
            }

            $plate = trim((string) ($entry['plate'] ?? $entry['plate_number'] ?? ''));
            $gate = (int) ($entry['gate'] ?? $entry['gate_number'] ?? 0);
            $direction = strtolower((string) ($entry['direction'] ?? ''));

            if ($plate === '' || $gate < 1 || !in_array($direction, ['in', 'out'], true)) {
                continue;
            }

            $timestamp = $entry['timestamp'] ?? $entry['logged_at'] ?? null;
            $loggedAt = is_numeric($timestamp)
                ? \Carbon\Carbon::createFromTimestamp((int) $timestamp)
                : (is_string($timestamp) ? \Carbon\Carbon::parse($timestamp) : now());

            \App\Models\GateLog::create([
                'firebase_key' => $key,
                'plate_number' => strtoupper($plate),
                'gate_number'  => $gate,
                'direction'    => $direction,
                'logged_at'    => $loggedAt,
            ]);

            $inserted++;
        }

        return $inserted;
    }

    public function syncToDatabase(): void
    {
        // 0. Gate Logs Sync (car plates at city gates)
        try {
            $this->syncGateLogsFromFirebase();
        } catch (\Throwable $e) {
            // Non-fatal: continue with other sync jobs even if gate-logs fails.
        }

        // 1. Smart Tank Sync
        $tankData = $this->get('smart-tank');
        if ($tankData) {
            $level = (int) ($tankData['level'] ?? 0);

            // Calculate status if missing or arbitrary
            $status = $tankData['status'] ?? null;
            if (!$status || $status === 'Unknown') {
                if ($level < 20)
                    $status = 'Low';
                elseif ($level > 80)
                    $status = 'Critical';
                else
                    $status = 'Normal';
            }

            \App\Models\SmartTankData::create([
                'level' => $level,
                'status' => $status,
                'is_pump_on' => (bool) ($tankData['isPumpOn'] ?? false),
            ]);
        }

        // 2. Smart Farm Sync
        $farmData = $this->get('smart_farm');
        if ($farmData) {
            // Handle nested 'sensors' structure if present
            $sensors = $farmData['sensors'] ?? $farmData;

            \App\Models\SmartFarmData::create([
                'temp' => (int) ($sensors['temperature'] ?? $sensors['temp'] ?? 0),
                'humidity' => (int) ($sensors['humidity'] ?? 0),
                'is_pump_on' => (bool) ($sensors['pump'] ?? $sensors['isPumpOn'] ?? false),
            ]);
        }

        // 3. Smart Parking Sync - Read from Firebase IoT sensors
        $parkingData = $this->get('smart-parking');
        if ($parkingData && is_array($parkingData)) {
            foreach ($parkingData as $slotKey => $slotData) {
                // Skip if not valid slot data
                if (!is_array($slotData))
                    continue;

                // Parse slot key (e.g., "A-1" or "slot_A_1")
                $slotIdentifier = str_replace(['slot_', '_'], ['', '-'], $slotKey);
                $parts = explode('-', $slotIdentifier);

                if (count($parts) >= 2) {
                    $area = strtoupper($parts[0]);
                    $slotNumber = (int) $parts[1];

                    // Find matching slot in DB
                    $slot = \App\Models\ParkingSlot::where('area', $area)
                        ->where('slot_number', $slotNumber)
                        ->first();

                    if ($slot) {
                        // IoT sensor reports occupied (car detected) or available
                        $sensorOccupied = (bool) ($slotData['occupied'] ?? $slotData['isOccupied'] ?? false);

                        // Only update if no active reservation (sensor-driven status)
                        if (!$slot->activeReservation) {
                            $newStatus = $sensorOccupied ? 'occupied' : 'available';
                            if ($slot->status !== $newStatus) {
                                $slot->update(['status' => $newStatus]);
                            }
                        }
                    }
                }
            }
        }
    }

    /**
     * Sync parking slot status to Firebase (for IoT display)
     */
    public function syncParkingToFirebase(): void
    {
        $slots = \App\Models\ParkingSlot::with('activeReservation')->get();

        $parkingData = [];
        foreach ($slots as $slot) {
            $key = $slot->area . '-' . $slot->slot_number;
            $parkingData[$key] = [
                'area' => $slot->area,
                'slot_number' => $slot->slot_number,
                'status' => $slot->status,
                'occupied' => $slot->status !== 'available',
                'has_reservation' => $slot->activeReservation !== null,
                'cost_per_hour' => $slot->cost_per_hour,
                'updated_at' => now()->toIso8601String(),
            ];
        }

        $this->set('smart-parking', $parkingData);
    }

    /**
     * Update single slot status in Firebase
     */
    public function updateSlotInFirebase(\App\Models\ParkingSlot $slot): bool
    {
        $key = $slot->area . '-' . $slot->slot_number;
        return $this->set("smart-parking/{$key}", [
            'area' => $slot->area,
            'slot_number' => $slot->slot_number,
            'status' => $slot->status,
            'occupied' => $slot->status !== 'available',
            'has_reservation' => $slot->activeReservation !== null,
            'cost_per_hour' => $slot->cost_per_hour,
            'updated_at' => now()->toIso8601String(),
        ]);
    }
}
