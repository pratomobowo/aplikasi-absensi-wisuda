<?php

namespace App\Console\Commands;

use App\Models\Mahasiswa;
use App\Services\SiakadService;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Storage;

class SyncPhotoByNimCommand extends Command
{
    protected $signature = 'sync:photo {nim : NPM/NIM mahasiswa}';
    
    protected $description = 'Sync foto wisuda untuk satu mahasiswa berdasarkan NIM';

    public function handle(SiakadService $siakad): int
    {
        $nim = $this->argument('nim');
        
        $this->info("=== SYNC FOTO UNTUK NIM: {$nim} ===\n");
        
        // Cek mahasiswa di database
        $mahasiswa = Mahasiswa::where('npm', $nim)->first();
        
        if (!$mahasiswa) {
            $this->error("❌ Mahasiswa dengan NIM {$nim} tidak ditemukan di database");
            return 1;
        }
        
        $this->info("📋 Data Mahasiswa:");
        $this->info("   Nama: {$mahasiswa->nama}");
        $this->info("   Prodi: {$mahasiswa->program_studi}");
        $this->info("   Foto saat ini: " . ($mahasiswa->foto_wisuda ?? 'Belum ada'));
        $this->newLine();
        
        // Cek foto existing
        if ($mahasiswa->foto_wisuda) {
            $oldPath = 'graduation-photos/' . $mahasiswa->foto_wisuda;
            if (Storage::disk('public')->exists($oldPath)) {
                $size = Storage::disk('public')->size($oldPath);
                $this->warn("⚠️  Foto lama ditemukan: {$mahasiswa->foto_wisuda} (" . round($size / 1024, 2) . " KB)");
                $this->warn("   Foto lama akan dihapus dan diganti dengan yang baru");
                $this->newLine();
            }
        }
        
        // Step 1: Cek apakah foto tersedia di server
        $this->info("🔍 Step 1: Mengecek ketersediaan foto di server SEVIMA...");
        $exists = $siakad->checkFotoExists($nim);
        
        if (!$exists) {
            $this->error("❌ Foto tidak tersedia di server SEVIMA untuk NIM {$nim}");
            $this->info("   URL: {$siakad->fotoBaseUrl}/{$nim}.jpg");
            return 1;
        }
        
        $this->info("✅ Foto tersedia di server\n");
        
        // Step 2: Download foto
        $this->info("⬇️  Step 2: Mendownload foto...");
        
        try {
            $fotoPath = $siakad->downloadFoto($nim);
            
            if (!$fotoPath) {
                $this->error("❌ Gagal mendownload foto");
                return 1;
            }
            
            $this->info("✅ Foto berhasil didownload: {$fotoPath}");
            
            // Cek ukuran file
            $fullPath = Storage::disk('public')->path($fotoPath);
            $size = filesize($fullPath);
            $this->info("   Ukuran: " . round($size / 1024, 2) . " KB");
            
            // Step 3: Update database
            $this->newLine();
            $this->info("💾 Step 3: Update database...");
            
            $mahasiswa->update(['foto_wisuda' => basename($fotoPath)]);
            
            $this->info("✅ Database updated!");
            $this->info("   foto_wisuda = " . basename($fotoPath));
            
            // Verifikasi
            $this->newLine();
            $this->info("🔍 Verifikasi:");
            $mahasiswa->refresh();
            
            if ($mahasiswa->hasFotoWisuda()) {
                $this->info("✅ Foto tersimpan dengan benar");
                $this->info("   Path: " . $mahasiswa->getFotoWisudaUrlAttribute());
            } else {
                $this->error("❌ Foto tidak ditemukan setelah update");
                return 1;
            }
            
            $this->newLine();
            $this->info("🎉 Sync foto berhasil!");
            
            return 0;
            
        } catch (\Exception $e) {
            $this->error("❌ Error: " . $e->getMessage());
            return 1;
        }
    }
}
