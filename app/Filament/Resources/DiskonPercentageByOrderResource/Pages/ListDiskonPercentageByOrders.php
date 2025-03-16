<?php

namespace App\Filament\Resources\DiskonPercentageByOrderResource\Pages;

use App\Filament\Resources\DiskonPercentageByOrderResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListDiskonPercentageByOrders extends ListRecords
{
    protected static string $resource = DiskonPercentageByOrderResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
