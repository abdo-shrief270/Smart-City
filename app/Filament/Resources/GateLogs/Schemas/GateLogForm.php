<?php

namespace App\Filament\Resources\GateLogs\Schemas;

use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;

class GateLogForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('plate_number')
                    ->label('Plate number')
                    ->required()
                    ->maxLength(32)
                    ->placeholder('e.g. ABC-1234'),

                TextInput::make('gate_number')
                    ->label('Gate number')
                    ->required()
                    ->integer()
                    ->minValue(1)
                    ->maxValue(255),

                Select::make('direction')
                    ->label('Direction')
                    ->required()
                    ->options([
                        'in' => 'Entering (in)',
                        'out' => 'Leaving (out)',
                    ])
                    ->native(false),

                DateTimePicker::make('logged_at')
                    ->label('Logged at')
                    ->required()
                    ->seconds(false)
                    ->default(now()),

                TextInput::make('firebase_key')
                    ->label('Firebase key')
                    ->helperText('Auto-filled for records synced from Firebase. Leave empty for manual entries.')
                    ->maxLength(128)
                    ->disabled()
                    ->dehydrated(false),
            ]);
    }
}
