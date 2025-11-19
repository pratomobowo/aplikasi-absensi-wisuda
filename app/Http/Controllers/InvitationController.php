<?php

namespace App\Http\Controllers;

use App\Services\PDFService;
use App\Services\QRCodeService;
use App\Services\TicketService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\RateLimiter;
use Symfony\Component\HttpFoundation\Response;

class InvitationController extends Controller
{
    protected TicketService $ticketService;
    protected QRCodeService $qrCodeService;
    protected PDFService $pdfService;

    public function __construct(
        TicketService $ticketService,
        QRCodeService $qrCodeService,
        PDFService $pdfService
    ) {
        $this->ticketService = $ticketService;
        $this->qrCodeService = $qrCodeService;
        $this->pdfService = $pdfService;
    }

    /**
     * Show invitation page with QR codes
     *
     * @param string $token
     * @return \Illuminate\View\View
     */
    public function show(string $token)
    {
        // Sanitize token input - only allow alphanumeric and basic characters
        $token = preg_replace('/[^a-zA-Z0-9\-_=]/', '', $token);
        
        if (empty($token)) {
            return view('invitation.error', [
                'message' => 'Token tidak valid'
            ]);
        }

        // Log magic link access
        Log::channel('stack')->info('Magic Link Access', [
            'token' => $token,
            'ip' => request()->ip(),
            'user_agent' => request()->userAgent()
        ]);

        // Validate magic link token
        $ticket = $this->ticketService->validateMagicLink($token);

        if (!$ticket) {
            return view('invitation.error', [
                'message' => 'Link tidak valid atau sudah kadaluarsa'
            ]);
        }

        // Load relationships
        $ticket->load(['mahasiswa', 'graduationEvent']);

        // Generate QR codes for display
        $qrCodes = [
            'mahasiswa' => $this->qrCodeService->generateQRCode($ticket->qr_token_mahasiswa),
            'pendamping1' => $this->qrCodeService->generateQRCode($ticket->qr_token_pendamping1),
            'pendamping2' => $this->qrCodeService->generateQRCode($ticket->qr_token_pendamping2),
        ];

        return view('invitation.show', [
            'ticket' => $ticket,
            'mahasiswa' => $ticket->mahasiswa,
            'event' => $ticket->graduationEvent,
            'qrCodes' => $qrCodes,
            'token' => $token,
        ]);
    }

    /**
     * Download invitation as PDF
     *
     * @param string $token
     * @return Response
     */
    public function downloadPDF(string $token)
    {
        // Sanitize token input - only allow alphanumeric and basic characters
        $token = preg_replace('/[^a-zA-Z0-9\-_=]/', '', $token);

        if (empty($token)) {
            abort(400, 'Token tidak valid');
        }

        // Rate limiting: 5 requests per minute per token
        $key = 'pdf-download:' . $token;

        if (RateLimiter::tooManyAttempts($key, 5)) {
            $seconds = RateLimiter::availableIn($key);
            return response()->json([
                'message' => "Terlalu banyak permintaan. Silakan coba lagi dalam {$seconds} detik."
            ], 429);
        }

        RateLimiter::hit($key, 60);

        // Validate magic link token
        $ticket = $this->ticketService->validateMagicLink($token);

        if (!$ticket) {
            abort(404, 'Link tidak valid atau sudah kadaluarsa');
        }

        // Load relationships
        $ticket->load(['mahasiswa', 'graduationEvent']);

        // Generate PDF and return as download
        $filepath = $this->pdfService->generateInvitationPDF($ticket);
        $filename = basename($filepath);

        return response()->download($filepath, $filename, [
            'Content-Type' => 'application/pdf',
        ]);
    }
}
