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
        $farmData = $firebaseService->get('SmartFarm') ?? [];

        $temp = (int) ($farmData['Temp'] ?? 0);
        $soil = (int) ($farmData['Soil'] ?? 0);
        $rain = (bool) ($farmData['Rain'] ?? 0);
        $pump = (bool) ($farmData['Pump'] ?? 0);

        return [
            Stat::make('🌡️ Temperature', (string) $temp)
                ->description('Farm temperature sensor')
                ->color('warning')
                ->chart([20, 22, 24, 23, 25, 24, $temp]),

            Stat::make('🌱 Soil Moisture', (string) $soil)
                ->description($rain ? '🌧️ Rain detected' : 'No rain')
                ->color($rain ? 'info' : 'success')
                ->chart([45, 50, 48, 52, 55, 53, $soil]),

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
