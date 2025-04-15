<?php

namespace App\Filament\Resources\LaporanOrderProductResource\Pages;

use App\Filament\Resources\LaporanOrderProductResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListLaporanOrderProducts extends ListRecords
{
    protected static string $resource = LaporanOrderProductResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
