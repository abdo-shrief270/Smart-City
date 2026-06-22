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
        // Current values come live from Firebase so changes show in real time.
        $live = app(FirebaseService::class)->get('SmartTank', fresh: true);

        if (is_array($live)) {
            $this->level = (int) ($live['Level'] ?? 0);
            $this->isPumpOn = (bool) ($live['Pump'] ?? 0);
            $this->status = $this->level < 20
                ? 'Low'
                : ($this->level > 80 ? 'Critical' : 'Normal');
        } else {
            // Fallback to the last synced DB row if Firebase is unreachable.
            $data = SmartTankData::latest()->first();
            if ($data) {
                $this->level = $data->level;
                $this->status = $data->status;
                $this->isPumpOn = $data->is_pump_on;
            } else {
                $this->level = 0;
                $this->status = 'Waiting for Sync...';
            }
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

        // Write the pump state back to the device (0/1 to match the schema).
        $firebase = app(FirebaseService::class);
        $firebase->set('SmartTank/Pump', $this->isPumpOn ? 1 : 0);

        Notification::make()
            ->title($this->isPumpOn ? 'Pump Activated' : 'Pump Deactivated')
            ->success()
            ->send();
    }
}
