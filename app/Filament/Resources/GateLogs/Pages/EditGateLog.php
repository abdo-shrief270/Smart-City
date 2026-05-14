<?php

namespace App\Filament\Resources\GateLogs\Pages;

use App\Filament\Resources\GateLogs\GateLogResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditGateLog extends EditRecord
{
    protected static string $resource = GateLogResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }
}
