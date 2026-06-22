<?php

namespace App\Filament\Pages;

use App\Services\FirebaseService;
use Filament\Notifications\Notification;
use Filament\Pages\Page;
use BezhanSalleh\FilamentShield\Traits\HasPageShield;

class SmartFarm extends Page
{
    use HasPageShield;
    protected string $view = 'filament.pages.smart-farm';

    public static function getNavigationIcon(): ?string
    {
        return 'heroicon-o-sun';
    }

    public static function getNavigationLabel(): string
    {
        return 'Smart Farm';
    }

    public static function getNavigationSort(): ?int
    {
        return 2;
    }

    public int $temp = 0;     // raw temperature sensor reading
    public int $soil = 0;     // raw soil-moisture sensor reading
    public bool $rain = false; // rain detected
    public bool $pump = false; // irrigation pump

    public function mount(): void
    {
        $this->fetchData();
    }

    public function fetchData(): void
    {
        // Read live sensor values from Firebase (schema: SmartFarm/*)
        $data = app(FirebaseService::class)->get('SmartFarm', fresh: true);

        if (is_array($data)) {
            $this->temp = (int) ($data['Temp'] ?? 0);
            $this->soil = (int) ($data['Soil'] ?? 0);
            $this->rain = (bool) ($data['Rain'] ?? 0);
            $this->pump = (bool) ($data['Pump'] ?? 0);
        }
    }

    public function togglePump(): void
    {
        $newStatus = !$this->pump;

        // Optimistic update
        $this->pump = $newStatus;

        // Write pump state to the device (0/1 to match the schema).
        app(FirebaseService::class)->set('SmartFarm/Pump', $newStatus ? 1 : 0);

        Notification::make()
            ->title('Pump ' . ($newStatus ? 'Started' : 'Stopped'))
            ->success()
            ->send();
    }
}
