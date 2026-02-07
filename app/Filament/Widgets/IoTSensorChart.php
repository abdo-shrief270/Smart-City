<?php

namespace App\Filament\Widgets;

use App\Services\FirebaseService;
use Filament\Widgets\ChartWidget;
use Illuminate\Support\Facades\Auth;

class IoTSensorChart extends ChartWidget
{
    protected ?string $heading = '📊 Sensor Data (24h)';

    protected static ?int $sort = 9;

    protected ?string $pollingInterval = '30s';

    public static function canView(): bool
    {
        return Auth::user()->can('page_SmartFarm') || Auth::user()->can('page_SmartTank');
    }

    protected function getData(): array
    {
        $firebaseService = app(FirebaseService::class);

        // Get current sensor data
        $farmData = $firebaseService->get('smart_farm');
        $tankData = $firebaseService->get('smart-tank') ?? [];

        $sensors = $farmData['sensors'] ?? $farmData ?? [];
        $temp = $sensors['temperature'] ?? $sensors['temp'] ?? 25;
        $humidity = $sensors['humidity'] ?? 60;
        $waterLevel = $tankData['level'] ?? 50;

        // Simulate historical data for visualization (in real app, fetch from DB)
        $hours = ['00:00', '04:00', '08:00', '12:00', '16:00', '20:00', 'Now'];

        return [
            'datasets' => [
                [
                    'label' => 'Temperature (°C)',
                    'data' => [22, 21, 23, 28, 32, 30, $temp],
                    'borderColor' => '#ef4444',
                    'backgroundColor' => 'rgba(239, 68, 68, 0.1)',
                    'fill' => true,
                    'tension' => 0.4,
                ],
                [
                    'label' => 'Humidity (%)',
                    'data' => [65, 70, 68, 55, 50, 58, $humidity],
                    'borderColor' => '#3b82f6',
                    'backgroundColor' => 'rgba(59, 130, 246, 0.1)',
                    'fill' => true,
                    'tension' => 0.4,
                ],
                [
                    'label' => 'Water Level (%)',
                    'data' => [80, 75, 70, 65, 60, 55, $waterLevel],
                    'borderColor' => '#10b981',
                    'backgroundColor' => 'rgba(16, 185, 129, 0.1)',
                    'fill' => true,
                    'tension' => 0.4,
                ],
            ],
            'labels' => $hours,
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
                    'max' => 100,
                ],
            ],
            'plugins' => [
                'legend' => [
                    'position' => 'bottom',
                ],
            ],
        ];
    }
}
