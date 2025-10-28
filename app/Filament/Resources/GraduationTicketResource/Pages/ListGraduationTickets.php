<?php

namespace App\Filament\Resources\GraduationTicketResource\Pages;

use App\Filament\Resources\GraduationTicketResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListGraduationTickets extends ListRecords
{
    protected static string $resource = GraduationTicketResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
