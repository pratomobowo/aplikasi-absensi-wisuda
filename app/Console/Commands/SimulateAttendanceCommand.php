<?php

namespace App\Console\Commands;

use App\Models\Attendance;
use App\Models\GraduationEvent;
use App\Models\GraduationTicket;
use App\Models\Mahasiswa;
use App\Models\User;
use App\Services\AttendanceService;
use App\Services\KonsumsiService;
use App\Services\TicketService;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class SimulateAttendanceCommand extends Command
{
    protected $signature = 'simulate:attendance 
                            {scenario=full : Scenario: full, validation, duplicate, expired, manual, bulk, konsumsi}
                            {count=5 : Jumlah mahasiswa untuk scenario bulk}
                            {--skip-scan : Hanya generate data, tidak scan}';
    
    protected $description = 'Simulasi komprehensif scanning kehadiran & konsumsi wisudawan oleh tim admin';
    
    private $attendanceService;
    private $konsumsiService;
    private $ticketService;
    private $admin;
    private $event;
    private $results = [];

    public function handle(TicketService $ticketService, AttendanceService $attendanceService, KonsumsiService $konsumsiService): int
    {
        $this->ticketService = $ticketService;
        $this->attendanceService = $attendanceService;
        $this->konsumsiService = $konsumsiService;
        $scenario = $this->argument('scenario');
        
        $this->newLine();
        $this->info('╔════════════════════════════════════════════════════════════╗');
        $this->info('║       SIMULASI SCANNING KEHADIRAN & KONSUMSI              ║');
        $this->info('╚════════════════════════════════════════════════════════════╝');
        $this->newLine();
        $this->warn("Environment: " . app()->environment());
        $this->newLine();
        
        // Validasi environment
        if (app()->environment('production')) {
            $this->error('Command ini TIDAK BOLEH dijalankan di production!');
            $this->error('Gunakan environment testing/development.');
            return 1;
        }
        
        DB::beginTransaction();
        
        try {
            // Setup admin
            $this->info('[1/3] Setup Admin Scanner...');
            $this->admin = User::firstOrCreate(
                ['email' => 'simulation.scanner@wisuda.test'],
                [
                    'name' => 'Simulation Scanner',
                    'password' => bcrypt('password123'),
                    'role' => 'admin',
                ]
            );
            $this->info("     Admin: {$this->admin->name} (ID: {$this->admin->id})");
            
            // Setup event
            $this->info('[2/3] Setup Event Wisuda...');
            $this->event = GraduationEvent::create([
                'name' => 'SIMULASI WISUDA ' . now()->format('Y-m-d H:i:s'),
                'date' => now(),
                'time' => now()->format('H:i:s'),
                'location_name' => 'Gedung Simulasi',
                'location_address' => 'Jl. Testing No. 123',
                'status' => 'active',
                'is_active' => true,
            ]);
            $this->info("     Event: {$this->event->name} (ID: {$this->event->id})");
            
            $this->newLine();
            
            // Pilih scenario
            switch ($scenario) {
                case 'validation':
                    $this->runValidationTests();
                    break;
                case 'duplicate':
                    $this->runDuplicateTest();
                    break;
                case 'expired':
                    $this->runExpiredTicketTest();
                    break;
                case 'manual':
                    $this->runManualAttendanceTest();
                    break;
                case 'konsumsi':
                    $this->runKonsumsiTest();
                    break;
                case 'bulk':
                    $this->runBulkScan((int)$this->argument('count'));
                    break;
                case 'full':
                default:
                    $this->runFullSimulation();
                    break;
            }
            
            // Tampilkan ringkasan akhir
            $this->showFinalSummary();
            
            // Cleanup
            $this->newLine();
            if ($this->confirm('Hapus semua data simulasi?', true)) {
                DB::rollBack();
                $this->info('✓ Data simulasi dihapus (rollback).');
            } else {
                DB::commit();
                $this->info('✓ Data simulasi disimpan.');
                $this->info("  Event ID: {$this->event->id}");
            }
            
            return 0;
            
        } catch (\Exception $e) {
            DB::rollBack();
            $this->newLine();
            $this->error('═══════════════════════════════════════');
            $this->error('ERROR: ' . $e->getMessage());
            $this->error('═══════════════════════════════════════');
            $this->error($e->getTraceAsString());
            return 1;
        }
    }
    
    /**
     * Scenario 1: Full Simulation (Semua test)
     */
    private function runFullSimulation(): void
    {
        $this->info('[3/3] Menjalankan SIMULASI LENGKAP...');
        $this->newLine();
        
        $this->info('═══════════════════════════════════════════════════════════');
        $this->info('SCENARIO 1: Validasi QR Code');
        $this->info('═══════════════════════════════════════════════════════════');
        $this->runValidationTests();
        
        $this->info('═══════════════════════════════════════════════════════════');
        $this->info('SCENARIO 2: Scan Berhasil & Duplicate');
        $this->info('═══════════════════════════════════════════════════════════');
        $this->runDuplicateTest();
        
        $this->info('═══════════════════════════════════════════════════════════');
        $this->info('SCENARIO 3: Tiket Expired');
        $this->info('═══════════════════════════════════════════════════════════');
        $this->runExpiredTicketTest();
        
        $this->info('═══════════════════════════════════════════════════════════');
        $this->info('SCENARIO 4: Attendance Manual');
        $this->info('═══════════════════════════════════════════════════════════');
        $this->runManualAttendanceTest();
        
        $this->info('═══════════════════════════════════════════════════════════');
        $this->info('SCENARIO 5: Bulk Scanning');
        $this->info('═══════════════════════════════════════════════════════════');
        $this->runBulkScan(5);
        
        $this->info('═══════════════════════════════════════════════════════════');
        $this->info('SCENARIO 6: Scan Konsumsi');
        $this->info('═══════════════════════════════════════════════════════════');
        $this->runKonsumsiTest();
    }
    
    /**
     * Test: Validasi QR Code (Valid vs Invalid)
     */
    private function runValidationTests(): void
    {
        $this->info("\n📝 TEST VALIDASI QR CODE\n");
        
        // Test 1: QR Kosong
        $this->info('Test 1: QR Code Kosong');
        $result = $this->attendanceService->recordAttendance('', $this->admin);
        $this->validateResult($result, false, 'QR kosong harus ditolak');
        
        // Test 2: QR Random/Invalid
        $this->info('Test 2: QR Code Random (Invalid)');
        $result = $this->attendanceService->recordAttendance('RANDOM_QR_CODE_12345', $this->admin);
        $this->validateResult($result, false, 'QR invalid harus ditolak');
        
        // Test 3: QR Format Salah
        $this->info('Test 3: QR Code Format Salah');
        $result = $this->attendanceService->recordAttendance(json_encode(['invalid' => 'data']), $this->admin);
        $this->validateResult($result, false, 'QR format salah harus ditolak');
        
        // Test 4: QR Valid
        $this->info('Test 4: QR Code Valid');
        $mhs = $this->createMahasiswa('VALID001', 'Mahasiswa Valid Test');
        $ticket = $this->ticketService->generateTicket($mhs, $this->event);
        $result = $this->attendanceService->recordAttendance($ticket->qr_token_mahasiswa, $this->admin);
        $this->validateResult($result, true, 'QR valid harus diterima');
        
        if ($result['success']) {
            $this->info("     Nama: {$result['data']['nama']}");
            $this->info("     NPM: {$result['data']['npm']}");
            $this->info("     Role: {$result['data']['role']}");
        }
    }
    
    /**
     * Test: Duplicate Scan Prevention
     */
    private function runDuplicateTest(): void
    {
        $this->info("\n📝 TEST DUPLICATE SCAN\n");
        
        $mhs = $this->createMahasiswa('DUP001', 'Mahasiswa Duplicate Test');
        $ticket = $this->ticketService->generateTicket($mhs, $this->event);
        
        // Scan pertama (harus berhasil)
        $this->info('Scan 1 (Pertama):');
        $result1 = $this->attendanceService->recordAttendance($ticket->qr_token_mahasiswa, $this->admin);
        $this->validateResult($result1, true, 'Scan pertama harus berhasil');
        
        // Scan kedua (harus gagal - duplicate)
        $this->info('Scan 2 (Duplicate):');
        $result2 = $this->attendanceService->recordAttendance($ticket->qr_token_mahasiswa, $this->admin);
        $this->validateResult($result2, false, 'Scan kedua harus ditolak (duplicate)');
        
        if (!$result2['success']) {
            $this->info("     Alasan: {$result2['message']}");
        }
        
        // Verifikasi hanya 1 record di database
        $count = Attendance::where('graduation_ticket_id', $ticket->id)->count();
        $this->info("     Total record di DB: {$count} (harusnya 1)");
        
        if ($count === 1) {
            $this->info("     ✓ Validasi duplicate berhasil");
        } else {
            $this->error("     ✗ Validasi duplicate GAGAL");
        }
    }
    
    /**
     * Test: Expired Ticket
     */
    private function runExpiredTicketTest(): void
    {
        $this->info("\n📝 TEST TIKET EXPIRED\n");
        
        $mhs = $this->createMahasiswa('EXP001', 'Mahasiswa Expired Test');
        $ticket = $this->ticketService->generateTicket($mhs, $this->event);
        
        // Expired tiket (set tanggal ke masa lalu)
        $ticket->update(['expires_at' => now()->subDays(1)]);
        $this->info("Tiket di-set expired (1 hari yang lalu)");
        
        $result = $this->attendanceService->recordAttendance($ticket->qr_token_mahasiswa, $this->admin);
        $this->validateResult($result, false, 'Tiket expired harus ditolak');
        
        if (!$result['success']) {
            $this->info("     Alasan: {$result['message']}");
        }
    }
    
    /**
     * Test: Manual Attendance (fallback tanpa QR)
     */
    private function runManualAttendanceTest(): void
    {
        $this->info("\n📝 TEST ATTENDANCE MANUAL\n");
        
        $mhs = $this->createMahasiswa('MAN001', 'Mahasiswa Manual Test');
        $ticket = $this->ticketService->generateTicket($mhs, $this->event);
        
        // Attendance manual berdasarkan NPM
        $this->info('Attendance manual via NPM:');
        $result = $this->attendanceService->recordManualAttendance($mhs->npm, $this->event->id, $this->admin);
        
        if ($result['success']) {
            $this->info("     ✓ Berhasil: {$result['message']}");
            $this->info("     Nama: {$result['data']['nama']}");
            $this->info("     NPM: {$result['data']['npm']}");
        } else {
            $this->error("     ✗ Gagal: {$result['message']}");
        }
        
        // Test duplicate manual attendance
        $this->info('Duplicate manual attendance:');
        $result2 = $this->attendanceService->recordManualAttendance($mhs->npm, $this->event->id, $this->admin);
        $this->validateResult($result2, false, 'Duplicate manual harus ditolak');
    }
    
    /**
     * Test: Bulk Scanning
     */
    private function runBulkScan(int $count): void
    {
        $this->info("\n📝 TEST BULK SCANNING ({$count} mahasiswa)\n");
        
        $progressBar = $this->output->createProgressBar($count);
        $progressBar->start();
        
        $mahasiswas = [];
        $tickets = [];
        
        // Generate data
        for ($i = 1; $i <= $count; $i++) {
            $mhs = $this->createMahasiswa(
                'BULK' . str_pad($i, 3, '0', STR_PAD_LEFT),
                "Mahasiswa Bulk {$i}"
            );
            $ticket = $this->ticketService->generateTicket($mhs, $this->event);
            
            $mahasiswas[] = $mhs;
            $tickets[] = $ticket;
            $progressBar->advance();
        }
        
        $progressBar->finish();
        $this->newLine(2);
        
        // Scan semua
        $this->info('Scanning semua mahasiswa...');
        $success = 0;
        $failed = 0;
        
        foreach ($tickets as $index => $ticket) {
            $result = $this->attendanceService->recordAttendance($ticket->qr_token_mahasiswa, $this->admin);
            
            if ($result['success']) {
                $success++;
            } else {
                $failed++;
                $this->error("     Gagal #{$index}: {$result['message']}");
            }
        }
        
        $this->info("     Berhasil: {$success}");
        $this->info("     Gagal: {$failed}");
        $this->info("     Persentase: " . round(($success / $count) * 100, 1) . '%');
    }
    
    /**
     * Create single mahasiswa
     */
    /**
     * Test: Scan Konsumsi (Makan Siang/Malam)
     */
    private function runKonsumsiTest(): void
    {
        $this->info("\n📝 TEST SCAN KONSUMSI\n");
        
        // Test 1: Konsumsi tanpa attendance (harus gagal - belum hadir)
        $this->info('Test 1: Konsumsi tanpa Attendance');
        $mhs1 = $this->createMahasiswa('KON001', 'Mahasiswa Konsumsi Test 1');
        $ticket1 = $this->ticketService->generateTicket($mhs1, $this->event);
        
        // Langsung scan konsumsi tanpa attendance
        $result1 = $this->konsumsiService->recordKonsumsi($ticket1->qr_token_mahasiswa, $this->admin);
        $this->validateResult($result1, false, 'Konsumsi tanpa attendance harus ditolak');
        
        if (!$result1['success']) {
            $this->info("     Alasan: {$result1['message']}");
        }
        
        // Test 2: Konsumsi setelah attendance (harus berhasil)
        $this->info('Test 2: Konsumsi setelah Attendance');
        $mhs2 = $this->createMahasiswa('KON002', 'Mahasiswa Konsumsi Test 2');
        $ticket2 = $this->ticketService->generateTicket($mhs2, $this->event);
        
        // Scan attendance dulu
        $attResult = $this->attendanceService->recordAttendance($ticket2->qr_token_mahasiswa, $this->admin);
        if ($attResult['success']) {
            $this->info("     ✓ Attendance berhasil");
            
            // Lalu scan konsumsi
            $result2 = $this->konsumsiService->recordKonsumsi($ticket2->qr_token_mahasiswa, $this->admin);
            $this->validateResult($result2, true, 'Konsumsi setelah attendance harus berhasil');
            
            if ($result2['success']) {
                $this->info("     Nama: {$result2['data']['nama']}");
                $this->info("     Status: {$result2['data']['status']}");
            }
        }
        
        // Test 3: Duplicate konsumsi (harus gagal)
        $this->info('Test 3: Duplicate Konsumsi');
        $result3 = $this->konsumsiService->recordKonsumsi($ticket2->qr_token_mahasiswa, $this->admin);
        $this->validateResult($result3, false, 'Duplicate konsumsi harus ditolak');
        
        if (!$result3['success']) {
            $this->info("     Alasan: {$result3['message']}");
        }
        
        // Verifikasi status di database
        $ticket2->refresh();
        $this->info("     Status konsumsi di DB: " . ($ticket2->konsumsi_diterima ? 'Sudah' : 'Belum'));
        $this->info("     Waktu konsumsi: " . ($ticket2->konsumsi_at ? $ticket2->konsumsi_at->format('H:i:s') : '-'));
    }
    
    private function createMahasiswa(string $npm, string $nama): Mahasiswa
    {
        return Mahasiswa::create([
            'npm' => $npm,
            'nama' => $nama,
            'program_studi' => $this->getRandomProdi(),
            'ipk' => round(mt_rand(275, 400) / 100, 2),
            'yudisium' => $this->getRandomYudisium(),
            'password' => bcrypt('password123'),
        ]);
    }
    
    /**
     * Validate test result
     */
    private function validateResult(array $result, bool $expectedSuccess, string $description): void
    {
        $pass = $result['success'] === $expectedSuccess;
        $status = $pass ? '✓ PASS' : '✗ FAIL';
        $color = $pass ? 'info' : 'error';
        
        $this->{$color}("     {$status} - {$description}");
        
        if (!$pass) {
            $this->error("     Expected: " . ($expectedSuccess ? 'Success' : 'Failed'));
            $this->error("     Actual: " . ($result['success'] ? 'Success' : 'Failed'));
            $this->error("     Message: {$result['message']}");
        }
        
        $this->results[] = [
            'test' => $description,
            'status' => $pass ? 'PASS' : 'FAIL',
            'expected' => $expectedSuccess ? 'Success' : 'Failed',
            'actual' => $result['success'] ? 'Success' : 'Failed',
            'message' => $result['message'] ?? '',
        ];
    }
    
    /**
     * Show final summary
     */
    private function showFinalSummary(): void
    {
        $total = count($this->results);
        $pass = count(array_filter($this->results, fn($r) => $r['status'] === 'PASS'));
        $fail = $total - $pass;
        
        $this->newLine();
        $this->info('╔════════════════════════════════════════════════════════════╗');
        $this->info('║                   RINGKASAN TEST                          ║');
        $this->info('╚════════════════════════════════════════════════════════════╝');
        $this->newLine();
        
        $this->table(
            ['Test', 'Status', 'Expected', 'Actual', 'Message'],
            $this->results
        );
        
        $this->newLine();
        $this->info('═══════════════════════════════════════════════════════════');
        $this->info("Total Test: {$total}");
        $this->info("Pass: {$pass} ✅");
        $this->info("Fail: {$fail} ❌");
        $this->info("Persentase: " . ($total > 0 ? round(($pass / $total) * 100, 1) : 0) . '%');
        $this->info('═══════════════════════════════════════════════════════════');
        
        // Statistik attendance di DB
        $totalAttendance = Attendance::whereHas('graduationTicket', function ($q) {
            $q->where('graduation_event_id', $this->event->id);
        })->count();
        
        $this->newLine();
        
        // Statistik Konsumsi
        $totalKonsumsi = \App\Models\GraduationTicket::where('graduation_event_id', $this->event->id)
            ->where('konsumsi_diterima', true)
            ->count();
        
        $this->info("📊 STATISTIK:");
        $this->info("   Total Attendance: {$totalAttendance}");
        $this->info("   Total Konsumsi: {$totalKonsumsi}");
        $this->info("   Event: {$this->event->name}");
    }
    
    private function getRandomProdi(): string
    {
        $prodis = [
            'Teknik Informatika', 'Sistem Informasi', 'Manajemen',
            'Akuntansi', 'Hukum', 'Psikologi', 'Kedokteran', 'Farmasi'
        ];
        return $prodis[array_rand($prodis)];
    }
    
    private function getRandomYudisium(): ?string
    {
        $yudisiums = ['Cum Laude', 'Dengan Pujian', 'Sangat Memuaskan', 'Memuaskan', null];
        return $yudisiums[array_rand($yudisiums)];
    }
}
