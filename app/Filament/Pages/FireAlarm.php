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
        return 'Smart Emergency';
    }

    public static function getNavigationSort(): ?int
    {
        return 6;
    }

    // Sensor states (schema: SmartEmergency/Fire, Smoke, Alarm)
    public bool $fire = false;   // flame detected
    public bool $smoke = false;  // smoke / gas detected
    public bool $alarm = false;  // alarm (buzzer / siren) active

    public function mount(): void
    {
        $this->pollData();
    }

    public function pollData(): void
    {
        $data = app(FirebaseService::class)->get('SmartEmergency', fresh: true);

        if (! is_array($data)) {
            return;
        }

        $this->fire  = (bool) ($data['Fire'] ?? 0);
        $this->smoke = (bool) ($data['Smoke'] ?? 0);
        $this->alarm = (bool) ($data['Alarm'] ?? 0);
    }

    public function toggleAlarm(): void
    {
        $firebase = app(FirebaseService::class);
        $target = ! $this->alarm;

        $ok = $firebase->set('SmartEmergency/Alarm', $target ? 1 : 0);

        if (! $ok) {
            Notification::make()
                ->title('Failed to update alarm')
                ->body('Could not write to Firebase. Check database URL, rules, and API key in Firebase settings.')
                ->danger()
                ->send();
            return;
        }

        $this->alarm = $target;

        Notification::make()
            ->title($this->alarm ? 'Alarm Activated' : 'Alarm Silenced')
            ->color($this->alarm ? 'danger' : 'success')
            ->send();
    }
}
