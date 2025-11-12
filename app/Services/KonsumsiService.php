<?php

namespace App\Services;

use App\Models\Attendance;
use App\Models\GraduationTicket;
use App\Models\KonsumsiRecord;
use App\Models\Mahasiswa;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use App\Models\User;

class KonsumsiService
{
    /**
     * Error code constants
     */
    private const ERROR_CODES = [
        'empty_qr' => 'ERROR_EMPTY_QR',
        'qr_too_long' => 'ERROR_QR_TOO_LONG',
        'decryption_exception' => 'ERROR_DECRYPTION_EXCEPTION',
        'decryption_failed' => 'ERROR_DECRYPTION_FAILED',
        'missing_fields' => 'ERROR_MISSING_FIELDS',
        'ticket_not_found' => 'ERROR_TICKET_NOT_FOUND',
        'ticket_expired' => 'ERROR_TICKET_EXPIRED',
        'duplicate' => 'ERROR_KONSUMSI_DUPLICATE',
        'database' => 'ERROR_DATABASE',
        'event_not_active' => 'ERROR_EVENT_NOT_ACTIVE',
        'invalid_event' => 'ERROR_INVALID_EVENT',
    ];

    protected QRCodeService $qrCodeService;
    protected ScannerCacheService $cacheService;

    public function __construct(
        QRCodeService $qrCodeService,
        ScannerCacheService $cacheService
    ) {
        $this->qrCodeService = $qrCodeService;
        $this->cacheService = $cacheService;
    }

    /**
     * Record konsumsi for a mahasiswa from QR code
     *
     * @param string $qrData Encrypted QR code data
     * @param User|null $scanner User who performed the scan
     * @return array Response array with keys: success, message, data, reason
     */
    public function recordKonsumsi(string $qrData, ?User $scanner = null): array
    {
        try {
            // Validate QR code
            $validation = $this->validateQRCode($qrData);
            if (!$validation['valid']) {
                return [
                    'success' => false,
                    'message' => $this->getErrorMessage($validation['reason']),
                    'data' => null,
                    'reason' => $validation['reason'],
                ];
            }

            $ticketId = $validation['ticket_id'];
            $ticket = $validation['ticket'];

            // Begin database transaction
            return DB::transaction(function () use ($ticketId, $ticket, $scanner) {
                // Create konsumsi record
                $konsumsi = KonsumsiRecord::create([
                    'graduation_ticket_id' => $ticketId,
                    'scanned_by' => $scanner?->id,
                    'scanned_at' => now(),
                ]);

                // Update graduation ticket
                $ticket->update([
                    'konsumsi_diterima' => true,
                    'konsumsi_at' => now(),
                ]);

                Log::info('Konsumsi recorded', [
                    'ticket_id' => $ticketId,
                    'mahasiswa_id' => $ticket->mahasiswa_id,
                    'scanner_id' => $scanner?->id,
                    'konsumsi_record_id' => $konsumsi->id,
                ]);

                return [
                    'success' => true,
                    'message' => '✓ ' . ($ticket->mahasiswa->nama ?? 'Mahasiswa') . ' sudah menerima konsumsi',
                    'data' => [
                        'konsumsi_id' => $konsumsi->id,
                        'ticket_id' => $ticketId,
                        'mahasiswa_id' => $ticket->mahasiswa_id,
                        'mahasiswa_nama' => $ticket->mahasiswa->nama,
                        'mahasiswa_npm' => $ticket->mahasiswa->npm,
                        'scanned_at' => $konsumsi->scanned_at?->format('Y-m-d H:i:s'),
                        'scanned_by' => $scanner?->name,
                    ],
                    'reason' => 'success',
                ];
            });
        } catch (\Throwable $e) {
            Log::error('Error recording konsumsi', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);

            return [
                'success' => false,
                'message' => 'Terjadi kesalahan sistem. Silakan coba lagi.',
                'data' => null,
                'reason' => 'ERROR_SYSTEM',
            ];
        }
    }

    /**
     * Validate QR code for konsumsi
     *
     * @param string $qrData Encrypted QR code data
     * @return array Validation result with keys: valid, ticket_id, ticket, reason
     */
    public function validateQRCode(string $qrData): array
    {
        // Step 1: Format validation
        if (empty($qrData)) {
            return [
                'valid' => false,
                'ticket_id' => null,
                'ticket' => null,
                'reason' => self::ERROR_CODES['empty_qr'],
            ];
        }

        if (strlen($qrData) > 10000) {
            return [
                'valid' => false,
                'ticket_id' => null,
                'ticket' => null,
                'reason' => self::ERROR_CODES['qr_too_long'],
            ];
        }

        // Step 2: Decryption
        try {
            $decrypted = $this->qrCodeService->decryptQRData($qrData);
        } catch (\Exception $e) {
            Log::warning('QR decryption exception', ['error' => $e->getMessage()]);

            return [
                'valid' => false,
                'ticket_id' => null,
                'ticket' => null,
                'reason' => self::ERROR_CODES['decryption_exception'],
            ];
        }

        if ($decrypted === null) {
            return [
                'valid' => false,
                'ticket_id' => null,
                'ticket' => null,
                'reason' => self::ERROR_CODES['decryption_failed'],
            ];
        }

        // Step 3: Structure validation
        if (!isset($decrypted['ticket_id']) || !isset($decrypted['event_id'])) {
            return [
                'valid' => false,
                'ticket_id' => null,
                'ticket' => null,
                'reason' => self::ERROR_CODES['missing_fields'],
            ];
        }

        $ticketId = $decrypted['ticket_id'];
        $eventId = $decrypted['event_id'];

        // Step 4: Database lookup
        $ticket = GraduationTicket::find($ticketId);

        if (!$ticket) {
            return [
                'valid' => false,
                'ticket_id' => $ticketId,
                'ticket' => null,
                'reason' => self::ERROR_CODES['ticket_not_found'],
            ];
        }

        // Step 5: Verify event exists and is active
        if ($ticket->graduation_event_id !== $eventId) {
            return [
                'valid' => false,
                'ticket_id' => $ticketId,
                'ticket' => $ticket,
                'reason' => self::ERROR_CODES['invalid_event'],
            ];
        }

        if (!$ticket->graduationEvent || !$ticket->graduationEvent->is_active) {
            return [
                'valid' => false,
                'ticket_id' => $ticketId,
                'ticket' => $ticket,
                'reason' => self::ERROR_CODES['event_not_active'],
            ];
        }

        // Step 6: Check ticket expiration
        if ($ticket->isExpired()) {
            return [
                'valid' => false,
                'ticket_id' => $ticketId,
                'ticket' => $ticket,
                'reason' => self::ERROR_CODES['ticket_expired'],
            ];
        }

        // Step 7: Check duplicate konsumsi
        if ($ticket->konsumsi_diterima) {
            return [
                'valid' => false,
                'ticket_id' => $ticketId,
                'ticket' => $ticket,
                'reason' => self::ERROR_CODES['duplicate'],
            ];
        }

        return [
            'valid' => true,
            'ticket_id' => $ticketId,
            'ticket' => $ticket,
            'reason' => 'valid',
        ];
    }

    /**
     * Get error message in Indonesian based on error code
     *
     * @param string $errorCode Error code
     * @return string Error message
     */
    private function getErrorMessage(string $errorCode): string
    {
        $messages = [
            'ERROR_EMPTY_QR' => 'QR Code kosong atau tidak valid',
            'ERROR_QR_TOO_LONG' => 'QR Code terlalu panjang',
            'ERROR_DECRYPTION_EXCEPTION' => 'Gagal menguraikan QR Code',
            'ERROR_DECRYPTION_FAILED' => 'QR Code tidak dapat diuraikan',
            'ERROR_MISSING_FIELDS' => 'QR Code tidak lengkap',
            'ERROR_TICKET_NOT_FOUND' => 'Tiket tidak ditemukan',
            'ERROR_TICKET_EXPIRED' => 'Tiket sudah kadaluarsa',
            'ERROR_KONSUMSI_DUPLICATE' => 'Mahasiswa ini sudah menerima konsumsi',
            'ERROR_DATABASE' => 'Kesalahan database',
            'ERROR_EVENT_NOT_ACTIVE' => 'Event wisuda tidak aktif',
            'ERROR_INVALID_EVENT' => 'Event tidak sesuai',
            'ERROR_SYSTEM' => 'Terjadi kesalahan sistem',
        ];

        return $messages[$errorCode] ?? 'Terjadi kesalahan yang tidak diketahui';
    }

    /**
     * Get konsumsi statistics for an event
     *
     * @param int $eventId Graduation event ID
     * @return array Statistics
     */
    public function getKonsumsiStats(int $eventId): array
    {
        $totalTickets = GraduationTicket::where('graduation_event_id', $eventId)->count();
        $konsumsiCount = GraduationTicket::where('graduation_event_id', $eventId)
            ->where('konsumsi_diterima', true)
            ->count();

        return [
            'total' => $totalTickets,
            'received' => $konsumsiCount,
            'pending' => $totalTickets - $konsumsiCount,
            'percentage' => $totalTickets > 0 ? round(($konsumsiCount / $totalTickets) * 100, 2) : 0,
        ];
    }

    /**
     * Get konsumsi records for an event with detailed information
     *
     * @param int $eventId Graduation event ID
     * @param array $filters Optional filters
     * @return \Illuminate\Pagination\Paginator
     */
    public function getKonsumsiRecords(int $eventId, array $filters = [])
    {
        $query = GraduationTicket::where('graduation_event_id', $eventId)
            ->with(['mahasiswa', 'konsumsiRecord.scannedBy'])
            ->select([
                'graduation_tickets.id',
                'graduation_tickets.mahasiswa_id',
                'graduation_tickets.konsumsi_diterima',
                'graduation_tickets.konsumsi_at',
            ]);

        // Filter by status if provided
        if (isset($filters['status'])) {
            if ($filters['status'] === 'received') {
                $query->where('konsumsi_diterima', true);
            } elseif ($filters['status'] === 'pending') {
                $query->where('konsumsi_diterima', false);
            }
        }

        // Search by nama or npm if provided
        if (isset($filters['search'])) {
            $search = $filters['search'];
            $query->whereHas('mahasiswa', function ($q) use ($search) {
                $q->where('nama', 'like', "%{$search}%")
                    ->orWhere('npm', 'like', "%{$search}%");
            });
        }

        return $query->paginate(50);
    }
}
