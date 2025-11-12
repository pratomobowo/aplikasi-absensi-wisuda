<?php

namespace App\Filament\Resources\KonsumsiRecordResource\Pages;

use App\Filament\Resources\KonsumsiRecordResource;
use Filament\Actions;
use Filament\Resources\Pages\ManageRecords;

class ManageKonsumsiRecords extends ManageRecords
{
    protected static string $resource = KonsumsiRecordResource::class;

    protected function getHeaderActions(): array
    {
        return [
            // CreateAction is disabled - konsumsi can only be created via scanner
        ];
    }
}
