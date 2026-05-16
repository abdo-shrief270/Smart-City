<?php

namespace App\Filament\Resources\GateLogs;

use App\Filament\Resources\GateLogs\Pages\CreateGateLog;
use App\Filament\Resources\GateLogs\Pages\EditGateLog;
use App\Filament\Resources\GateLogs\Pages\ListGateLogs;
use App\Filament\Resources\GateLogs\Schemas\GateLogForm;
use App\Filament\Resources\GateLogs\Tables\GateLogsTable;
use App\Models\GateLog;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;

class GateLogResource extends Resource
{
    protected static ?string $model = GateLog::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedTruck;

    protected static ?string $recordTitleAttribute = 'plate_number';

    public static function getNavigationLabel(): string
    {
        return 'Gate Logs';
    }

    public static function getNavigationSort(): ?int
    {
        return 7;
    }

    public static function form(Schema $schema): Schema
    {
        return GateLogForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return GateLogsTable::configure($table);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListGateLogs::route('/'),
            'create' => CreateGateLog::route('/create'),
            'edit' => EditGateLog::route('/{record}/edit'),
        ];
    }
}
