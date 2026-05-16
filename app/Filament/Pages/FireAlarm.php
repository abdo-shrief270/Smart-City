<?php

namespace App\Filament\Pages;

use App\Services\FirebaseService;
use Filament\Notifications\Notification;
use Filament\Pages\Page;
use BezhanSalleh\FilamentShield\Traits\HasPageShield;

class FireAlarm extends Page
{
    use HasPageShield;

    protected string $view = 'filament.pages.fire-alarm';

    public static function getNavigationIcon(): ?string
    {
        return 'heroicon-o-fire';
    }

    public static function getNavigationLabel(): string
    {
        return 'Fire Alarm';
    }

    public static function getNavigationSort(): ?int
    {
        return 6;
    }

    // Sensor data
    public int|float $flameValue = 0;     // raw ADC value from sensor (0-4095)
    public bool $fireDetected = false;    // true = fire detected
    public bool $pumpActive = false;      // true = pump is running
    public int|float $gasValue = 0;       // raw ADC value from gas sensor (0-4095)
    public bool $gasDetected = false;     // true = gas leak detected
    public int $gasThreshold = 2000;      // alarm threshold for gas sensor

    public function mount(): void
    {
        $firebase = app(FirebaseService::class);
        $data = $firebase->get('fire-alarm');

        $this->pumpActive = (bool) ($data['pumpActive'] ?? false);

        $this->pollData();
    }

    public function pollData(): void
    {
        $firebase = app(FirebaseService::class);
        $data = $firebase->get('fire-alarm');

        if (!$data) {
            return;
        }

        $this->flameValue   = (int) ($data['flameValue']   ?? 0);
        $this->fireDetected = (bool) ($data['fireDetected'] ?? false);
        $this->gasValue     = (int) ($data['gasValue']     ?? 0);
        $this->gasDetected  = array_key_exists('gasDetected', $data ?? [])
            ? (bool) $data['gasDetected']
            : $this->gasValue >= $this->gasThreshold;

        // Note: pumpActive is intentionally NOT synced from polling.
        // The admin button is the source of truth — polling used to race
        // with toggle writes and flip the UI back. Sensor values still refresh.
    }

    public function togglePump(): void
    {
        $firebase = app(FirebaseService::class);
        $target = ! $this->pumpActive;

        $ok = $firebase->set('fire-alarm/pumpActive', $target);

        if (! $ok) {
            Notification::make()
                ->title('Failed to update pump')
                ->body('Could not write to Firebase. Check database URL, rules, and API key in Firebase settings.')
                ->danger()
                ->send();
            return;
        }

        $this->pumpActive = $target;

        Notification::make()
            ->title('Pump ' . ($this->pumpActive ? 'Activated' : 'Deactivated'))
            ->color($this->pumpActive ? 'success' : 'warning')
            ->send();
    }
}
