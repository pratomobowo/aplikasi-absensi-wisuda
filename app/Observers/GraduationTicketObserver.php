<?php

namespace App\Observers;

use App\Models\GraduationTicket;
use App\Services\ScannerCacheService;
use Illuminate\Support\Facades\Log;

class GraduationTicketObserver
{
    /**
     * Handle the GraduationTicket "created" event.
     */
    public function created(GraduationTicket $ticket): void
    {
        Log::info('GraduationTicketObserver: Ticket created, invalidating cache', [
            'ticket_id' => $ticket->id,
            'graduation_event_id' => $ticket->graduation_event_id,
            'mahasiswa_id' => $ticket->mahasiswa_id,
        ]);

        // Invalidate all event caches since new ticket was added
        ScannerCacheService::invalidateAll();
    }

    /**
     * Handle the GraduationTicket "updated" event.
     */
    public function updated(GraduationTicket $ticket): void
    {
        Log::info('GraduationTicketObserver: Ticket updated, invalidating cache', [
            'ticket_id' => $ticket->id,
            'graduation_event_id' => $ticket->graduation_event_id,
        ]);

        // Invalidate specific ticket cache
        ScannerCacheService::invalidateTicket($ticket->id);
        // Also invalidate event cache since ticket counts/status changed
        ScannerCacheService::invalidateAll();
    }

    /**
     * Handle the GraduationTicket "deleted" event.
     */
    public function deleted(GraduationTicket $ticket): void
    {
        Log::info('GraduationTicketObserver: Ticket deleted, invalidating cache', [
            'ticket_id' => $ticket->id,
            'graduation_event_id' => $ticket->graduation_event_id,
        ]);

        ScannerCacheService::invalidateTicket($ticket->id);
        ScannerCacheService::invalidateAll();
    }

    /**
     * Handle the GraduationTicket "restored" event.
     */
    public function restored(GraduationTicket $ticket): void
    {
        Log::info('GraduationTicketObserver: Ticket restored, invalidating cache', [
            'ticket_id' => $ticket->id,
        ]);

        ScannerCacheService::invalidateAll();
    }

    /**
     * Handle the GraduationTicket "force deleted" event.
     */
    public function forceDeleted(GraduationTicket $ticket): void
    {
        Log::info('GraduationTicketObserver: Ticket force deleted, invalidating cache', [
            'ticket_id' => $ticket->id,
        ]);

        ScannerCacheService::invalidateAll();
    }
}
