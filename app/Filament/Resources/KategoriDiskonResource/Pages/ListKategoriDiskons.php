<?php

namespace App\Filament\Resources\KategoriDiskonResource\Pages;

use App\Filament\Resources\KategoriDiskonResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListKategoriDiskons extends ListRecords
{
    protected static string $resource = KategoriDiskonResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
