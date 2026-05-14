<?php

namespace App\Filament\Resources\GateLogs\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\Filter;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class GateLogsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->defaultSort('logged_at', 'desc')
            ->columns([
                TextColumn::make('logged_at')
                    ->label('Time')
                    ->dateTime('Y-m-d H:i')
                    ->sortable(),

                TextColumn::make('plate_number')
                    ->label('Plate')
                    ->searchable()
                    ->copyable()
                    ->badge()
                    ->color('gray')
                    ->weight('bold'),

                TextColumn::make('gate_number')
                    ->label('Gate #')
                    ->badge()
                    ->color('info')
                    ->sortable(),

                TextColumn::make('direction')
                    ->label('Status')
                    ->badge()
                    ->formatStateUsing(fn (string $state): string => match ($state) {
                        'in'  => '→ Entering',
                        'out' => '← Leaving',
                        default => ucfirst($state),
                    })
                    ->color(fn (string $state): string => match ($state) {
                        'in'  => 'success',
                        'out' => 'warning',
                        default => 'gray',
                    }),

                TextColumn::make('firebase_key')
                    ->label('Source')
                    ->formatStateUsing(fn (?string $state): string => $state ? 'Firebase' : 'Manual')
                    ->badge()
                    ->color(fn (?string $state): string => $state ? 'primary' : 'gray')
                    ->toggleable(isToggledHiddenByDefault: true),

                TextColumn::make('created_at')
                    ->label('Synced at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                SelectFilter::make('direction')
                    ->label('Direction')
                    ->options([
                        'in' => 'Entering',
                        'out' => 'Leaving',
                    ]),

                SelectFilter::make('gate_number')
                    ->label('Gate')
                    ->options(fn () => \App\Models\GateLog::query()
                        ->distinct()
                        ->orderBy('gate_number')
                        ->pluck('gate_number', 'gate_number')
                        ->map(fn ($n) => 'Gate ' . $n)
                        ->all()),

                Filter::make('today')
                    ->label('Today only')
                    ->toggle()
                    ->query(fn (Builder $q): Builder => $q->whereDate('logged_at', today())),
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
