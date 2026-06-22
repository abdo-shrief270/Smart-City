<?php

namespace App\Filament\Widgets;

use App\Services\FirebaseService;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Illuminate\Support\Facades\Auth;

class SystemAlertsWidget extends BaseWidget
{
    protected static ?int $sort = 2;

    protected ?string $heading = '🚨 Live System Alerts';

    protected ?string $pollingInterval = '10s';

    public static function canView(): bool
    {
        $user = Auth::user();
        if (! $user) {
            return false;
        }

        return $user->can('view_fire_alarm')
            || $user->can('view_smart_tank')
            || $user->can('view_smart_farm');
    }

    protected function getStats(): array
    {
        $stats = [];
        $firebase = app(FirebaseService::class);
        $user = Auth::user();

        if ($user->can('view_fire_alarm')) {
            $emergency = $firebase->get('SmartEmergency') ?? [];

            $fireDetected = (bool) ($emergency['Fire'] ?? 0);
            $stats[] = Stat::make('Fire Status', $fireDetected ? 'FIRE DETECTED' : 'All Clear')
                ->description($fireDetected ? 'Take action immediately' : 'Sensors nominal')
                ->descriptionIcon($fireDetected ? 'heroicon-m-fire' : 'heroicon-m-shield-check')
                ->color($fireDetected ? 'danger' : 'success');

            $smokeDetected = (bool) ($emergency['Smoke'] ?? 0);
            $stats[] = Stat::make('Smoke / Gas', $smokeDetected ? 'Detected' : 'Clear')
                ->description($smokeDetected ? 'Leak detected' : 'Within safe range')
                ->descriptionIcon($smokeDetected ? 'heroicon-m-exclamation-triangle' : 'heroicon-m-check-circle')
                ->color($smokeDetected ? 'danger' : 'success');
        }

        if ($user->can('view_smart_tank')) {
            $tank = $firebase->get('SmartTank') ?? [];
            $level = (int) ($tank['Level'] ?? 0);

            $color = 'success';
            $note = 'Normal level';
            if ($level >= 90) {
                $color = 'danger';
                $note = 'Critically high';
            } elseif ($level <= 15) {
                $color = 'warning';
                $note = 'Low — refill soon';
            }

            $stats[] = Stat::make('Water Tank', $level . '%')
                ->description($note)
                ->descriptionIcon('heroicon-m-beaker')
                ->color($color);
        }

        if ($user->can('view_smart_farm')) {
            $farm = $firebase->get('SmartFarm') ?? [];
            $temp = (int) ($farm['Temp'] ?? 0);
            $soil = (int) ($farm['Soil'] ?? 0);
            $rain = (bool) ($farm['Rain'] ?? 0);

            $stats[] = Stat::make('Farm Temperature', (string) $temp)
                ->description('Soil: ' . $soil . ($rain ? ' · 🌧️ Rain' : ''))
                ->descriptionIcon('heroicon-m-sun')
                ->color('warning');
        }

        return $stats;
    }
}
