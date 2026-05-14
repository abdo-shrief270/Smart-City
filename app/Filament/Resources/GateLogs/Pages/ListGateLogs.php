<?php

namespace App\Filament\Resources\GateLogs\Pages;

use App\Filament\Resources\GateLogs\GateLogResource;
use App\Services\FirebaseService;
use Filament\Actions\Action;
use Filament\Actions\CreateAction;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\ListRecords;

class ListGateLogs extends ListRecords
{
    protected static string $resource = GateLogResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Action::make('syncFromFirebase')
                ->label('Sync from Firebase')
                ->icon('heroicon-o-arrow-path')
                ->color('warning')
                ->action(function (): void {
                    try {
                        $count = app(FirebaseService::class)->syncGateLogsFromFirebase();
                        Notification::make()
                            ->title("Imported {$count} new gate log(s)")
                            ->success()
                            ->send();
                    } catch (\Throwable $e) {
                        Notification::make()
                            ->title('Sync failed')
                            ->body($e->getMessage())
                            ->danger()
                            ->send();
                    }
                }),

            CreateAction::make()->label('Manual entry'),
        ];
    }
}
