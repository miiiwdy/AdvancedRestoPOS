<?php

namespace App\Filament\Resources\DiskonThresholdByProductResource\Pages;

use App\Filament\Resources\DiskonThresholdByProductResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditDiskonThresholdByProduct extends EditRecord
{
    protected static string $resource = DiskonThresholdByProductResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
