<?php

namespace App\Console\Commands;

use App\Services\SiakadService;
use Illuminate\Console\Command;

class DebugSevimaFields extends Command
{
    protected $signature = 'debug:sevima-fields {npm? : NPM mahasiswa untuk check}';

    protected $description = 'Debug Sevima API fields untuk melihat semua data yang diberikan';

    public function handle(SiakadService $siakad)
    {
        $npm = $this->argument('npm');

        if ($npm) {
            $this->info("Fetching data untuk NPM: {$npm}");
            $data = $siakad->fetchMahasiswaByNim($npm);
        } else {
            $this->info("Fetching sample data dari /kelulusan...");
            $data = $siakad->fetchKelulusan();
        }

        if (empty($data)) {
            $this->error("Tidak ada data dari API");
            return;
        }

        $this->info("Jumlah data: " . count($data));

        // Ambil 1 sample
        $sample = $data[0] ?? $data;

        $this->newLine();
        $this->info("=== SAMPLE DATA ===");
        $this->info("Nama: " . ($sample['attributes']['nama'] ?? 'N/A'));
        $this->info("NPM: " . ($sample['attributes']['npm'] ?? 'N/A'));
        
        $this->newLine();
        $this->info("=== ALL FIELDS ===");
        
        $attributes = $sample['attributes'] ?? [];
        
        if (empty($attributes)) {
            // Jika data langsung array tanpa 'attributes'
            $attributes = $sample;
        }

        foreach ($attributes as $key => $value) {
            $displayValue = is_array($value) ? json_encode($value) : $value;
            $this->line(str_pad($key, 30) . ": " . $displayValue);
        }

        $this->newLine();
        $this->info("=== JSON STRUCTURE ===");
        $this->line(json_encode($sample, JSON_PRETTY_PRINT));

        // Check specific fields
        $this->newLine();
        $this->info("=== FIELD CHECK ===");
        $this->line("jenjang: " . ($attributes['jenjang'] ?? 'TIDAK ADA'));
        $this->line("id_jenjang: " . ($attributes['id_jenjang'] ?? 'TIDAK ADA'));
        $this->line("program_studi: " . ($attributes['program_studi'] ?? 'TIDAK ADA'));
        $this->line("nama_program_studi: " . ($attributes['nama_program_studi'] ?? 'TIDAK ADA'));
        $this->line("fakultas: " . ($attributes['fakultas'] ?? 'TIDAK ADA'));
    }
}
