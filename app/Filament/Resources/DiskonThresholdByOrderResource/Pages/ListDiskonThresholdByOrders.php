<?php

namespace App\Filament\Resources\DiskonThresholdByOrderResource\Pages;

use App\Filament\Resources\DiskonThresholdByOrderResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListDiskonThresholdByOrders extends ListRecords
{
    protected static string $resource = DiskonThresholdByOrderResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
