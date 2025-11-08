<?php

namespace App\Filament\Resources\BukuWisudaResource\Pages;

use App\Filament\Resources\BukuWisudaResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListBukuWisudas extends ListRecords
{
    protected static string $resource = BukuWisudaResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
