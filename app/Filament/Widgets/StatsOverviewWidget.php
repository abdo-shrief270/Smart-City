<?php

namespace App\Filament\Widgets;

use App\Models\ParkingReservation;
use App\Models\ParkingSlot;
use App\Models\User;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Illuminate\Support\Facades\Auth;

class StatsOverviewWidget extends BaseWidget
{
    protected static ?int $sort = 1;

    protected function getStats(): array
    {
        $stats = [];

        // Total Users - visible to admins only
        if (Auth::user()->can('view_any_user')) {
            $stats[] = Stat::make('Total Users', User::count())
                ->description('Registered users')
                ->descriptionIcon('heroicon-m-users')
                ->color('primary')
                ->chart([7, 3, 4, 5, 6, 3, 5]);
        }

        // Active Reservations - visible to users with parking permission
        if (Auth::user()->can('view_smart_parking')) {
            $activeReservations = ParkingReservation::where('status', 'active')->count();
            $stats[] = Stat::make('Active Reservations', $activeReservations)
                ->description('Currently parked')
                ->descriptionIcon('heroicon-m-truck')
                ->color('info');
        }

        // Parking Slots Overview - visible to users with parking permission
        if (Auth::user()->can('view_smart_parking')) {
            $totalSlots = ParkingSlot::count();
            $availableSlots = ParkingSlot::where('status', 'available')->count();

            $stats[] = Stat::make('Available Slots', $availableSlots . ' / ' . $totalSlots)
                ->description('Parking availability')
                ->descriptionIcon('heroicon-m-check-circle')
                ->color($availableSlots > 0 ? 'success' : 'danger');
        }

        // Today's Revenue - visible only to users with revenue permission
        if (Auth::user()->can('view_smart_parking_revenue')) {
            $todayRevenue = ParkingReservation::whereDate('created_at', today())
                ->where('status', 'completed')
                ->sum('total_cost');

            $stats[] = Stat::make("Today's Revenue", '$' . number_format($todayRevenue, 2))
                ->description('Parking revenue')
                ->descriptionIcon('heroicon-m-currency-dollar')
                ->color('warning')
                ->chart([15, 20, 18, 25, 22, 30, 28]);
        }

        return $stats;
    }
}
