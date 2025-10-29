<?php

namespace App\Filament\Resources\GraduationTicketResource\Pages;

use App\Filament\Resources\GraduationTicketResource;
use App\Models\GraduationEvent;
use App\Models\GraduationTicket;
use App\Models\Mahasiswa;
use App\Services\TicketService;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;

class CreateGraduationTicket extends CreateRecord
{
    protected static string $resource = GraduationTicketResource::class;

    /**
     * Override record creation to use TicketService
     * This ensures QR tokens are properly generated
     */
    protected function handleRecordCreation(array $data): GraduationTicket
    {
        $ticketService = app(TicketService::class);
        
        $mahasiswa = Mahasiswa::findOrFail($data['mahasiswa_id']);
        $event = GraduationEvent::findOrFail($data['graduation_event_id']);
        
        return $ticketService->createTicket($mahasiswa, $event);
    }
}
