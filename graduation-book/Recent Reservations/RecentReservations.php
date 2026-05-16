<?php

namespace App\Filament\Pages;

use App\Filament\Widgets\RecentReservationsWidget;
use Filament\Pages\Page;
use Illuminate\Support\Facades\Auth;

class RecentReservations extends Page
{
    protected string $view = 'filament.pages.recent-reservations';

    // -----------------------------------------------------------------
    // Navigation
    // -----------------------------------------------------------------
    public static function getNavigationIcon(): ?string
    {
        return 'heroicon-o-clipboard-document-list';
    }

    public static function getNavigationLabel(): string
    {
        return 'Recent Reservations';
    }

    public static function getNavigationSort(): ?int
    {
        return 9;
    }

    public function getTitle(): string
    {
        return 'Recent Reservations';
    }

    // -----------------------------------------------------------------
    // Access control
    // -----------------------------------------------------------------
    public static function shouldRegisterNavigation(): bool
    {
        return Auth::user()?->can('view_any_parking_reservation') ?? false;
    }

    public static function canAccess(): bool
    {
        return Auth::user()?->can('view_any_parking_reservation') ?? false;
    }

    // -----------------------------------------------------------------
    // Header widgets
    // -----------------------------------------------------------------
    protected function getHeaderWidgets(): array
    {
        return [
            RecentReservationsWidget::class,
        ];
    }

    public function getHeaderWidgetsColumns(): int|array
    {
        return 1;
    }
}
