<?php

namespace App\Services;

use App\Models\Attendance;
use App\Models\GraduationEvent;
use App\Models\GraduationTicket;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class AttendanceService
{
    protected QRCodeService $qrCodeService;

    public function __construct(QRCodeService $qrCodeService)
    {
        $this->qrCodeService = $qrCodeService;
    }

    /**
     * Record attendance from scanned QR code
     *
     * @param string $qrData Encrypted QR data
     * @param User|null $scanner
     * @return array ['success' => bool, 'message' => string, 'data' => array|null]
     */
    public function recordAttendance(string $qrData, ?User $scanner = null): array
    {
        try {
            // Validate QR code
            $validationResult = $this->validateQRCode($qrData);
            
            if (!$validationResult['valid']) {
                $this->logScanAttempt($qrData, $scanner, false, $validationResult['reason']);
                return [
                    'success' => false,
                    'message' => $validationResult['message'],
                    'data' => null,
                ];
            }

            $data = $validationResult['data'];
            $ticketId = $data['ticket_id'];
            $role = $data['role'];

            // Check for duplicate
            if ($this->checkDuplicate($ticketId, $role)) {
                $this->logScanAttempt($qrData, $scanner, false, 'duplicate');
                return [
                    'success' => false,
                    'message' => 'Sudah melakukan absensi sebelumnya',
                    'data' => null,
                ];
            }

            // Get ticket with mahasiswa
            $ticket = GraduationTicket::with('mahasiswa')->find($ticketId);
            
            if (!$ticket) {
                $this->logScanAttempt($qrData, $scanner, false, 'ticket_not_found');
                return [
                    'success' => false,
                    'message' => 'Tiket tidak ditemukan',
                    'data' => null,
                ];
            }

            // Record attendance
            $attendance = new Attendance();
            $attendance->graduation_ticket_id = $ticketId;
            $attendance->role = $role;
            $attendance->scanned_by = $scanner?->id;
            $attendance->scanned_at = now();
            $attendance->save();

            $this->logScanAttempt($qrData, $scanner, true, 'success');

            return [
                'success' => true,
                'message' => 'Absensi berhasil dicatat',
                'data' => [
                    'mahasiswa_name' => $ticket->mahasiswa->nama,
                    'npm' => $ticket->mahasiswa->npm,
                    'role' => $role,
                    'scanned_at' => $attendance->scanned_at->format('Y-m-d H:i:s'),
                ],
            ];
        } catch (\Exception $e) {
            $this->logScanAttempt($qrData, $scanner, false, 'exception: ' . $e->getMessage());
            return [
                'success' => false,
                'message' => 'Terjadi kesalahan sistem',
                'data' => null,
            ];
        }
    }

    /**
     * Validate scanned QR code
     *
     * @param string $qrData
     * @return array ['valid' => bool, 'message' => string, 'data' => array|null, 'reason' => string]
     */
    public function validateQRCode(string $qrData): array
    {
        // Decrypt QR data
        $data = $this->qrCodeService->decryptQRData($qrData);

        if (!$data) {
            return [
                'valid' => false,
                'message' => 'QR Code tidak valid',
                'data' => null,
                'reason' => 'invalid_qr',
            ];
        }

        // Validate required fields
        if (!isset($data['ticket_id']) || !isset($data['role']) || !isset($data['event_id'])) {
            return [
                'valid' => false,
                'message' => 'QR Code tidak valid',
                'data' => null,
                'reason' => 'missing_fields',
            ];
        }

        // Validate role
        if (!in_array($data['role'], ['mahasiswa', 'pendamping1', 'pendamping2'])) {
            return [
                'valid' => false,
                'message' => 'QR Code tidak valid',
                'data' => null,
                'reason' => 'invalid_role',
            ];
        }

        // Check if ticket exists and not expired
        $ticket = GraduationTicket::find($data['ticket_id']);
        
        if (!$ticket) {
            return [
                'valid' => false,
                'message' => 'Tiket tidak ditemukan',
                'data' => null,
                'reason' => 'ticket_not_found',
            ];
        }

        if ($ticket->expires_at && $ticket->expires_at->isPast()) {
            return [
                'valid' => false,
                'message' => 'Tiket sudah kadaluarsa',
                'data' => null,
                'reason' => 'expired',
            ];
        }

        return [
            'valid' => true,
            'message' => 'QR Code valid',
            'data' => $data,
            'reason' => 'valid',
        ];
    }

    /**
     * Check if attendance already recorded for this ticket and role
     *
     * @param int $ticketId
     * @param string $role
     * @return bool
     */
    public function checkDuplicate(int $ticketId, string $role): bool
    {
        return Attendance::where('graduation_ticket_id', $ticketId)
            ->where('role', $role)
            ->exists();
    }

    /**
     * Get attendance statistics
     *
     * @param GraduationEvent|null $event
     * @return array
     */
    public function getStatistics(?GraduationEvent $event = null): array
    {
        $query = Attendance::query();

        if ($event) {
            $ticketIds = GraduationTicket::where('graduation_event_id', $event->id)
                ->pluck('id');
            $query->whereIn('graduation_ticket_id', $ticketIds);
        }

        $totalMahasiswa = (clone $query)->where('role', 'mahasiswa')->count();
        $totalPendamping1 = (clone $query)->where('role', 'pendamping1')->count();
        $totalPendamping2 = (clone $query)->where('role', 'pendamping2')->count();

        // Get total registered students for the event
        $totalRegistered = 0;
        if ($event) {
            $totalRegistered = GraduationTicket::where('graduation_event_id', $event->id)->count();
        } else {
            $totalRegistered = GraduationTicket::count();
        }

        return [
            'total_registered' => $totalRegistered,
            'total_attended' => $totalMahasiswa,
            'total_pendamping1' => $totalPendamping1,
            'total_pendamping2' => $totalPendamping2,
            'total_all_attendees' => $totalMahasiswa + $totalPendamping1 + $totalPendamping2,
        ];
    }

    /**
     * Log scan attempt for audit trail
     *
     * @param string $qrData
     * @param User|null $scanner
     * @param bool $success
     * @param string $reason
     * @return void
     */
    protected function logScanAttempt(string $qrData, ?User $scanner, bool $success, string $reason): void
    {
        Log::channel('single')->info('QR Scan Attempt', [
            'scanner_id' => $scanner?->id,
            'scanner_name' => $scanner?->name,
            'result' => $success ? 'success' : 'failed',
            'reason' => $reason,
            'timestamp' => now()->toIso8601String(),
            'ip' => request()->ip(),
        ]);
    }
}
