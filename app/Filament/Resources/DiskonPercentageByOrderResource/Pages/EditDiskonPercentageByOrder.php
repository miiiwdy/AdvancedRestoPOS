<?php

namespace App\Filament\Resources\DiskonPercentageByOrderResource\Pages;

use App\Filament\Resources\DiskonPercentageByOrderResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditDiskonPercentageByOrder extends EditRecord
{
    protected static string $resource = DiskonPercentageByOrderResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
