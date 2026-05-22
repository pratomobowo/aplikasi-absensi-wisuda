<?php

namespace App\Console\Commands;

use App\Models\Attendance;
use App\Models\GraduationEvent;
use App\Models\GraduationTicket;
use App\Models\Mahasiswa;
use App\Models\User;
use App\Services\AttendanceService;
use App\Services\TicketService;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class SimulateAttendanceCommand extends Command
{
    protected $signature = 'simulate:attendance 
                            {count=5 : Jumlah mahasiswa yang akan disimulasikan}
                            {--skip-scan : Hanya generate data, tidak scan}';
    
    protected $description = 'Simulasi scanning kehadiran wisudawan oleh tim admin';

    public function handle(TicketService $ticketService, AttendanceService $attendanceService): int
    {
        $count = (int) $this->argument('count');
        $skipScan = $this->option('skip-scan');
        
        $this->newLine();
        $this->info('╔════════════════════════════════════════════════════════════╗');
        $this->info('║       SIMULASI SCANNING KEHADIRAN WISUDAWAN               ║');
        $this->info('╚════════════════════════════════════════════════════════════╝');
        $this->newLine();
        
        // Cek apakah running di production
        if (app()->environment('production')) {
            $this->error('Command ini tidak boleh dijalankan di production!');
            return 1;
        }
        
        DB::beginTransaction();
        
        try {
            // 1. Buat atau ambil admin
            $admin = User::firstOrCreate(
                ['email' => 'scanner@simulation.test'],
                [
                    'name' => 'Admin Scanner',
                    'password' => bcrypt('password'),
                    'role' => 'admin',
                ]
            );
            $this->info("Admin: {$admin->name}");
            
            // 2. Buat event wisuda
            $event = GraduationEvent::create([
                'name' => 'WISUDA SIMULASI ' . now()->format('Y-m-d H:i:s'),
                'date' => now(),
                'time' => now()->format('H:i:s'),
                'location_name' => 'Gedung Serba Guna',
                'status' => 'active',
                'is_active' => true,
            ]);
            $this->info("Event: {$event->name}");
            $this->newLine();
            
            // 3. Generate mahasiswa dummy
            $this->info("Generating {$count} mahasiswa...");
            $progressBar = $this->output->createProgressBar($count);
            $progressBar->start();
            
            $mahasiswas = [];
            $tickets = [];
            
            for ($i = 1; $i <= $count; $i++) {
                $mahasiswa = Mahasiswa::create([
                    'npm' => '2114' . str_pad($i, 6, '0', STR_PAD_LEFT),
                    'nama' => "Mahasiswa Simulasi {$i}",
                    'program_studi' => $this->getRandomProdi(),
                    'ipk' => round(rand(275, 400) / 100, 2),
                    'yudisium' => $this->getRandomYudisium(),
                    'password' => bcrypt('password'),
                ]);
                
                $ticket = $ticketService->generateTicket($mahasiswa, $event);
                
                $mahasiswas[] = $mahasiswa;
                $tickets[] = $ticket;
                
                $progressBar->advance();
            }
            
            $progressBar->finish();
            $this->newLine(2);
            
            if ($skipScan) {
                $this->info('Mode: Hanya generate data (skip scanning)');
                $this->info("Total data: {$count} mahasiswa + {$count} tiket");
                DB::commit();
                return 0;
            }
            
            // 4. Simulasi Scanning
            $this->info('═══════════════════════════════════════════════════════════');
            $this->info('MULAI SCANNING...');
            $this->info('═══════════════════════════════════════════════════════════');
            $this->newLine();
            
            $successCount = 0;
            $failCount = 0;
            
            foreach ($mahasiswas as $index => $mhs) {
                $ticket = $tickets[$index];
                
                // Scan QR
                $result = $attendanceService->recordAttendance($ticket->qr_token_mahasiswa, $admin);
                
                $status = $result['success'] ? '✓ HADIR' : '✗ GAGAL';
                $color = $result['success'] ? 'info' : 'error';
                
                $this->{$color}(sprintf(
                    "%3d. %-25s | %-15s | %s",
                    $index + 1,
                    $mhs->nama,
                    $mhs->npm,
                    $status
                ));
                
                if (!$result['success']) {
                    $this->error("     → {$result['message']}");
                    $failCount++;
                } else {
                    $successCount++;
                }
                
                // Delay 100ms untuk simulasi realistis
                usleep(100000);
            }
            
            // 5. Ringkasan
            $this->newLine();
            $this->info('═══════════════════════════════════════════════════════════');
            $this->info('RINGKASAN');
            $this->info('═══════════════════════════════════════════════════════════');
            $this->table(
                ['Metrik', 'Nilai'],
                [
                    ['Total Mahasiswa', $count],
                    ['Berhasil Scan', $successCount],
                    ['Gagal Scan', $failCount],
                    ['Persentase Kehadiran', round(($successCount / $count) * 100, 1) . '%'],
                ]
            );
            
            // 6. Detail Attendance
            $attendances = Attendance::whereHas('graduationTicket', function ($q) use ($event) {
                $q->where('graduation_event_id', $event->id);
            })->get();
            
            $this->newLine();
            $this->info("Total record attendance: {$attendances->count()}");
            
            // Tampilkan sample attendance
            if ($attendances->isNotEmpty()) {
                $this->newLine();
                $this->info('Sample Data Kehadiran:');
                $sampleData = $attendances->take(3)->map(function ($att) {
                    return [
                        'Nama' => $att->graduationTicket->mahasiswa->nama ?? 'N/A',
                        'NPM' => $att->graduationTicket->mahasiswa->npm ?? 'N/A',
                        'Waktu Scan' => $att->scanned_at->format('d M Y H:i:s'),
                        'Scanner' => $att->scannedBy->name ?? 'System',
                    ];
                });
                $this->table(['Nama', 'NPM', 'Waktu Scan', 'Scanner'], $sampleData->toArray());
            }
            
            // Test duplicate scan
            $this->newLine();
            $this->info('═══════════════════════════════════════════════════════════');
            $this->info('TEST DUPLICATE SCAN');
            $this->info('═══════════════════════════════════════════════════════════');
            
            $firstTicket = $tickets[0];
            $dupResult = $attendanceService->recordAttendance($firstTicket->qr_token_mahasiswa, $admin);
            
            if (!$dupResult['success']) {
                $this->info("✓ Duplicate scan dicegah: {$dupResult['message']}");
            } else {
                $this->warn("⚠ Duplicate scan berhasil (tidak diharapkan)");
            }
            
            // Cleanup prompt
            $this->newLine(2);
            if ($this->confirm('Hapus data simulasi?')) {
                DB::rollBack();
                $this->info('Data simulasi dihapus.');
            } else {
                DB::commit();
                $this->info('Data simulasi disimpan.');
                $this->info("Event ID: {$event->id}");
            }
            
            return 0;
            
        } catch (\Exception $e) {
            DB::rollBack();
            $this->error('Error: ' . $e->getMessage());
            $this->error($e->getTraceAsString());
            return 1;
        }
    }
    
    private function getRandomProdi(): string
    {
        $prodis = [
            'Teknik Informatika',
            'Sistem Informasi',
            'Manajemen',
            'Akuntansi',
            'Hukum',
            'Psikologi',
            'Kedokteran',
            'Farmasi',
        ];
        
        return $prodis[array_rand($prodis)];
    }
    
    private function getRandomYudisium(): ?string
    {
        $yudisiums = [
            'Cum Laude',
            'Dengan Pujian',
            'Sangat Memuaskan',
            'Memuaskan',
            null,
        ];
        
        return $yudisiums[array_rand($yudisiums)];
    }
}
