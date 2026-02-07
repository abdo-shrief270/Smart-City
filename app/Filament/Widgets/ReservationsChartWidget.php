<?php

namespace App\Filament\Widgets;

use App\Models\ParkingReservation;
use Filament\Widgets\ChartWidget;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class ReservationsChartWidget extends ChartWidget
{
    protected ?string $heading = '📈 Reservations Trend';

    protected static ?int $sort = 5;

    protected int|string|array $columnSpan = 'full';

    public static function canView(): bool
    {
        return Auth::user()->can('view_any_parking_reservation');
    }

    protected function getData(): array
    {
        $days = collect();
        $reservations = collect();
        $revenue = collect();

        // Get data for the last 7 days
        for ($i = 6; $i >= 0; $i--) {
            $date = Carbon::now()->subDays($i);
            $days->push($date->format('D'));

            $dayReservations = ParkingReservation::whereDate('created_at', $date)->count();
            $dayRevenue = ParkingReservation::whereDate('created_at', $date)
                ->whereIn('status', ['completed', 'active'])
                ->sum('total_cost');

            $reservations->push($dayReservations);
            $revenue->push($dayRevenue);
        }

        return [
            'datasets' => [
                [
                    'label' => 'Reservations',
                    'data' => $reservations->toArray(),
                    'borderColor' => '#10b981',
                    'backgroundColor' => 'rgba(16, 185, 129, 0.1)',
                    'fill' => true,
                ],
                [
                    'label' => 'Revenue ($)',
                    'data' => $revenue->toArray(),
                    'borderColor' => '#f59e0b',
                    'backgroundColor' => 'rgba(245, 158, 11, 0.1)',
                    'fill' => true,
                ],
            ],
            'labels' => $days->toArray(),
        ];
    }

    protected function getType(): string
    {
        return 'line';
    }

    protected function getOptions(): array
    {
        return [
            'scales' => [
                'y' => [
                    'beginAtZero' => true,
                ],
            ],
            'plugins' => [
                'legend' => [
                    'display' => true,
                ],
            ],
        ];
    }
}
