<?php

namespace App\Filament\Widgets;

use App\Models\ParkingSlot;
use Filament\Widgets\ChartWidget;
use Illuminate\Support\Facades\Auth;

class ParkingOccupancyChart extends ChartWidget
{
    protected ?string $heading = '🅿️ Parking Occupancy';

    protected static ?int $sort = 6;

    public static function canView(): bool
    {
        return Auth::user()->can('page_SmartParking');
    }

    protected function getData(): array
    {
        $available = ParkingSlot::where('status', 'available')->count();
        $reserved = ParkingSlot::where('status', 'reserved')->count();
        $occupied = ParkingSlot::where('status', 'occupied')->count();

        return [
            'datasets' => [
                [
                    'label' => 'Slots',
                    'data' => [$available, $reserved, $occupied],
                    'backgroundColor' => [
                        '#10b981', // green - available
                        '#f59e0b', // amber - reserved
                        '#ef4444', // red - occupied
                    ],
                    'borderWidth' => 0,
                ],
            ],
            'labels' => ['Available', 'Reserved', 'Occupied'],
        ];
    }

    protected function getType(): string
    {
        return 'doughnut';
    }

    protected function getOptions(): array
    {
        return [
            'plugins' => [
                'legend' => [
                    'position' => 'bottom',
                ],
            ],
            'cutout' => '60%',
        ];
    }
}
