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

    public static function getNavigationGroup(): ?string
    {
        return 'IoT Devices';
    }

    public static function getNavigationSort(): ?int
    {
        return 6;
    }

    // Sensor data
    public int|float $flameValue = 0;     // raw ADC value from sensor (0-4095)
    public bool $fireDetected = false;    // true = fire detected
    public bool $pumpActive = false;      // true = pump is running

    public function mount(): void
    {
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
        $this->pumpActive   = (bool) ($data['pumpActive']   ?? false);
    }

    public function togglePump(): void
    {
        $this->pumpActive = !$this->pumpActive;

        $firebase = app(FirebaseService::class);
        $firebase->set('fire-alarm/pumpActive', $this->pumpActive);

        Notification::make()
            ->title('Pump ' . ($this->pumpActive ? 'Activated' : 'Deactivated'))
            ->color($this->pumpActive ? 'success' : 'warning')
            ->send();
    }
}
