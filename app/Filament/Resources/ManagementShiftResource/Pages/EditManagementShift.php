<?php

namespace App\Filament\Resources\ManagementShiftResource\Pages;

use App\Filament\Resources\ManagementShiftResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditManagementShift extends EditRecord
{
    protected static string $resource = ManagementShiftResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
