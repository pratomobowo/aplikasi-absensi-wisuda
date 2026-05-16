<?php

namespace App\Services;

use App\Models\BukuWisuda;
use App\Models\GraduationEvent;
use App\Models\Mahasiswa;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Spatie\Browsershot\Browsershot;

class BukuWisudaService
{
    /**
     * Get all mahasiswa for a graduation event
     */
    public function getMahasiswaForEvent(GraduationEvent $event)
    {
        return Mahasiswa::query()
            ->orderBy('program_studi', 'asc')
            ->orderBy('nama', 'asc')
            ->get();
    }

    /**
     * Generate preview HTML for buku wisuda
     */
    public function generatePreview(GraduationEvent $event): array
    {
        $mahasiswa = $this->getMahasiswaForEvent($event);
        
        return [
            'event' => $event,
            'mahasiswa' => $mahasiswa,
            'total' => $mahasiswa->count(),
        ];
    }

    /**
     * Group mahasiswa by program studi
     */
    public function groupByJurusan($mahasiswa): array
    {
        $grouped = [];
        foreach ($mahasiswa as $mhs) {
            $jurusan = $mhs->program_studi ?? 'Belum diisi';
            if (!isset($grouped[$jurusan])) {
                $grouped[$jurusan] = [];
            }
            $grouped[$jurusan][] = $mhs;
        }
        return $grouped;
    }

    /**
     * Generate PDF for buku wisuda using Browsershot
     */
    public function generatePdf(GraduationEvent $event, string $generatedBy): BukuWisuda
    {
        $mahasiswa = $this->getMahasiswaForEvent($event);
        $grouped = $this->groupByJurusan($mahasiswa);
        
        // Generate filename
        $filename = 'Buku_Wisuda_' . Str::slug($event->name) . '_' . now()->format('Y-m-d') . '.pdf';
        
        // Generate HTML content
        $html = view('admin.buku-wisuda.pdf-template', [
            'event' => $event,
            'mahasiswa' => $mahasiswa,
            'grouped' => $grouped,
        ])->render();
        
        // Path untuk menyimpan PDF
        $path = 'generated/' . $filename;
        $fullPath = Storage::disk('buku_wisuda')->path($path);
        
        // Pastikan folder generated ada
        $directory = dirname($fullPath);
        if (!is_dir($directory)) {
            mkdir($directory, 0755, true);
        }
        
        // Generate PDF dengan Browsershot
        Browsershot::html($html)
            ->format('A4')
            ->noSandbox() // Required for servers without sandbox capabilities
            ->windowSize(1200, 1600)
            ->deviceScaleFactor(2)
            ->waitUntilNetworkIdle()
            ->setDelay(2000) // Wait for fonts and images to load
            ->ignoreHttpsErrors()
            ->save($fullPath);
        
        // Create or update BukuWisuda record
        $bukuWisuda = BukuWisuda::updateOrCreate(
            [
                'graduation_event_id' => $event->id,
            ],
            [
                'status' => 'generated',
                'filename' => $filename,
                'file_path' => $path,
                'file_size' => Storage::disk('buku_wisuda')->size($path),
                'mime_type' => 'application/pdf',
                'download_count' => 0,
                'uploaded_at' => now(),
                'generated_at' => now(),
                'generated_by' => $generatedBy,
            ]
        );
        
        return $bukuWisuda;
    }

    /**
     * Publish buku wisuda
     */
    public function publish(BukuWisuda $bukuWisuda): void
    {
        $bukuWisuda->update([
            'status' => 'published',
        ]);
    }

    /**
     * Check if buku wisuda exists for event
     */
    public function existsForEvent(GraduationEvent $event): bool
    {
        return BukuWisuda::where('graduation_event_id', $event->id)
            ->whereIn('status', ['generated', 'published'])
            ->exists();
    }
}