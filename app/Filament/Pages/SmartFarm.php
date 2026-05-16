<?php

namespace App\Filament\Pages;

use App\Services\FirebaseService;
use App\Models\SmartFarmData;
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

    public int $temp = 0;
    public int $humidity = 0;
    public bool $pump = false;

    public function mount(): void
    {
        $this->fetchData();
    }

    public function fetchData(): void
    {
        // Fetch data from local DB
        $data = SmartFarmData::latest()->first();

        if ($data) {
            $this->temp = $data->temp;
            $this->humidity = $data->humidity;
            $this->pump = $data->is_pump_on;
        } else {
            // Fallback
            $this->temp = 0;
            $this->humidity = 0;
            $this->pump = false;
        }
    }

    public function togglePump(): void
    {
        $newStatus = !$this->pump;

        // Optimistic update
        $this->pump = $newStatus;

        // Update Firebase
        // Note: Writing to 'smart_farm/sensors/pump' to match structure
        app(FirebaseService::class)->set('smart_farm/sensors/pump', $newStatus);

        Notification::make()
            ->title('Pump ' . ($newStatus ? 'Started' : 'Stopped'))
            ->success()
            ->send();
    }
}
