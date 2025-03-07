<?php

namespace App\Filament\Resources\RestoResource\Pages;

use App\Filament\Resources\RestoResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditResto extends EditRecord
{
    protected static string $resource = RestoResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
