<?php

namespace App\Filament\Pages;

use App\Services\FirebaseService;
use Filament\Notifications\Notification;
use Filament\Pages\Page;
use BezhanSalleh\FilamentShield\Traits\HasPageShield;

class SmartLighting extends Page
{
    use HasPageShield;

    protected string $view = 'filament.pages.smart-lighting';

    public static function getNavigationIcon(): ?string
    {
        return 'heroicon-o-light-bulb';
    }

    public static function getNavigationLabel(): string
    {
        return 'Smart Lighting';
    }

    public static function getNavigationSort(): ?int
    {
        return 4;
    }

    /** Raw ambient-light sensor reading (LDR). */
    public int $ldr = 0;

    /** Street lamp on/off state. */
    public bool $lamp = false;

    public function mount(): void
    {
        $this->refreshData();
    }

    public function refreshData(): void
    {
        $data = app(FirebaseService::class)->get('SmartLighting', fresh: true);

        if (is_array($data)) {
            $this->ldr = (int) ($data['LDR'] ?? 0);
            $this->lamp = (bool) ($data['Lamp'] ?? 0);
        }
    }

    public function toggleLamp(): void
    {
        $this->lamp = ! $this->lamp;

        // Write lamp state to the device (0/1 to match the schema).
        app(FirebaseService::class)->set('SmartLighting/Lamp', $this->lamp ? 1 : 0);

        Notification::make()
            ->title('Lamp turned ' . ($this->lamp ? 'On' : 'Off'))
            ->success()
            ->send();
    }
}
