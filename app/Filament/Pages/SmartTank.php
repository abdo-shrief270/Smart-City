<?php

namespace App\Filament\Pages;

use App\Services\FirebaseService;
use App\Models\SmartTankData;
use Filament\Notifications\Notification;
use Filament\Pages\Page;
use BezhanSalleh\FilamentShield\Traits\HasPageShield;

class SmartTank extends Page
{
    use HasPageShield;
    protected string $view = 'filament.pages.smart-tank';

    public static function getNavigationIcon(): ?string
    {
        return 'heroicon-o-beaker';
    }

    public static function getNavigationLabel(): string
    {
        return 'Water Tank';
    }

    public static function getNavigationGroup(): ?string
    {
        return 'IoT Devices';
    }

    public static function getNavigationSort(): ?int
    {
        return 1;
    }

    public int $level = 0;
    public string $status = 'Normal';
    public bool $isPumpOn = false;
    public array $history = [];
    public string $lastUpdated = '-';

    public function mount(): void
    {
        $this->fetchData();
    }

    public function fetchData(): void
    {
        // Fetch data from local DB (synced via Job)
        $data = SmartTankData::latest()->first();

        if ($data) {
            $this->level = $data->level;
            $this->status = $data->status;
            $this->isPumpOn = $data->is_pump_on;
        } else {
            // Fallback if DB is empty (e.g. before first sync)
            $this->level = 0;
            $this->status = 'Waiting for Sync...';
        }

        // Fetch history data for the chart from DB
        $historyData = SmartTankData::latest()
            ->take(20)
            ->get()
            ->pluck('level')
            ->reverse()
            ->values()
            ->toArray();

        $this->history = !empty($historyData) ? $historyData : array_fill(0, 10, 0);

        $this->lastUpdated = now()->toTimeString();
    }

    public function togglePump(): void
    {
        $this->isPumpOn = !$this->isPumpOn;

        // Optimistic update to Firebase
        // The SyncJob will eventually pull this back to DB
        // But for now, we just update the UI state
        // In a real app, you would inject FirebaseService here and call:
        // $firebase->set('smart-tank/isPumpOn', $this->isPumpOn);

        // Simulating the Firebase call for this demo context where we might not have the service injected in this method signature easily without DI resolution adjustment or using app()
        $firebase = app(FirebaseService::class);
        $firebase->set('smart-tank/isPumpOn', $this->isPumpOn);

        Notification::make()
            ->title($this->isPumpOn ? 'Pump Activated' : 'Pump Deactivated')
            ->success()
            ->send();
    }
}
