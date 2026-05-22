<?php

namespace App\Console\Commands;

use App\Models\Mahasiswa;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Storage;

class FixFotoWisudaCommand extends Command
{
    protected $signature = 'fix:foto-wisuda 
                            {--check-only : Hanya cek, tidak hapus}
                            {--fix-invalid : Hapus foto_wisuda yang file tidak ada}';
    
    protected $description = 'Periksa dan perbaiki data foto_wisuda yang invalid';

    public function handle(): int
    {
        $this->info('=== PEMERIKSAAN DATA FOTO WISUDA ===\n');
        
        $total = Mahasiswa::count();
        $nullCount = Mahasiswa::whereNull('foto_wisuda')->count();
        $notNullCount = Mahasiswa::whereNotNull('foto_wisuda')->count();
        $emptyStringCount = Mahasiswa::where('foto_wisuda', '')->count();
        
        $this->info("Total Mahasiswa: {$total}");
        $this->info("foto_wisuda NULL: {$nullCount}");
        $this->info("foto_wisuda NOT NULL: {$notNullCount}");
        $this->info("foto_wisuda Empty String: {$emptyStringCount}");
        $this->newLine();
        
        // Cek yang foto_wisuda terisi tapi file tidak ada
        $invalidCount = 0;
        $validCount = 0;
        
        Mahasiswa::whereNotNull('foto_wisuda')
            ->where('foto_wisuda', '!=', '')
            ->chunk(100, function ($mahasiswas) use (&$invalidCount, &$validCount) {
                foreach ($mahasiswas as $mhs) {
                    $exists = Storage::disk('public')->exists('graduation-photos/' . $mhs->foto_wisuda);
                    if ($exists) {
                        $validCount++;
                    } else {
                        $invalidCount++;
                        if (!$this->option('check-only')) {
                            $mhs->update(['foto_wisuda' => null]);
                        }
                    }
                }
            });
        
        $this->info("Foto Valid (file ada): {$validCount}");
        $this->info("Foto Invalid (file tidak ada): {$invalidCount}");
        
        if (!$this->option('check-only') && $invalidCount > 0) {
            $this->newLine();
            $this->info("✓ {$invalidCount} data invalid sudah diperbaiki (foto_wisuda di-set NULL)");
        }
        
        // Total tanpa foto yang sebenarnya
        $actualWithoutPhoto = Mahasiswa::whereNull('foto_wisuda')->count() + 
                              Mahasiswa::where('foto_wisuda', '')->count();
        
        $this->newLine();
        $this->info("Total Tanpa Foto (seharusnya): {$actualWithoutPhoto}");
        $this->info("Total Dengan Foto (valid): {$validCount}");
        
        return 0;
    }
}
