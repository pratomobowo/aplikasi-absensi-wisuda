<?php

namespace App\Filament\Resources\GraduationEventResource\Pages;

use App\Filament\Resources\GraduationEventResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListGraduationEvents extends ListRecords
{
    protected static string $resource = GraduationEventResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
