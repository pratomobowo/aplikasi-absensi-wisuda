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
     * Generate PDF for buku wisuda using Browsershot
     */
    public function generatePdf(GraduationEvent $event, string $generatedBy): BukuWisuda
    {
        $mahasiswa = $this->getMahasiswaForEvent($event);
        
        // Generate filename
        $filename = 'Buku_Wisuda_' . Str::slug($event->name) . '_' . now()->format('Y-m-d') . '.pdf';
        
        // Generate HTML content
        $html = view('admin.buku-wisuda.pdf-template', [
            'event' => $event,
            'mahasiswa' => $mahasiswa,
        ])->render();
        
        // Path untuk menyimpan PDF
        $path = 'generated/' . $filename;
        $fullPath = Storage::disk('buku_wisuda')->path($path);
        
        // Generate PDF dengan Browsershot (Chromium rendering)
        Browsershot::html($html)
            ->setNodeBinary('/usr/local/bin/node') // Production path
            ->setNpmBinary('/usr/local/bin/npm')
            ->landscape()
            ->margins(20, 25, 20, 25) // top, right, bottom, left (in mm)
            ->format('A4')
            ->waitUntilNetworkIdle()
            ->ignoreHttpsErrors()
            ->headerHtml($this->getHeaderHtml($event))
            ->footerHtml($this->getFooterHtml())
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
     * Get header HTML for PDF
     */
    private function getHeaderHtml(GraduationEvent $event): string
    {
        return '<div style="
            font-family: Poppins, sans-serif;
            font-size: 9pt;
            color: #1e40af;
            width: 100%;
            padding: 10px 20px;
            border-bottom: 2px solid #1e40af;
            display: flex;
            justify-content: space-between;
            align-items: center;
        ">
            <div style="display: flex; align-items: center; gap: 8px;">
                <span style="font-weight: 700; text-transform: uppercase; letter-spacing: 1px;">Buku Wisuda</span>
            </div>
            <div style="font-weight: 500; color: #64748b;">
                ' . htmlspecialchars($event->name) . '
            </div>
        </div>';
    }

    /**
     * Get footer HTML for PDF
     */
    private function getFooterHtml(): string
    {
        return '<div style="
            font-family: Poppins, sans-serif;
            font-size: 8pt;
            color: #64748b;
            width: 100%;
            padding: 10px 25px;
            border-top: 1px solid #e2e8f0;
            display: flex;
            justify-content: space-between;
        ">
            <span>Universitas Sangga Buana YPKP</span>
            <span class="pageNumber"></span>
        </div>';
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