<?php

namespace App\Services;

use App\Settings\FirebaseSettings;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;

class FirebaseService
{
    protected FirebaseSettings $settings;

    /**
     * How long a cached node stays valid. The background SyncFirebaseData job
     * refreshes every second, so this only needs to outlive a few skipped
     * cycles — long enough that page polls (every 2–3s) always hit warm cache.
     */
    protected const CACHE_TTL = 10;

    public function __construct(FirebaseSettings $settings)
    {
        $this->settings = $settings;
    }

    protected function cacheKey(string $path): string
    {
        return 'firebase:' . trim($path, '/');
    }

    /**
     * Get data from Firebase Realtime Database.
     *
     * Reads are served from cache so they never block a web request on a
     * Firebase round-trip. The cache is kept fresh by the background sync job
     * (which calls this with $fresh = true). On a cache miss we fetch once and
     * cache the result; on a network error we fall back to the last good value.
     *
     * @param string $path  Path to the data node
     * @param bool   $fresh Force a live fetch (used by the background warmer)
     * @return mixed
     */
    public function get(string $path, bool $fresh = false)
    {
        if (empty($this->settings->database_url)) {
            return null;
        }

        $cacheKey = $this->cacheKey($path);

        if (! $fresh) {
            $cached = Cache::get($cacheKey);
            if ($cached !== null) {
                return $cached;
            }
        }

        $url = rtrim($this->settings->database_url, '/') . '/' . ltrim($path, '/') . '.json';

        // Append auth param if needed, but usually for read-only public data or
        // using a service account is better. For simplicity with API Key:
        if (!empty($this->settings->api_key)) {
            $url .= '?auth=' . $this->settings->api_key; // Note: This might need proper Auth token exchange
        }

        try {
            // Fail fast: a slow/unreachable Firebase must not hang the request.
            $response = Http::connectTimeout(2)->timeout(4)->get($url);
            $data = $response->json();
            Cache::put($cacheKey, $data, self::CACHE_TTL);

            return $data;
        } catch (\Exception $e) {
            // Serve the last known value rather than nothing.
            return Cache::get($cacheKey);
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
            $response = Http::connectTimeout(2)->timeout(4)
                ->withBody(json_encode($value), 'application/json')
                ->put($url);

            if ($response->successful()) {
                // Invalidate the written path and its root node so the next read
                // reflects the change instead of serving a stale cached value.
                Cache::forget($this->cacheKey($path));
                $root = explode('/', trim($path, '/'))[0];
                Cache::forget($this->cacheKey($root));
            }

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

        // Warm the cache with one fresh read per live node. Pages and widgets
        // then read these from cache instead of each making their own blocking
        // Firebase HTTP call inside a web request.
        $nodes = [
            'SmartTank', 'SmartFarm', 'SmartLighting',
            'SmartParking', 'SmartTraffic', 'SmartEmergency',
        ];
        $live = [];
        foreach ($nodes as $node) {
            $live[$node] = $this->get($node, fresh: true);
        }

        // 1. Smart Tank Sync  (schema: SmartTank/Level, SmartTank/Pump)
        $tankData = $live['SmartTank'];
        if (is_array($tankData)) {
            $level = (int) ($tankData['Level'] ?? 0);

            // Status is derived locally — the device only reports the raw level.
            if ($level < 20) {
                $status = 'Low';
            } elseif ($level > 80) {
                $status = 'Critical';
            } else {
                $status = 'Normal';
            }

            \App\Models\SmartTankData::create([
                'level' => $level,
                'status' => $status,
                'is_pump_on' => (bool) ($tankData['Pump'] ?? 0),
            ]);
        }

        // 2. Smart Farm Sync  (schema: SmartFarm/Temp, Soil, Rain, Pump)
        // Soil moisture is stored in the existing `humidity` column.
        $farmData = $live['SmartFarm'];
        if (is_array($farmData)) {
            \App\Models\SmartFarmData::create([
                'temp' => (int) ($farmData['Temp'] ?? 0),
                'humidity' => (int) ($farmData['Soil'] ?? 0),
                'is_pump_on' => (bool) ($farmData['Pump'] ?? 0),
            ]);
        }

        // Smart Parking is now reported by the IoT device as aggregate counts
        // (SmartParking/FreeSlots, SmartParking/OccupiedSlots) and is read live
        // on the page — there is no per-slot data to sync into the DB anymore.
    }
}
