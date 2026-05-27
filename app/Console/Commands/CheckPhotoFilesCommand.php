<?php

namespace App\Console\Commands;

use App\Models\Mahasiswa;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Storage;

class CheckPhotoFilesCommand extends Command
{
    protected $signature = 'check:photos {nim? : Cek foto untuk NIM tertentu}';
    
    protected $description = 'Cek file foto yang tersimpan di storage';

    public function handle(): int
    {
        $nim = $this->argument('nim');
        
        if ($nim) {
            $this->checkSingle($nim);
        } else {
            $this->checkAll();
        }
        
        return 0;
    }
    
    private function checkSingle(string $nim): void
    {
        $this->info("=== CEK FOTO NIM: {$nim} ===\n");
        
        $mahasiswa = Mahasiswa::where('npm', $nim)->first();
        
        if (!$mahasiswa) {
            $this->error("❌ Mahasiswa tidak ditemukan");
            return;
        }
        
        $this->info("Nama: {$mahasiswa->nama}");
        $this->info("Foto DB: " . ($mahasiswa->foto_wisuda ?? 'NULL'));
        $this->info("HasFotoWisuda: " . ($mahasiswa->hasFotoWisuda() ? '✅ Ya' : '❌ Tidak'));
        $this->newLine();
        
        if ($mahasiswa->foto_wisuda) {
            $path = 'graduation-photos/' . $mahasiswa->foto_wisuda;
            $disk = Storage::disk('public');
            
            $this->info("Path: {$path}");
            $this->info("Full Path: " . $disk->path($path));
            $this->info("Exists: " . ($disk->exists($path) ? '✅ Ya' : '❌ Tidak'));
            
            if ($disk->exists($path)) {
                $size = $disk->size($path);
                $this->info("Size: " . round($size / 1024, 2) . " KB");
                
                $mime = mime_content_type($disk->path($path));
                $this->info("MIME Type: {$mime}");
                
                if (!str_starts_with($mime, 'image/')) {
                    $this->error("⚠️  File bukan gambar! Mungkin HTML error page.");
                    $content = $disk->get($path);
                    $this->info("Preview: " . substr($content, 0, 100));
                }
            }
        }
        
        $this->newLine();
        $this->info("URL: " . $mahasiswa->foto_wisuda_url);
        
        // Cek storage:link
        $this->newLine();
        $this->info("=== CEK STORAGE LINK ===");
        $publicLink = public_path('storage');
        $this->info("Symlink: {$publicLink}");
        $this->info("Symlink exists: " . (is_link($publicLink) ? '✅ Ya' : '❌ Tidak'));
        if (is_link($publicLink)) {
            $this->info("Symlink target: " . readlink($publicLink));
        }
    }
    
    private function checkAll(): void
    {
        $this->info("=== CEK SEMUA FOTO ===\n");
        
        $mahasiswas = Mahasiswa::whereNotNull('foto_wisuda')->get();
        $disk = Storage::disk('public');
        
        $valid = 0;
        $invalid = 0;
        $notImage = 0;
        
        foreach ($mahasiswas as $mhs) {
            $path = 'graduation-photos/' . $mhs->foto_wisuda;
            
            if (!$disk->exists($path)) {
                $invalid++;
                $this->warn("❌ {$mhs->npm} - File tidak ada: {$mhs->foto_wisuda}");
                continue;
            }
            
            $mime = mime_content_type($disk->path($path));
            if (!str_starts_with($mime, 'image/')) {
                $notImage++;
                $this->error("⚠️  {$mhs->npm} - Bukan gambar ({$mime}): {$mhs->foto_wisuda}");
                continue;
            }
            
            $valid++;
        }
        
        $this->newLine();
        $this->info("=== RINGKASAN ===");
        $this->info("Total dengan foto_wisuda: {$mahasiswas->count()}");
        $this->info("✅ Valid (gambar): {$valid}");
        $this->info("❌ File tidak ada: {$invalid}");
        $this->info("⚠️  Bukan gambar: {$notImage}");
    }
}
