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
            $fire = $firebase->get('fire-alarm') ?? [];

            $fireDetected = (bool) ($fire['fireDetected'] ?? false);
            $stats[] = Stat::make('Fire Status', $fireDetected ? 'FIRE DETECTED' : 'All Clear')
                ->description($fireDetected ? 'Take action immediately' : 'Sensors nominal')
                ->descriptionIcon($fireDetected ? 'heroicon-m-fire' : 'heroicon-m-shield-check')
                ->color($fireDetected ? 'danger' : 'success');

            $gasValue = (int) ($fire['gasValue'] ?? 0);
            $gasDetected = array_key_exists('gasDetected', $fire)
                ? (bool) $fire['gasDetected']
                : $gasValue >= 2000;

            $stats[] = Stat::make('Gas Sensor', number_format($gasValue) . ' / 4095')
                ->description($gasDetected ? 'Leak detected' : 'Within safe range')
                ->descriptionIcon($gasDetected ? 'heroicon-m-exclamation-triangle' : 'heroicon-m-check-circle')
                ->color($gasDetected ? 'danger' : 'success');
        }

        if ($user->can('view_smart_tank')) {
            $tank = $firebase->get('smart-tank') ?? [];
            $level = (int) ($tank['level'] ?? 0);

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
            $farm = $firebase->get('smart_farm') ?? [];
            $sensors = $farm['sensors'] ?? $farm;
            $temp = (int) ($sensors['temperature'] ?? $sensors['temp'] ?? 0);
            $humidity = (int) ($sensors['humidity'] ?? 0);

            $tempColor = $temp >= 35 ? 'danger' : ($temp >= 30 ? 'warning' : 'success');
            $stats[] = Stat::make('Farm Temperature', $temp . '°C')
                ->description('Humidity: ' . $humidity . '%')
                ->descriptionIcon('heroicon-m-sun')
                ->color($tempColor);
        }

        return $stats;
    }
}
