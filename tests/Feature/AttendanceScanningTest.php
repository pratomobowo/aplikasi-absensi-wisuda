<?php

namespace Tests\Feature;

use App\Models\Attendance;
use App\Models\GraduationEvent;
use App\Models\GraduationTicket;
use App\Models\Mahasiswa;
use App\Models\User;
use App\Services\AttendanceService;
use App\Services\TicketService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AttendanceScanningTest extends TestCase
{
    // Tidak pakai RefreshDatabase karena akan menghapus data existing
    // Test ini diasumsikan dijalankan di environment test/dev

    /**
     * Setup data untuk test
     */
    private function createTestData()
    {
        // Buat admin scanner
        $admin = User::firstOrCreate(
            ['email' => 'test.scanner@wisuda.test'],
            [
                'name' => 'Test Scanner',
                'password' => bcrypt('password123'),
                'role' => 'admin',
            ]
        );

        // Buat event aktif
        $event = GraduationEvent::create([
            'name' => 'TEST WISUDA SIMULASI',
            'date' => now(),
            'time' => now()->format('H:i:s'),
            'location_name' => 'Gedung Test',
            'location_address' => 'Alamat Test',
            'status' => 'active',
            'is_active' => true,
        ]);

        return [$admin, $event];
    }

    /**
     * Test: Simulasi scanning 1 mahasiswa
     */
    public function test_single_scan(): void
    {
        [$admin, $event] = $this->createTestData();
        $ticketService = app(TicketService::class);
        $attendanceService = app(AttendanceService::class);

        // Buat mahasiswa
        $mahasiswa = Mahasiswa::create([
            'npm' => 'TEST' . time(),
            'nama' => 'Mahasiswa Test',
            'program_studi' => 'Teknik Informatika',
            'ipk' => 3.75,
            'password' => bcrypt('password'),
        ]);

        // Generate tiket
        $ticket = $ticketService->generateTicket($mahasiswa, $event);
        $this->assertNotNull($ticket);

        // Scan QR
        $result = $attendanceService->recordAttendance($ticket->qr_token_mahasiswa, $admin);

        // Assert berhasil
        $this->assertTrue($result['success'], 'Scan seharusnya berhasil: ' . ($result['message'] ?? ''));
        $this->assertEquals('mahasiswa', $result['data']['role'] ?? '');
        $this->assertEquals($mahasiswa->nama, $result['data']['nama'] ?? '');

        // Assert tercatat di database
        $this->assertDatabaseHas('attendances', [
            'graduation_ticket_id' => $ticket->id,
            'role' => 'mahasiswa',
            'scanned_by' => $admin->id,
        ]);

        // Test duplicate scan (harus gagal)
        $result2 = $attendanceService->recordAttendance($ticket->qr_token_mahasiswa, $admin);
        $this->assertFalse($result2['success'], 'Duplicate scan seharusnya gagal');

        // Cleanup
        Attendance::where('graduation_ticket_id', $ticket->id)->delete();
        $ticket->delete();
        $mahasiswa->delete();
        $event->delete();
    }

    /**
     * Test: Simulasi scanning 10 mahasiswa
     */
    public function test_bulk_scan(): void
    {
        [$admin, $event] = $this->createTestData();
        $ticketService = app(TicketService::class);
        $attendanceService = app(AttendanceService::class);

        $count = 10;
        $mahasiswas = [];
        $tickets = [];
        $success = 0;
        $failed = 0;

        // Generate mahasiswa dan tiket
        for ($i = 1; $i <= $count; $i++) {
            $mhs = Mahasiswa::create([
                'npm' => 'BULK' . str_pad($i, 4, '0', STR_PAD_LEFT) . time(),
                'nama' => "Mahasiswa Bulk {$i}",
                'program_studi' => 'Sistem Informasi',
                'ipk' => round(mt_rand(275, 400) / 100, 2),
                'password' => bcrypt('password'),
            ]);

            $ticket = $ticketService->generateTicket($mhs, $event);
            
            $mahasiswas[] = $mhs;
            $tickets[] = $ticket;
        }

        // Scan semua
        foreach ($tickets as $ticket) {
            $result = $attendanceService->recordAttendance($ticket->qr_token_mahasiswa, $admin);
            
            if ($result['success']) {
                $success++;
            } else {
                $failed++;
            }
        }

        // Assert semua berhasil
        $this->assertEquals($count, $success, "Semua {$count} scan harus berhasil");
        $this->assertEquals(0, $failed, 'Tidak boleh ada yang gagal');

        // Assert jumlah attendance di database
        $attendanceCount = Attendance::whereHas('graduationTicket', function ($q) use ($event) {
            $q->where('graduation_event_id', $event->id);
        })->count();

        $this->assertEquals($count, $attendanceCount, "Harus ada {$count} record attendance");

        // Cleanup
        Attendance::whereHas('graduationTicket', function ($q) use ($event) {
            $q->where('graduation_event_id', $event->id);
        })->delete();

        foreach ($tickets as $ticket) {
            $ticket->delete();
        }

        foreach ($mahasiswas as $mhs) {
            $mhs->delete();
        }

        $event->delete();
    }

    /**
     * Test: Scan dengan QR invalid
     */
    public function test_scan_invalid_qr(): void
    {
        [$admin] = $this->createTestData();
        $attendanceService = app(AttendanceService::class);

        $result = $attendanceService->recordAttendance('INVALID_QR_CODE', $admin);

        $this->assertFalse($result['success'], 'QR invalid harus gagal');
        $this->assertNotEmpty($result['message']);
    }
}
