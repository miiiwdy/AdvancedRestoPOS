<?php

namespace App\Filament\Resources\KategoriMejaResource\Pages;

use App\Filament\Resources\KategoriMejaResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListKategoriMejas extends ListRecords
{
    protected static string $resource = KategoriMejaResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
