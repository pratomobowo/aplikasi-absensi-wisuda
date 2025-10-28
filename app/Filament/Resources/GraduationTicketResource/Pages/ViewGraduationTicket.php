<?php

namespace App\Filament\Resources\GraduationTicketResource\Pages;

use App\Filament\Resources\GraduationTicketResource;
use Filament\Actions;
use Filament\Resources\Pages\ViewRecord;

class ViewGraduationTicket extends ViewRecord
{
    protected static string $resource = GraduationTicketResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\EditAction::make(),
        ];
    }
}
