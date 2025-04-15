<?php

namespace App\Filament\Resources\LaporanOrderResource\Pages;

use App\Filament\Resources\LaporanOrderResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditLaporanOrder extends EditRecord
{
    protected static string $resource = LaporanOrderResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
