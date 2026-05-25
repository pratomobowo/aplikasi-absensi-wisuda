<?php

namespace App\Console\Commands;

use App\Models\Mahasiswa;
use App\Services\SiakadService;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Storage;

class DiagnosePhotoCommand extends Command
{
    protected $signature = 'diagnose:photo {nim? : NPM/NIM mahasiswa (kosongkan untuk semua)}';
    
    protected $description = 'Diagnose photo download issues';

    public function handle(SiakadService $siakad): int
    {
        $nim = $this->argument('nim');
        
        if ($nim) {
            $this->diagnoseSingle($siakad, $nim);
        } else {
            $this->diagnoseAll($siakad);
        }
        
        return 0;
    }
    
    private function diagnoseSingle(SiakadService $siakad, string $nim): void
    {
        $this->info("=== DIAGNOSIS FOTO NIM: {$nim} ===\n");
        
        $mahasiswa = Mahasiswa::where('npm', $nim)->first();
        
        if (!$mahasiswa) {
            $this->error("❌ Mahasiswa tidak ditemukan");
            return;
        }
        
        $this->info("Nama: {$mahasiswa->nama}");
        $this->info("Foto DB: " . ($mahasiswa->foto_wisuda ?? 'NULL'));
        $this->info("Has Foto: " . ($mahasiswa->hasFotoWisuda() ? '✅ Ya' : '❌ Tidak'));
        $this->newLine();
        
        // Cek file fisik
        if ($mahasiswa->foto_wisuda) {
            $path = 'graduation-photos/' . $mahasiswa->foto_wisuda;
            $exists = Storage::disk('public')->exists($path);
            $this->info("File exists: " . ($exists ? '✅ Ya' : '❌ Tidak'));
            if ($exists) {
                $size = Storage::disk('public')->size($path);
                $this->info("Ukuran: " . round($size/1024, 2) . " KB");
            }
            $this->newLine();
        }
        
        // Test URL
        $url = $siakad->fotoBaseUrl . '/' . $nim . '.jpg';
        $this->info("URL: {$url}");
        $this->newLine();
        
        // Test dengan timeout berbeda
        $timeouts = [5, 10, 15, 30];
        foreach ($timeouts as $timeout) {
            $this->info("Testing dengan timeout {$timeout}d...");
            $start = microtime(true);
            try {
                $response = \Illuminate\Support\Facades\Http::withoutVerifying()
                    ->timeout($timeout)
                    ->head($url);
                $elapsed = round((microtime(true) - $start) * 1000);
                $this->info("  ✅ Response: {$response->status()} ({$elapsed}ms)");
            } catch (\Exception $e) {
                $elapsed = round((microtime(true) - $start) * 1000);
                $this->error("  ❌ Error: " . $e->getMessage() . " ({$elapsed}ms)");
            }
        }
        
        $this->newLine();
        $this->info("=== REKOMENDASI ===");
        
        if (!$mahasiswa->hasFotoWisuda()) {
            $this->warn("⚠️  Foto belum ada di storage");
            $this->info("   Coba jalankan: php artisan sync:photo {$nim}");
        }
    }
    
    private function diagnoseAll(SiakadService $siakad): void
    {
        $this->info("=== DIAGNOSIS SEMUA FOTO ===\n");
        
        $mahasiswas = Mahasiswa::all();
        $total = $mahasiswas->count();
        $withPhoto = 0;
        $withoutPhoto = 0;
        $dbOnly = 0; // DB terisi tapi file tidak ada
        
        foreach ($mahasiswas as $mhs) {
            if ($mhs->foto_wisuda) {
                if ($mhs->hasFotoWisuda()) {
                    $withPhoto++;
                } else {
                    $dbOnly++;
                }
            } else {
                $withoutPhoto++;
            }
        }
        
        $this->info("Total Mahasiswa: {$total}");
        $this->info("✅ Dengan Foto (valid): {$withPhoto}");
        $this->info("⚠️  DB terisi tapi file hilang: {$dbOnly}");
        $this->info("❌ Tanpa Foto: {$withoutPhoto}");
        
        if ($dbOnly > 0) {
            $this->newLine();
            $this->warn("⚠️  Ada {$dbOnly} mahasiswa dengan foto_wisuda terisi tapi file tidak ada di storage!");
            $this->info("   Solusi: Jalankan sync ulang atau fix foto");
        }
    }
}
