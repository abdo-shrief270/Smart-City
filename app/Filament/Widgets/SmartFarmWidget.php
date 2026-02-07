<?php

namespace App\Filament\Widgets;

use App\Services\FirebaseService;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Illuminate\Support\Facades\Auth;

class SmartFarmWidget extends BaseWidget
{
    protected static ?int $sort = 2;

    protected ?string $pollingInterval = '10s';

    public static function canView(): bool
    {
        return Auth::user()->can('page_SmartFarm');
    }

    protected function getStats(): array
    {
        $firebaseService = app(FirebaseService::class);
        $farmData = $firebaseService->get('smart_farm');
        $sensors = $farmData['sensors'] ?? $farmData ?? [];

        $temp = $sensors['temperature'] ?? $sensors['temp'] ?? 0;
        $humidity = $sensors['humidity'] ?? 0;
        $pump = $sensors['pump'] ?? $sensors['isPumpOn'] ?? false;

        return [
            Stat::make('🌡️ Temperature', $temp . '°C')
                ->description('Farm temperature')
                ->color($temp > 35 ? 'danger' : ($temp > 25 ? 'warning' : 'success'))
                ->chart([20, 22, 24, 23, 25, 24, $temp]),

            Stat::make('💧 Humidity', $humidity . '%')
                ->description('Soil moisture')
                ->color($humidity < 30 ? 'danger' : ($humidity < 50 ? 'warning' : 'success'))
                ->chart([45, 50, 48, 52, 55, 53, $humidity]),

            Stat::make('⚡ Pump', $pump ? 'ON' : 'OFF')
                ->description($pump ? 'Irrigation active' : 'System idle')
                ->color($pump ? 'info' : 'gray'),
        ];
    }

    protected function getHeading(): ?string
    {
        return '🌾 Smart Farm';
    }
}
