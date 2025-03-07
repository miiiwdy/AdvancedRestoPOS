<?php

namespace App\Filament\Resources\RestoResource\Pages;

use App\Filament\Resources\RestoResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListRestos extends ListRecords
{
    protected static string $resource = RestoResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
