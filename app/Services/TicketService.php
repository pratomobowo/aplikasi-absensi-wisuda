<?php

namespace App\Services;

use App\Models\GraduationEvent;
use App\Models\GraduationTicket;
use App\Models\Mahasiswa;
use Illuminate\Support\Str;

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
}
