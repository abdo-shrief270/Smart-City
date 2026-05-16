<?php

namespace App\Filament\Pages;

use App\Filament\Resources\ParkingSlots\ParkingSlotResource;
use App\Filament\Resources\Users\UserResource;
use App\Filament\Widgets\SmartFarmWidget;
use App\Filament\Widgets\SmartParkingWidget;
use App\Filament\Widgets\SmartTankWidget;
use App\Filament\Widgets\StatsOverviewWidget;
use App\Filament\Widgets\SystemAlertsWidget;
use App\Services\FirebaseService;
use Filament\Actions\Action;
use Filament\Actions\ActionGroup;
use Filament\Notifications\Notification;
use Filament\Pages\Dashboard as BaseDashboard;
use Filament\Widgets\AccountWidget;
use Illuminate\Support\Facades\Auth;

class Dashboard extends BaseDashboard
{
    public function getWidgets(): array
    {
        return [
            AccountWidget::class,
            StatsOverviewWidget::class,
            SystemAlertsWidget::class,
            SmartFarmWidget::class,
            SmartTankWidget::class,
            SmartParkingWidget::class,
        ];
    }

    public function getHeaderActions(): array
    {
        return [
            ActionGroup::make([
                Action::make('newUser')
                    ->label('New User')
                    ->icon('heroicon-o-user-plus')
                    ->color('primary')
                    ->visible(fn (): bool => Auth::user()?->can('create_user') ?? false)
                    ->url(fn (): string => UserResource::getUrl('create')),

                Action::make('newParkingSlot')
                    ->label('New Parking Slot')
                    ->icon('heroicon-o-squares-plus')
                    ->color('info')
                    ->visible(fn (): bool => Auth::user()?->can('create_parking::slot') ?? Auth::user()?->can('create_parking_slot') ?? false)
                    ->url(fn (): string => ParkingSlotResource::getUrl('create')),

                Action::make('firebaseSettings')
                    ->label('Firebase Settings')
                    ->icon('heroicon-o-cog-6-tooth')
                    ->color('gray')
                    ->url(fn (): string => ManageFirebaseSettings::getUrl()),
            ])
                ->label('Manage')
                ->icon('heroicon-o-plus-circle')
                ->button()
                ->color('primary'),

            Action::make('syncFirebase')
                ->label('Sync Firebase Now')
                ->icon('heroicon-o-arrow-path')
                ->color('warning')
                ->requiresConfirmation()
                ->modalHeading('Sync from Firebase?')
                ->modalDescription('Pull the latest sensor snapshots from Firebase into the local database.')
                ->modalSubmitActionLabel('Sync now')
                ->action(function (): void {
                    try {
                        app(FirebaseService::class)->syncToDatabase();
                        Notification::make()
                            ->title('Firebase sync complete')
                            ->success()
                            ->send();
                    } catch (\Throwable $e) {
                        Notification::make()
                            ->title('Firebase sync failed')
                            ->body($e->getMessage())
                            ->danger()
                            ->send();
                    }
                }),

            Action::make('landingPage')
                ->label('Landing Page')
                ->icon('heroicon-o-home')
                ->color('gray')
                ->url(fn (): string => url('/'), shouldOpenInNewTab: true),
        ];
    }
}
