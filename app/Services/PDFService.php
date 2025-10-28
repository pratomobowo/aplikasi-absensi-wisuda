<?php

namespace App\Services;

use App\Models\GraduationTicket;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Str;

class PDFService
{
    protected QRCodeService $qrCodeService;

    public function __construct(QRCodeService $qrCodeService)
    {
        $this->qrCodeService = $qrCodeService;
    }

    /**
     * Generate invitation PDF for a graduation ticket
     *
     * @param GraduationTicket $ticket
     * @return string PDF file path
     */
    public function generateInvitationPDF(GraduationTicket $ticket): string
    {
        // Load ticket with relationships
        $ticket->load(['mahasiswa', 'graduationEvent']);

        // Generate QR codes as base64 images
        $qrCodes = [
            'mahasiswa' => $this->qrCodeService->generateQRCode($ticket->qr_token_mahasiswa),
            'pendamping1' => $this->qrCodeService->generateQRCode($ticket->qr_token_pendamping1),
            'pendamping2' => $this->qrCodeService->generateQRCode($ticket->qr_token_pendamping2),
        ];

        // Prepare data for view
        $data = [
            'ticket' => $ticket,
            'mahasiswa' => $ticket->mahasiswa,
            'event' => $ticket->graduationEvent,
            'qrCodes' => $qrCodes,
        ];

        // Generate PDF
        $pdf = Pdf::loadView('pdf.invitation', $data);
        
        // Set paper size to A4
        $pdf->setPaper('a4', 'portrait');
        
        // Set options for better quality
        $pdf->setOptions([
            'isHtml5ParserEnabled' => true,
            'isRemoteEnabled' => true,
            'defaultFont' => 'sans-serif',
        ]);

        // Generate filename
        $filename = $this->generateFilename($ticket->mahasiswa->nama, $ticket->mahasiswa->npm);
        $filepath = storage_path('app/public/invitations/' . $filename);

        // Ensure directory exists
        if (!file_exists(dirname($filepath))) {
            mkdir(dirname($filepath), 0755, true);
        }

        // Save PDF
        $pdf->save($filepath);

        return $filepath;
    }

    /**
     * Generate filename for PDF
     *
     * @param string $name
     * @param string $npm
     * @return string
     */
    protected function generateFilename(string $name, string $npm): string
    {
        $slug = Str::slug($name);
        return "undangan-wisuda-{$slug}-{$npm}.pdf";
    }
}
