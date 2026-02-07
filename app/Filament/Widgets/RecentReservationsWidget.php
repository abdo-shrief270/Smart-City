<?php

namespace App\Filament\Widgets;

use App\Models\ParkingReservation;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget as BaseWidget;
use Illuminate\Support\Facades\Auth;

class RecentReservationsWidget extends BaseWidget
{
    protected static ?int $sort = 7;

    protected int|string|array $columnSpan = 'full';

    protected static ?string $heading = '📋 Recent Reservations';

    public static function canView(): bool
    {
        return Auth::user()->can('view_any_parking_reservation');
    }

    public function table(Table $table): Table
    {
        return $table
            ->query(
                ParkingReservation::query()
                    ->with(['user', 'parkingSlot'])
                    ->latest()
                    ->limit(5)
            )
            ->columns([
                Tables\Columns\TextColumn::make('user.name')
                    ->label('User')
                    ->searchable()
                    ->icon('heroicon-o-user'),
                Tables\Columns\TextColumn::make('parkingSlot.area')
                    ->label('Slot')
                    ->formatStateUsing(fn($record) => $record->parkingSlot?->area . '-' . $record->parkingSlot?->slot_number),
                Tables\Columns\BadgeColumn::make('status')
                    ->colors([
                        'success' => 'completed',
                        'warning' => 'active',
                        'danger' => 'cancelled',
                    ]),
                Tables\Columns\TextColumn::make('start_time')
                    ->label('Started')
                    ->dateTime('M d, H:i')
                    ->sortable(),
                Tables\Columns\TextColumn::make('total_cost')
                    ->label('Cost')
                    ->money('USD')
                    ->sortable(),
            ])
            ->paginated(false);
    }
}
