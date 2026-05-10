<?php

namespace Tests\Feature;

use App\Models\GraduationEvent;
use App\Models\GraduationTicket;
use App\Models\Mahasiswa;
use App\Services\QRCodeService;
use App\Services\TicketService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class TicketQrGenerationTest extends TestCase
{
    use RefreshDatabase;

    public function test_generate_qr_tokens_returns_v2_tokens_for_all_roles(): void
    {
        $event = GraduationEvent::create([
            'name' => 'Wisuda Test',
            'date' => now()->addDay()->toDateString(),
            'time' => '08:00:00',
            'location_name' => 'Aula Test',
            'location_address' => 'Jl. Test',
            'is_active' => true,
        ]);

        $mahasiswa = Mahasiswa::create([
            'npm' => '1234567890',
            'nama' => 'Mahasiswa Test',
            'program_studi' => 'Teknik Informatika',
            'ipk' => 3.5,
            'yudisium' => 'Dengan Pujian',
            'judul_skripsi' => 'Judul Test',
        ]);

        $ticket = GraduationTicket::create([
            'mahasiswa_id' => $mahasiswa->id,
            'graduation_event_id' => $event->id,
            'magic_link_token' => 'token-test',
            'qr_token_mahasiswa' => '{}',
            'qr_token_pendamping1' => '{}',
            'qr_token_pendamping2' => '{}',
            'expires_at' => now()->addDay(),
        ]);

        $tokens = app(TicketService::class)->generateQRTokens($ticket);
        $qrCodeService = app(QRCodeService::class);

        foreach (['mahasiswa', 'pendamping1', 'pendamping2'] as $role) {
            $data = $qrCodeService->decryptQRData($tokens[$role]);

            $this->assertSame($ticket->id, $data['ticket_id']);
            $this->assertSame($role, $data['role']);
            $this->assertSame($event->id, $data['event_id']);
            $this->assertSame(2, $data['version']);
            $this->assertFalse($data['_legacy']);
        }
    }
}
