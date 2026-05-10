<?php

namespace App\Services;

use App\Models\BukuWisuda;
use App\Models\GraduationEvent;
use App\Models\Mahasiswa;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class BukuWisudaService
{
    /**
     * Get all mahasiswa for a graduation event
     */
    public function getMahasiswaForEvent(GraduationEvent $event)
    {
        return Mahasiswa::whereHas('graduationTickets', function ($query) use ($event) {
            $query->where('graduation_event_id', $event->id)
                  ->whereNull('archived_at');
        })
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
     * Generate PDF for buku wisuda
     */
    public function generatePdf(GraduationEvent $event, string $generatedBy): BukuWisuda
    {
        $mahasiswa = $this->getMahasiswaForEvent($event);
        
        // Generate filename
        $filename = 'Buku_Wisuda_' . Str::slug($event->name) . '_' . now()->format('Y-m-d') . '.pdf';
        
        // Generate PDF
        $pdf = Pdf::loadView('admin.buku-wisuda.pdf-template', [
            'event' => $event,
            'mahasiswa' => $mahasiswa,
        ]);
        
        // Set paper size
        $pdf->setPaper('A4', 'portrait');
        
        // Save to storage
        $path = 'generated/' . $filename;
        Storage::disk('buku_wisuda')->put($path, $pdf->output());
        
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