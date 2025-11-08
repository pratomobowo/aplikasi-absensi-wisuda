<?php

namespace App\Filament\Resources\BukuWisudaResource\Pages;

use App\Filament\Resources\BukuWisudaResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditBukuWisuda extends EditRecord
{
    protected static string $resource = BukuWisudaResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
