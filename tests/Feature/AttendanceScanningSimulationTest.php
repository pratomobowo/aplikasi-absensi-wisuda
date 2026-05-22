<?php

namespace Tests\Feature;

use App\Models\Attendance;
use App\Models\GraduationEvent;
use App\Models\GraduationTicket;
use App\Models\Mahasiswa;
use App\Models\User;
use App\Services\AttendanceService;
use App\Services\QRCodeService;
use App\Services\TicketService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Crypt;
use Tests\TestCase;

class AttendanceScanningSimulationTest extends TestCase
{
    use RefreshDatabase;

    private QRCodeService $qrService;
    private AttendanceService $attendanceService;
    private TicketService $ticketService;

    protected function setUp(): void
    {
        parent::setUp();
        $this->qrService = app(QRCodeService::class);
        $this->attendanceService = app(AttendanceService::class);
        $this->ticketService = app(TicketService::class);
    }

    /**
     * Test: Simulasi lengkap scan QR oleh tim admin
     */
    public function test_complete_scanning_simulation(): void
    {
        $this->info('=== MEMULAI SIMULASI SCANNING KEHADIRAN ===');
        
        // 1. Setup: Buat admin
        $admin = User::factory()->create([
            'name' => 'Admin Scanner',
            'email' => 'admin@usbypkp.ac.id',
            'role' => 'admin',
        ]);
        $this->info("Admin dibuat: {$admin->name}");
        
        // 2. Setup: Buat event wisuda
        $event = GraduationEvent::factory()->create([
            'name' => 'WISUDA XXII GELOMBANG II',
            'date' => now(),
            'time' => now()->format('H:i:s'),
            'location_name' => 'Gedung Serba Guna',
            'status' => 'active',
            'is_active' => true,
        ]);
        $this->info("Event dibuat: {$event->name}");
        
        // 3. Setup: Buat mahasiswa
        $mahasiswa = Mahasiswa::factory()->create([
            'npm' => '2114218001',
            'nama' => 'Budi Santoso',
            'program_studi' => 'Teknik Informatika',
            'ipk' => 3.75,
            'yudisium' => 'Cum Laude',
        ]);
        $this->info("Mahasiswa dibuat: {$mahasiswa->nama} ({$mahasiswa->npm})");
        
        // 4. Setup: Generate tiket
        $ticket = $this->ticketService->generateTicket($mahasiswa, $event);
        $this->assertNotNull($ticket);
        $this->info("Tiket digenerate dengan QR Token");
        
        // 5. Verifikasi tiket ada
        $this->assertDatabaseHas('graduation_tickets', [
            'mahasiswa_id' => $mahasiswa->id,
            'graduation_event_id' => $event->id,
        ]);
        $this->info("Tiket tersimpan di database");
        
        // 6. Generate QR Code dari tiket
        $qrToken = $ticket->qr_token_mahasiswa;
        $this->assertNotEmpty($qrToken);
        $this->info("QR Token: " . substr($qrToken, 0, 50) . "...");
        
        // 7. Simulasi SCAN PERTAMA - Absensi Pagi
        $this->info("\n--- SCAN PERTAMA (Absensi Pagi) ---");
        $result1 = $this->attendanceService->recordAttendance($qrToken, $admin);
        
        $this->assertTrue($result1['success'], "Scan pertama gagal: " . ($result1['message'] ?? 'Unknown error'));
        $this->info("Status: " . ($result1['success'] ? 'BERHASIL' : 'GAGAL'));
        $this->info("Pesan: " . $result1['message']);
        $this->info("Role: " . ($result1['data']['role'] ?? 'N/A'));
        $this->info("Nama: " . ($result1['data']['nama'] ?? 'N/A'));
        
        // 8. Verifikasi attendance tercatat
        $this->assertDatabaseHas('attendances', [
            'graduation_ticket_id' => $ticket->id,
            'role' => 'mahasiswa',
            'scanned_by' => $admin->id,
        ]);
        $this->info("Attendance tercatat di database");
        
        // 9. Hitung total attendance
        $totalAttendance = Attendance::where('graduation_ticket_id', $ticket->id)->count();
        $this->info("Total scan untuk mahasiswa ini: {$totalAttendance}");
        $this->assertEquals(1, $totalAttendance);
        
        // 10. Simulasi SCAN KEDUA - Konsumsi Sore (duplicate)
        $this->info("\n--- SCAN KEDUA (Konsumsi Sore - Duplicate Test) ---");
        $result2 = $this->attendanceService->recordAttendance($qrToken, $admin);
        
        // Seharusnya gagal karena duplicate
        $this->assertFalse($result2['success'], "Scan kedua seharusnya gagal karena duplicate");
        $this->info("Status: " . ($result2['success'] ? 'BERHASIL' : 'GAGAL'));
        $this->info("Pesan: " . $result2['message']);
        
        // 11. Total attendance tetap 1
        $totalAttendance = Attendance::where('graduation_ticket_id', $ticket->id)->count();
        $this->assertEquals(1, $totalAttendance);
        $this->info("Total scan tetap: {$totalAttendance} (tidak bertambah karena duplicate)");
        
        // 12. Simulasi dengan QR Code tidak valid
        $this->info("\n--- TEST QR CODE TIDAK VALID ---");
        $result3 = $this->attendanceService->recordAttendance('INVALID_QR_CODE', $admin);
        
        $this->assertFalse($result3['success'], "Scan invalid seharusnya gagal");
        $this->info("Status: GAGAL");
        $this->info("Pesan: " . $result3['message']);
        
        // 13. Ringkasan
        $this->info("\n=== RINGKASAN SIMULASI ===");
        $this->info("Mahasiswa: {$mahasiswa->nama} ({$mahasiswa->npm})");
        $this->info("Event: {$event->name}");
        $this->info("Total Attendance: {$totalAttendance}");
        $this->info("Status Tiket: " . ($ticket->isExpired() ? 'Expired' : 'Aktif'));
        
        $this->info("\n=== SIMULASI SELESAI ===");
    }

    /**
     * Test: Simulasi scan banyak mahasiswa
     */
    public function test_bulk_scanning_simulation(): void
    {
        $this->info('=== SIMULASI BULK SCANNING ===');
        
        $admin = User::factory()->create(['role' => 'admin']);
        $event = GraduationEvent::factory()->create([
            'status' => 'active',
            'is_active' => true,
            'date' => now(),
        ]);
        
        // Buat 5 mahasiswa
        $mahasiswas = Mahasiswa::factory()->count(5)->create();
        $successCount = 0;
        $failCount = 0;
        
        $this->info("Memproses {$mahasiswas->count()} mahasiswa...\n");
        
        foreach ($mahasiswas as $index => $mhs) {
            // Generate ticket
            $ticket = $this->ticketService->generateTicket($mhs, $event);
            
            // Scan
            $result = $this->attendanceService->recordAttendance($ticket->qr_token_mahasiswa, $admin);
            
            if ($result['success']) {
                $successCount++;
                $this->info(($index + 1) . ". {$mhs->nama} - BERHASIL");
            } else {
                $failCount++;
                $this->info(($index + 1) . ". {$mhs->nama} - GAGAL: {$result['message']}");
            }
        }
        
        $this->info("\n=== HASIL BULK SCANNING ===");
        $this->info("Total: {$mahasiswas->count()}");
        $this->info("Berhasil: {$successCount}");
        $this->info("Gagal: {$failCount}");
        
        // Verifikasi total attendance di database
        $totalDb = Attendance::count();
        $this->assertEquals($successCount, $totalDb);
        $this->info("Total di database: {$totalDb}");
    }

    /**
     * Test: Simulasi scan dengan event tidak aktif
     */
    public function test_scan_with_inactive_event(): void
    {
        $this->info('=== TEST EVENT TIDAK AKTIF ===');
        
        $admin = User::factory()->create(['role' => 'admin']);
        $event = GraduationEvent::factory()->create([
            'status' => 'completed', // Event sudah selesai
            'is_active' => false,
        ]);
        
        $mahasiswa = Mahasiswa::factory()->create();
        $ticket = $this->ticketService->generateTicket($mahasiswa, $event);
        
        // Update ticket supaya tidak expired untuk test ini
        $ticket->update(['expires_at' => now()->addDays(7)]);
        
        $result = $this->attendanceService->recordAttendance($ticket->qr_token_mahasiswa, $admin);
        
        $this->assertFalse($result['success']);
        $this->info("Status: GAGAL (seperti yang diharapkan)");
        $this->info("Pesan: {$result['message']}");
    }
}
