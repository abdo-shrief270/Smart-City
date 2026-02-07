<?php

namespace App\Filament\Widgets;

use App\Models\User;
use Filament\Widgets\ChartWidget;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class UserGrowthChart extends ChartWidget
{
    protected ?string $heading = '👥 User Registrations';

    protected static ?int $sort = 10;

    public static function canView(): bool
    {
        return Auth::user()->can('view_any_user');
    }

    protected function getData(): array
    {
        $months = collect();
        $users = collect();

        for ($i = 5; $i >= 0; $i--) {
            $date = Carbon::now()->subMonths($i);
            $months->push($date->format('M'));

            $monthUsers = User::whereMonth('created_at', $date->month)
                ->whereYear('created_at', $date->year)
                ->count();

            $users->push($monthUsers);
        }

        return [
            'datasets' => [
                [
                    'label' => 'New Users',
                    'data' => $users->toArray(),
                    'backgroundColor' => [
                        'rgba(16, 185, 129, 0.8)',
                        'rgba(16, 185, 129, 0.7)',
                        'rgba(16, 185, 129, 0.6)',
                        'rgba(16, 185, 129, 0.7)',
                        'rgba(16, 185, 129, 0.8)',
                        'rgba(16, 185, 129, 0.9)',
                    ],
                    'borderRadius' => 8,
                ],
            ],
            'labels' => $months->toArray(),
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
                        'stepSize' => 1,
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
