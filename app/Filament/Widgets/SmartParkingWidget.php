<?php

namespace App\Filament\Widgets;

use App\Models\ParkingReservation;
use App\Services\FirebaseService;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Illuminate\Support\Facades\Auth;

class SmartParkingWidget extends BaseWidget
{
    protected static ?int $sort = 4;

    protected ?string $pollingInterval = '30s';

    public static function canView(): bool
    {
        return Auth::user()->can('page_SmartParking');
    }

    protected function getStats(): array
    {
        // Live aggregate counts from the IoT parking sensor.
        $sensor = app(FirebaseService::class)->get('SmartParking') ?? [];
        $availableSlots = (int) ($sensor['FreeSlots'] ?? 0);
        $occupiedSlots = (int) ($sensor['OccupiedSlots'] ?? 0);
        $totalSlots = $availableSlots + $occupiedSlots;

        $stats = [
            Stat::make('🅿️ Total Slots', $totalSlots)
                ->description('Parking capacity')
                ->color('primary'),

            Stat::make('✅ Available', $availableSlots)
                ->description(round(($availableSlots / max($totalSlots, 1)) * 100) . '% free')
                ->color('success')
                ->chart([8, 6, 5, 7, 4, 6, $availableSlots]),

            Stat::make('🚗 Occupied', $occupiedSlots)
                ->description('Currently parked')
                ->color('danger'),
        ];

        // Add revenue stat if user has permission
        if (Auth::user()->can('view_smart_parking_revenue')) {
            $todayRevenue = ParkingReservation::whereDate('created_at', today())
                ->whereIn('status', ['completed', 'active'])
                ->sum('total_cost');

            $stats[] = Stat::make('💰 Revenue', '$' . number_format($todayRevenue, 2))
                ->description("Today's earnings")
                ->color('warning')
                ->chart([50, 60, 55, 70, 65, 80, $todayRevenue]);
        }

        return $stats;
    }

    protected function getHeading(): ?string
    {
        return '🚗 Smart Parking';
    }
}
