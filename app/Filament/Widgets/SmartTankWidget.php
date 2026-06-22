<?php

namespace App\Filament\Widgets;

use App\Services\FirebaseService;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Illuminate\Support\Facades\Auth;

class SmartTankWidget extends BaseWidget
{
    protected static ?int $sort = 3;

    protected ?string $pollingInterval = '10s';

    public static function canView(): bool
    {
        return Auth::user()->can('page_SmartTank');
    }

    protected function getStats(): array
    {
        $firebaseService = app(FirebaseService::class);
        $tankData = $firebaseService->get('SmartTank') ?? [];

        $level = (int) ($tankData['Level'] ?? 0);
        $pump = (bool) ($tankData['Pump'] ?? 0);

        // Determine status based on level
        $status = match (true) {
            $level >= 50 => 'Normal',
            $level >= 20 => 'Low',
            default => 'Critical',
        };

        return [
            Stat::make('🌊 Water Level', $level . '%')
                ->description($level * 10 . ' Liters')
                ->color($level >= 50 ? 'success' : ($level >= 20 ? 'warning' : 'danger'))
                ->chart([70, 68, 65, 60, 55, 52, $level]),

            Stat::make('📊 Status', $status)
                ->description('Tank condition')
                ->color($status === 'Normal' ? 'success' : ($status === 'Low' ? 'warning' : 'danger')),

            Stat::make('⚡ Pump', $pump ? 'ON' : 'OFF')
                ->description($pump ? 'Filling tank' : 'Pump idle')
                ->color($pump ? 'info' : 'gray'),
        ];
    }

    protected function getHeading(): ?string
    {
        return '🚰 Smart Tank';
    }
}
