<?php

namespace App\Filament\Pages;

use App\Services\FirebaseService;
use Filament\Notifications\Notification;
use Filament\Pages\Page;
use BezhanSalleh\FilamentShield\Traits\HasPageShield;
use Filament\Actions\Action;
use Filament\Actions\Concerns\InteractsWithActions;

class SmartLighting extends Page
{
    use HasPageShield;
    use InteractsWithActions;

    protected string $view = 'filament.pages.smart-lighting';

    public static function getNavigationIcon(): ?string
    {
        return 'heroicon-o-light-bulb';
    }

    public static function getNavigationLabel(): string
    {
        return 'Smart Lighting';
    }

    public static function getNavigationGroup(): ?string
    {
        return 'IoT Devices';
    }

    public static function getNavigationSort(): ?int
    {
        return 4;
    }

    public string $mode = 'manual'; // 'manual' or 'auto'
    public array $lights = [];

    public function mount(): void
    {
        // Initialize 8 lights if not set
        $this->lights = array_fill(0, 8, false);
        $this->refreshData();
    }

    public function refreshData(): void
    {
        $firebase = app(FirebaseService::class);
        $data = $firebase->get('smart-lighting');

        if ($data) {
            $this->mode = $data['mode'] ?? 'manual';
            // Ensure we have 8 lights, merging with defaults if needed
            $fetchedLights = $data['lights'] ?? [];
            for ($i = 0; $i < 8; $i++) {
                $this->lights[$i] = $fetchedLights[$i] ?? false;
            }
        }
    }

    public function toggleMode(): void
    {
        $this->mode = $this->mode === 'manual' ? 'auto' : 'manual';
        
        // Sync to Firebase
        $firebase = app(FirebaseService::class);
        $firebase->set('smart-lighting/mode', $this->mode);

        Notification::make()
            ->title('Mode switched to ' . ucfirst($this->mode))
            ->success()
            ->send();
    }

    public function toggleLight(int $index): void
    {
        // Prevent manual control if in auto mode
        if ($this->mode === 'auto') {
            Notification::make()
                ->title('Cannot control lights in Auto mode')
                ->warning()
                ->send();
            return;
        }

        if (isset($this->lights[$index])) {
            $this->lights[$index] = !$this->lights[$index];

            // Sync to Firebase
            $firebase = app(FirebaseService::class);
            $firebase->set("smart-lighting/lights/{$index}", $this->lights[$index]);
            
            Notification::make()
                ->title('Light ' . ($index + 1) . ' turned ' . ($this->lights[$index] ? 'On' : 'Off'))
                ->success()
                ->send();
        }
    }
}
