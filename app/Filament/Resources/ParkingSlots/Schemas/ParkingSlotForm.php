<?php

namespace App\Filament\Resources\ParkingSlots\Schemas;

use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;

class ParkingSlotForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Select::make('area')
                    ->options([
                        'A' => 'Area A',
                        'B' => 'Area B',
                        'C' => 'Area C',
                    ])
                    ->required(),
                TextInput::make('slot_number')
                    ->required()
                    ->numeric()
                    ->helperText('e.g., 1 for A-1'),
                Select::make('status')
                    ->options([
                        'available' => 'Available',
                        'occupied' => 'Occupied',
                        'reserved' => 'Reserved',
                    ])
                    ->default('available')
                    ->required(),
                TextInput::make('cost_per_hour')
                    ->required()
                    ->prefix('$')
                    ->numeric()
                    ->default(10.00),
            ]);
    }
}
