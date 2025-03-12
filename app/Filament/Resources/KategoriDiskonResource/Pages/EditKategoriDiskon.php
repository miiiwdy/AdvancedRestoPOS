<?php

namespace App\Filament\Resources\KategoriDiskonResource\Pages;

use App\Filament\Resources\KategoriDiskonResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditKategoriDiskon extends EditRecord
{
    protected static string $resource = KategoriDiskonResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
