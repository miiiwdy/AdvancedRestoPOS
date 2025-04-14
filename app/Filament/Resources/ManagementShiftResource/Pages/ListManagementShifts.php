<?php

namespace App\Filament\Resources\ManagementShiftResource\Pages;

use App\Filament\Resources\ManagementShiftResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListManagementShifts extends ListRecords
{
    protected static string $resource = ManagementShiftResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
