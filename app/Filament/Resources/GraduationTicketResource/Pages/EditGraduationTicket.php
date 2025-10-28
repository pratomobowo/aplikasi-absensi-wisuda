<?php

namespace App\Filament\Resources\GraduationTicketResource\Pages;

use App\Filament\Resources\GraduationTicketResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditGraduationTicket extends EditRecord
{
    protected static string $resource = GraduationTicketResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
