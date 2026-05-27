<?php

namespace App\Console\Commands;

use App\Services\SiakadService;
use Illuminate\Console\Command;

class DebugSiakadFieldsCommand extends Command
{
    protected $signature = 'debug:siakad-fields {periode=20251 : Periode akademik}';
    
    protected $description = 'Debug: Cek semua field yang tersedia dari API SIAKAD';

    public function handle(SiakadService $siakad): int
    {
        $periode = $this->argument('periode');
        
        $this->info("=== DEBUG FIELD SIAKAD ===");
        $this->info("Periode: {$periode}\n");
        
        try {
            $data = $siakad->fetchKelulusan($periode);
            
            if (empty($data)) {
                $this->error("❌ Tidak ada data dari SIAKAD");
                return 1;
            }
            
            $this->info("✓ Total data ditemukan: " . count($data) . "\n");
            
            // Ambil 3 sample data
            $samples = array_slice($data, 0, 3);
            
            foreach ($samples as $index => $item) {
                $attr = $item['attributes'] ?? [];
                
                $this->info("--- Sample Data #" . ($index + 1) . " ---");
                $this->info("NIM: " . ($attr['nim'] ?? 'N/A'));
                $this->info("Nama: " . ($attr['nama'] ?? 'N/A'));
                $this->info("\nSemua Field:");
                
                foreach ($attr as $key => $value) {
                    $displayValue = '';
                    if (is_null($value)) {
                        $displayValue = 'NULL';
                    } elseif (is_string($value)) {
                        $displayValue = strlen($value) > 100 ? substr($value, 0, 100) . '...' : $value;
                    } elseif (is_array($value)) {
                        $displayValue = json_encode($value);
                    } else {
                        $displayValue = (string) $value;
                    }
                    
                    $this->info("  {$key}: {$displayValue}");
                }
                
                $this->info("");
            }
            
            // Cari field yang mungkin terkait judul skripsi
            $this->info("\n=== CARI FIELD JUDUL SKRIPSI ===");
            $firstAttr = $data[0]['attributes'] ?? [];
            $possibleFields = [];
            
            foreach ($firstAttr as $key => $value) {
                $lowerKey = strtolower($key);
                if (strpos($lowerKey, 'judul') !== false || 
                    strpos($lowerKey, 'skripsi') !== false ||
                    strpos($lowerKey, 'tugas') !== false ||
                    strpos($lowerKey, 'akhir') !== false ||
                    strpos($lowerKey, 'thesis') !== false ||
                    strpos($lowerKey, 'title') !== false) {
                    $possibleFields[$key] = $value;
                }
            }
            
            if (empty($possibleFields)) {
                $this->warn("⚠️  Tidak ditemukan field yang mengandung kata 'judul', 'skripsi', 'tugas', 'akhir', 'thesis', atau 'title'");
                $this->info("\nField yang tersedia (sorted):");
                $keys = array_keys($firstAttr);
                sort($keys);
                foreach ($keys as $key) {
                    $this->info("  - {$key}");
                }
            } else {
                $this->info("✓ Ditemukan field yang mungkin:");
                foreach ($possibleFields as $key => $value) {
                    $displayValue = is_string($value) ? (strlen($value) > 100 ? substr($value, 0, 100) . '...' : $value) : json_encode($value);
                    $this->info("  {$key}: {$displayValue}");
                }
            }
            
            return 0;
            
        } catch (\Exception $e) {
            $this->error("❌ Error: " . $e->getMessage());
            return 1;
        }
    }
}
