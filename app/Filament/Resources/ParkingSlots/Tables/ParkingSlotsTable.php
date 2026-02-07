<?php

namespace App\Filament\Resources\ParkingSlots\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\DeleteAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class ParkingSlotsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('area')
                    ->badge()
                    ->color('gray'),
                TextColumn::make('slot_number')
                    ->numeric()
                    ->sortable()
                    ->label('Slot #'),
                TextColumn::make('status')
                    ->badge()
                    ->color(fn(string $state): string => match ($state) {
                        'available' => 'success',
                        'occupied' => 'danger',
                        'reserved' => 'warning',
                        default => 'gray',
                    }),
                TextColumn::make('cost_per_hour')
                    ->money('USD')
                    ->sortable()
                    ->label('Cost/Hour'),
                TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('updated_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                //
            ])
            ->recordActions([
                EditAction::make(),
                DeleteAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}
