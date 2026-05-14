<?php

namespace App\Filament\Pages;

use App\Services\FirebaseService;
use Filament\Notifications\Notification;
use Filament\Pages\Page;
use BezhanSalleh\FilamentShield\Traits\HasPageShield;
use Filament\Actions\Concerns\InteractsWithActions;

class SmartTraffic extends Page
{
    use HasPageShield;
    use InteractsWithActions;

    protected string $view = 'filament.pages.smart-traffic';

    public static function getNavigationIcon(): ?string
    {
        return 'heroicon-o-stop';
    }

    public static function getNavigationLabel(): string
    {
        return 'Smart Traffic';
    }

    public static function getNavigationGroup(): ?string
    {
        return 'IoT Devices';
    }

    public static function getNavigationSort(): ?int
    {
        return 5;
    }

    public const DIRECTIONS = ['north', 'south', 'east', 'west'];
    public const COLORS = ['red', 'yellow', 'green'];

    public string $mode = 'manual'; // 'manual' or 'auto'
    public int $greenTimer = 30;

    /** Per-light state: 'north'|'south'|'east'|'west' => 'red'|'yellow'|'green' */
    public array $lights = [
        'north' => 'red',
        'south' => 'red',
        'east'  => 'red',
        'west'  => 'red',
    ];

    // Auto-mode UI helpers
    public int $timeLeft = 0;
    public int $nextSwitchTime = 0;

    public function mount(): void
    {
        $this->pollData();
    }

    public function pollData(): void
    {
        $firebase = app(FirebaseService::class);
        $data = $firebase->get('smart-traffic');

        if (!$data) {
            return;
        }

        $this->mode = $data['mode'] ?? 'manual';
        $this->greenTimer = (int) ($data['greenTimer'] ?? 30);
        $now = time();
        $isYellow = false;

        if ($this->mode === 'auto') {
            // Cycle locally so the page works without hardware; the per-light
            // values are mirrored to Firebase for the ESP32 to consume.
            $nextSwitch = (int) ($data['nextSwitchTime'] ?? 0);
            $direction = $data['direction'] ?? 'ns_green';

            if ($nextSwitch === 0 || $nextSwitch < $now - 10) {
                $nextSwitch = $now + $this->greenTimer;
                $firebase->set('smart-traffic/nextSwitchTime', $nextSwitch);
            }

            if ($now >= $nextSwitch) {
                $direction = $direction === 'ns_green' ? 'ew_green' : 'ns_green';
                $nextSwitch = $now + $this->greenTimer;
                $firebase->set('smart-traffic/direction', $direction);
                $firebase->set('smart-traffic/nextSwitchTime', $nextSwitch);
                $this->writeLightsForDirection($firebase, $direction);
            }

            $this->nextSwitchTime = $nextSwitch;
            $this->timeLeft = max(0, $nextSwitch - $now);
            $isYellow = $this->timeLeft <= 3 && $this->timeLeft > 0;
        } else {
            $this->timeLeft = 0;
            $this->nextSwitchTime = 0;
        }

        // Source of truth for what the UI shows: per-light keys in Firebase.
        $lightsData = $data['lights'] ?? [];
        foreach (self::DIRECTIONS as $dir) {
            $state = $lightsData[$dir] ?? 'red';
            // Overlay yellow flash during the auto-mode transition window.
            if ($isYellow && $state === 'green') {
                $state = 'yellow';
            }
            $this->lights[$dir] = $state;
        }
    }

    private function writeLightsForDirection(FirebaseService $firebase, string $direction): void
    {
        $pairs = $direction === 'ns_green'
            ? ['north' => 'green', 'south' => 'green', 'east' => 'red', 'west' => 'red']
            : ['north' => 'red',   'south' => 'red',   'east' => 'green', 'west' => 'green'];

        foreach ($pairs as $dir => $color) {
            $firebase->set("smart-traffic/lights/{$dir}", $color);
        }
    }

    public function toggleMode(): void
    {
        $this->pollData();
        $this->mode = $this->mode === 'manual' ? 'auto' : 'manual';

        $firebase = app(FirebaseService::class);
        $firebase->set('smart-traffic/mode', $this->mode);

        if ($this->mode === 'auto') {
            $firebase->set('smart-traffic/nextSwitchTime', time() + $this->greenTimer);
        }

        Notification::make()
            ->title('Mode switched to ' . ucfirst($this->mode))
            ->success()
            ->send();

        $this->pollData();
    }

    public function saveTimers(): void
    {
        if ($this->greenTimer < 5) {
            Notification::make()->title('Timer must be at least 5 seconds')->warning()->send();
            return;
        }

        $firebase = app(FirebaseService::class);
        $firebase->set('smart-traffic/greenTimer', (int) $this->greenTimer);

        if ($this->mode === 'auto') {
            $firebase->set('smart-traffic/nextSwitchTime', time() + $this->greenTimer);
        }

        Notification::make()->title('Timer saved successfully')->success()->send();
        $this->pollData();
    }

    public function setLight(string $dir, string $color): void
    {
        if ($this->mode === 'auto') {
            Notification::make()->title('Cannot control lights manually in Auto mode')->warning()->send();
            return;
        }

        if (!in_array($dir, self::DIRECTIONS, true) || !in_array($color, self::COLORS, true)) {
            return;
        }

        $firebase = app(FirebaseService::class);
        $firebase->set("smart-traffic/lights/{$dir}", $color);
        $this->lights[$dir] = $color;
    }
}
