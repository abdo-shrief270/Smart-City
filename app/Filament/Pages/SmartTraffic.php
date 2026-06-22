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

    public static function getNavigationSort(): ?int
    {
        return 5;
    }

    public const ROADS = ['A', 'B'];

    /** Which road currently has the green light: 'A' or 'B'. */
    public string $light = 'A';

    /** Vehicle counts per road. */
    public int $roadA = 0;
    public int $roadB = 0;

    public function mount(): void
    {
        $this->pollData();
    }

    public function pollData(): void
    {
        $data = app(FirebaseService::class)->get('SmartTraffic', fresh: true);

        if (! is_array($data)) {
            return;
        }

        $light = strtoupper((string) ($data['Light'] ?? 'A'));
        $this->light = in_array($light, self::ROADS, true) ? $light : 'A';
        $this->roadA = (int) ($data['RoadA'] ?? 0);
        $this->roadB = (int) ($data['RoadB'] ?? 0);
    }

    public function setActive(string $road): void
    {
        $road = strtoupper($road);

        if (! in_array($road, self::ROADS, true)) {
            return;
        }

        app(FirebaseService::class)->set('SmartTraffic/Light', $road);
        $this->light = $road;

        Notification::make()
            ->title("Green light given to Road {$road}")
            ->success()
            ->send();
    }
}
