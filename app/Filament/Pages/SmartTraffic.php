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

    public const DIRECTIONS = ['north', 'south', 'east', 'west'];
    public const COLORS = ['red', 'yellow', 'green'];

    public string $mode = 'manual';

    /** Per-light state: 'north'|'south'|'east'|'west' => 'red'|'yellow'|'green' */
    public array $lights = [
        'north' => 'red',
        'south' => 'red',
        'east'  => 'red',
        'west'  => 'red',
    ];

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

        $lightsData = $data['lights'] ?? [];
        foreach (self::DIRECTIONS as $dir) {
            $this->lights[$dir] = $lightsData[$dir] ?? 'red';
        }
    }

    public function toggleMode(): void
    {
        $this->pollData();
        $this->mode = $this->mode === 'manual' ? 'auto' : 'manual';

        app(FirebaseService::class)->set('smart-traffic/mode', $this->mode);

        Notification::make()
            ->title('Mode switched to ' . ucfirst($this->mode))
            ->success()
            ->send();

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
