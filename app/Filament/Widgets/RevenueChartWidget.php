<?php

namespace App\Filament\Widgets;

use App\Models\ParkingReservation;
use Filament\Widgets\ChartWidget;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class RevenueChartWidget extends ChartWidget
{
    protected ?string $heading = '💰 Weekly Revenue';

    protected static ?int $sort = 8;

    public static function canView(): bool
    {
        return Auth::user()->can('view_smart_parking_revenue');
    }

    protected function getData(): array
    {
        $days = collect();
        $revenue = collect();

        for ($i = 6; $i >= 0; $i--) {
            $date = Carbon::now()->subDays($i);
            $days->push($date->format('D'));

            $dayRevenue = ParkingReservation::whereDate('created_at', $date)
                ->whereIn('status', ['completed', 'active'])
                ->sum('total_cost');

            $revenue->push(round($dayRevenue, 2));
        }

        return [
            'datasets' => [
                [
                    'label' => 'Revenue ($)',
                    'data' => $revenue->toArray(),
                    'backgroundColor' => 'rgba(245, 158, 11, 0.5)',
                    'borderColor' => '#f59e0b',
                    'borderWidth' => 2,
                    'borderRadius' => 6,
                ],
            ],
            'labels' => $days->toArray(),
        ];
    }

    protected function getType(): string
    {
        return 'bar';
    }

    protected function getOptions(): array
    {
        return [
            'scales' => [
                'y' => [
                    'beginAtZero' => true,
                    'ticks' => [
                        'callback' => "function(value) { return '$' + value; }",
                    ],
                ],
            ],
            'plugins' => [
                'legend' => [
                    'display' => false,
                ],
            ],
        ];
    }
}
