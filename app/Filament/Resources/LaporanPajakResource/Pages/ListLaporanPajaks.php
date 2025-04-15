<?php

namespace App\Filament\Resources\LaporanPajakResource\Pages;

use App\Filament\Resources\LaporanPajakResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListLaporanPajaks extends ListRecords
{
    protected static string $resource = LaporanPajakResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
