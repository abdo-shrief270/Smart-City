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

    public static function getNavigationGroup(): ?string
    {
        return 'IoT Devices';
    }

    public static function getNavigationSort(): ?int
    {
        return 5;
    }

    public string $mode = 'manual'; // 'manual' or 'auto'
    
    // unified timer for Auto mode
    public int $greenTimer = 30; // Green Timer for all directions
    
    // 'ns_green' means North/South is Green, East/West is Red.
    // 'ew_green' means East/West is Green, North/South is Red.
    public string $direction = 'ns_green';

    // Frontend state
    public int $timeLeft = 0;
    public int $nextSwitchTime = 0;
    public bool $isYellow = false;

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
        $this->greenTimer = (int) ($data['greenTimer'] ?? 30);
        $this->direction = $data['direction'] ?? 'ns_green';
        
        $now = time();

        if ($this->mode === 'auto') {
            $nextSwitch = (int) ($data['nextSwitchTime'] ?? 0);
            
            // Initialize or fix stuck timer
            if ($nextSwitch === 0 || $nextSwitch < $now - 10) {
                $nextSwitch = $now + $this->greenTimer;
                $firebase->set('smart-traffic/nextSwitchTime', $nextSwitch);
            }
            
            if ($now >= $nextSwitch) {
                // Time to switch
                $this->direction = $this->direction === 'ns_green' ? 'ew_green' : 'ns_green';
                $nextSwitch = $now + $this->greenTimer;
                
                $firebase->set('smart-traffic/direction', $this->direction);
                $firebase->set('smart-traffic/nextSwitchTime', $nextSwitch);
            }
            
            $this->timeLeft = max(0, $nextSwitch - $now);
            $this->nextSwitchTime = $nextSwitch;
            $this->isYellow = ($this->timeLeft <= 3 && $this->timeLeft > 0);
        } else {
            // Manual mode
            $pendingDirection = $data['pendingDirection'] ?? null;
            $transitionUntil = (int) ($data['transitionUntil'] ?? 0);
            
            if ($pendingDirection && $now >= $transitionUntil) {
                // Finish manual transition
                $this->direction = $pendingDirection;
                $firebase->set('smart-traffic/direction', $this->direction);
                $firebase->set('smart-traffic/pendingDirection', null);
                $firebase->set('smart-traffic/transitionUntil', 0);
                
                $this->isYellow = false;
            } elseif ($pendingDirection && $now < $transitionUntil) {
                // Currently in yellow phase
                $this->isYellow = true;
            } else {
                // Normal manual state
                $this->isYellow = false;
            }
            
            $this->timeLeft = 0;
        }
    }

    public function toggleMode(): void
    {
        $this->pollData(); // sync first
        $this->mode = $this->mode === 'manual' ? 'auto' : 'manual';
        
        $firebase = app(FirebaseService::class);
        $firebase->set('smart-traffic/mode', $this->mode);

        if ($this->mode === 'auto') {
            $firebase->set('smart-traffic/nextSwitchTime', time() + $this->greenTimer);
            $firebase->set('smart-traffic/pendingDirection', null);
            $firebase->set('smart-traffic/transitionUntil', 0);
        } else {
            $firebase->set('smart-traffic/pendingDirection', null);
            $firebase->set('smart-traffic/transitionUntil', 0);
        }

        Notification::make()
            ->title('Mode switched to ' . ucfirst($this->mode))
            ->success()
            ->send();
            
        $this->pollData();
    }

    public function saveTimers(): void
    {
        if ($this->greenTimer < 5) {
            Notification::make()->title('Timer must be at least 5 seconds')->warning()->send();
            return;
        }

        $firebase = app(FirebaseService::class);
        $firebase->set('smart-traffic/greenTimer', (int) $this->greenTimer);
        
        if ($this->mode === 'auto') {
            $firebase->set('smart-traffic/nextSwitchTime', time() + $this->greenTimer);
        }

        Notification::make()->title('Timer saved successfully')->success()->send();
        $this->pollData();
    }

    public function setDirection(string $direction): void
    {
        if ($this->mode === 'auto') {
            Notification::make()->title('Cannot change lights manually in Auto mode')->warning()->send();
            return;
        }

        if (in_array($direction, ['ns_green', 'ew_green']) && $this->direction !== $direction) {
            $firebase = app(FirebaseService::class);
            // Trigger yellow phase for 2 seconds
            $firebase->set('smart-traffic/pendingDirection', $direction);
            $firebase->set('smart-traffic/transitionUntil', time() + 2);
            
            Notification::make()->title("Transitioning lights...")->success()->send();
            $this->pollData();
        }
    }
}
