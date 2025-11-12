<?php

namespace App\Services;

use App\Models\GraduationEvent;
use App\Models\GraduationTicket;
use App\Models\Mahasiswa;
use Illuminate\Support\Str;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class TicketService
{
    protected QRCodeService $qrCodeService;

    public function __construct(QRCodeService $qrCodeService)
    {
        $this->qrCodeService = $qrCodeService;
    }

    /**
     * Create graduation ticket for a student
     *
     * @param Mahasiswa $mahasiswa
     * @param GraduationEvent $event
     * @return GraduationTicket
     */
    public function createTicket(Mahasiswa $mahasiswa, GraduationEvent $event): GraduationTicket
    {
        $ticket = new GraduationTicket();
        $ticket->mahasiswa_id = $mahasiswa->id;
        $ticket->graduation_event_id = $event->id;
        $ticket->magic_link_token = $this->generateUniqueToken();
        $ticket->expires_at = $event->date->addDays(1); // Expire 1 day after event
        
        // Set placeholder QR tokens (will be updated after save)
        $ticket->qr_token_mahasiswa = '{}';
        $ticket->qr_token_pendamping1 = '{}';
        $ticket->qr_token_pendamping2 = '{}';
        
        $ticket->save();

        // Now generate real QR tokens with the actual ticket ID
        $qrTokens = $this->generateQRTokens($ticket);
        $ticket->qr_token_mahasiswa = $qrTokens['mahasiswa'];
        $ticket->qr_token_pendamping1 = $qrTokens['pendamping1'];
        $ticket->qr_token_pendamping2 = $qrTokens['pendamping2'];
        $ticket->save();

        return $ticket;
    }

    /**
     * Generate magic link for ticket
     *
     * @param GraduationTicket $ticket
     * @return string Full URL
     */
    public function generateMagicLink(GraduationTicket $ticket): string
    {
        return route('invitation.show', ['token' => $ticket->magic_link_token]);
    }

    /**
     * Generate QR tokens for all roles
     *
     * @param GraduationTicket $ticket
     * @return array
     */
    public function generateQRTokens(GraduationTicket $ticket): array
    {
        $roles = ['mahasiswa', 'pendamping1', 'pendamping2'];
        $tokens = [];

        foreach ($roles as $role) {
            $data = [
                'ticket_id' => $ticket->id,
                'role' => $role,
                'event_id' => $ticket->graduation_event_id,
            ];

            $tokens[$role] = $this->qrCodeService->encryptQRData($data);
        }

        return $tokens;
    }

    /**
     * Validate magic link token
     *
     * @param string $token
     * @return GraduationTicket|null
     */
    public function validateMagicLink(string $token): ?GraduationTicket
    {
        $ticket = GraduationTicket::where('magic_link_token', $token)->first();

        if (!$ticket) {
            return null;
        }

        // Check if ticket is expired
        if ($ticket->expires_at && $ticket->expires_at->isPast()) {
            return null;
        }

        return $ticket;
    }

    /**
     * Mark ticket as distributed
     *
     * @param GraduationTicket $ticket
     * @return void
     */
    public function markAsDistributed(GraduationTicket $ticket): void
    {
        $ticket->is_distributed = true;
        $ticket->distributed_at = now();
        $ticket->save();
    }

    /**
     * Generate unique token for magic link
     *
     * @return string
     */
    protected function generateUniqueToken(): string
    {
        do {
            $token = Str::random(64);
        } while (GraduationTicket::where('magic_link_token', $token)->exists());

        return $token;
    }

    /**
     * Generate tickets for multiple mahasiswa in an event
     *
     * @param GraduationEvent $event
     * @param Collection|array|null $mahasiswaIds Specific mahasiswa IDs, or null for all without tickets
     * @param bool $skipExisting Skip if ticket already exists
     * @return array Result array with created, skipped, failed counts
     */
    public function generateTicketsForEvent(
        GraduationEvent $event,
        $mahasiswaIds = null,
        bool $skipExisting = true
    ): array
    {
        $result = [
            'created' => 0,
            'skipped' => 0,
            'failed' => 0,
            'errors' => [],
        ];

        try {
            // Get mahasiswa to process
            $query = Mahasiswa::query();

            if ($mahasiswaIds) {
                // If specific IDs provided
                $ids = is_array($mahasiswaIds) ? $mahasiswaIds : $mahasiswaIds->pluck('id')->toArray();
                $query->whereIn('id', $ids);
            } else {
                // Get all mahasiswa without tickets for this event
                $query->whereDoesntHave('graduationTickets', function ($q) use ($event) {
                    $q->where('graduation_event_id', $event->id);
                });
            }

            $mahasiswas = $query->get();

            Log::info('TicketService: Starting bulk ticket generation', [
                'event_id' => $event->id,
                'event_name' => $event->name,
                'total_mahasiswa' => $mahasiswas->count(),
            ]);

            foreach ($mahasiswas as $mahasiswa) {
                try {
                    // Check if ticket already exists
                    $existingTicket = GraduationTicket::where('mahasiswa_id', $mahasiswa->id)
                        ->where('graduation_event_id', $event->id)
                        ->first();

                    if ($existingTicket) {
                        if ($skipExisting) {
                            $result['skipped']++;
                            continue;
                        } else {
                            // Delete existing and recreate
                            $existingTicket->delete();
                        }
                    }

                    // Create new ticket
                    $this->createTicket($mahasiswa, $event);
                    $result['created']++;

                } catch (\Exception $e) {
                    $result['failed']++;
                    $errorMsg = "Mahasiswa ID {$mahasiswa->id} ({$mahasiswa->nama}): {$e->getMessage()}";
                    $result['errors'][] = $errorMsg;

                    Log::error('TicketService: Ticket creation failed', [
                        'mahasiswa_id' => $mahasiswa->id,
                        'mahasiswa_name' => $mahasiswa->nama,
                        'event_id' => $event->id,
                        'error' => $e->getMessage(),
                    ]);
                }
            }

            // Clear cache
            $this->clearTicketCache($event->id);

            Log::info('TicketService: Bulk ticket generation completed', $result);

        } catch (\Exception $e) {
            Log::error('TicketService: Bulk ticket generation failed', [
                'event_id' => $event->id,
                'error' => $e->getMessage(),
            ]);
            $result['failed'] = -1; // Indicates fatal error
            $result['errors'][] = "Fatal error: {$e->getMessage()}";
        }

        return $result;
    }

    /**
     * Get mahasiswa that are missing tickets for an event
     *
     * @param GraduationEvent $event
     * @return Collection
     */
    public function getMissingTickets(GraduationEvent $event): Collection
    {
        return Mahasiswa::whereDoesntHave('graduationTickets', function ($q) use ($event) {
            $q->where('graduation_event_id', $event->id);
        })->get();
    }

    /**
     * Get count of missing tickets for an event
     *
     * @param GraduationEvent $event
     * @return int
     */
    public function getMissingTicketCount(GraduationEvent $event): int
    {
        return Mahasiswa::whereDoesntHave('graduationTickets', function ($q) use ($event) {
            $q->where('graduation_event_id', $event->id);
        })->count();
    }

    /**
     * Clear ticket-related cache for an event
     *
     * @param int $eventId
     * @return void
     */
    protected function clearTicketCache(int $eventId): void
    {
        // Add cache invalidation here if caching is implemented
        // For now, just log
        Log::debug('TicketService: Cache cleared for event', ['event_id' => $eventId]);
    }
}
