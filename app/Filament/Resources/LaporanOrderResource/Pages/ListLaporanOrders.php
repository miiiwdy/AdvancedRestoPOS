<?php

namespace App\Filament\Resources\LaporanOrderResource\Pages;

use App\Filament\Resources\LaporanOrderResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListLaporanOrders extends ListRecords
{
    protected static string $resource = LaporanOrderResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
