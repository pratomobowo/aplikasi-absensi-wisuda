<?php

namespace App\Livewire;

use App\Services\AttendanceService;
use App\Services\KonsumsiService;
use Livewire\Component;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class Scanner extends Component
{
    public string $status = 'ready'; // ready, scanning, success, error
    public ?array $scanResult = null;
    public string $errorMessage = '';

    // Dual mode scanning: 'kehadiran' or 'konsumsi'
    public string $scanMode = 'kehadiran';

    // Scan history for live log display (stores last 10 scans)
    public array $scanHistory = [];
    private const MAX_HISTORY_SIZE = 10;

    protected AttendanceService $attendanceService;
    protected KonsumsiService $konsumsiService;

    public function boot(AttendanceService $attendanceService, KonsumsiService $konsumsiService)
    {
        $this->attendanceService = $attendanceService;
        $this->konsumsiService = $konsumsiService;
    }

    /**
     * Toggle between kehadiran and konsumsi scan modes
     */
    public function toggleScanMode(string $mode): void
    {
        if (in_array($mode, ['kehadiran', 'konsumsi'])) {
            $this->scanMode = $mode;
            Log::info('Scanner: Mode toggled', [
                'scanner_id' => Auth::user()?->id,
                'new_mode' => $mode,
            ]);

            // Dispatch event to JavaScript to restart camera
            $this->dispatch('update-scanMode');
        }
    }

    /**
     * Handle QR code scan from JavaScript
     * Routes to appropriate handler based on scan mode
     *
     * @param string $qrData
     * @return void
     */
    public function scanQRCode(string $qrData): void
    {
        if ($this->scanMode === 'konsumsi') {
            $this->scanKonsumsi($qrData);
        } else {
            $this->scanAttendance($qrData);
        }
    }

    /**
     * Handle attendance QR code scan (kehadiran mode)
     *
     * @param string $qrData
     * @return void
     */
    private function scanAttendance(string $qrData): void
    {
        $scanStartTime = microtime(true);
        $scanner = Auth::user();
        $scannerId = $scanner ? $scanner->id : null;
        $scannerName = $scanner ? $scanner->name : 'Unknown';

        // Initial log with context
        Log::info('Scanner: Attendance scan initiated', [
            'scanner_id' => $scannerId,
            'scanner_name' => $scannerName,
            'qr_length' => strlen($qrData),
            'current_status' => $this->status,
            'timestamp' => now()->toIso8601String(),
        ]);

        // Validate input
        if (empty($qrData)) {
            $duration = round((microtime(true) - $scanStartTime) * 1000, 2);
            Log::warning('Scanner: Validation failed - empty QR data', [
                'scanner_id' => $scannerId,
                'duration_ms' => $duration,
            ]);

            $this->recordScanHistory('validation_failed', 'empty_data', null, $duration);
            $this->status = 'error';
            $this->errorMessage = 'Data QR code tidak valid';
            $this->dispatch('scanner-auto-reset', delay: 1000);
            return;
        }

        if (strlen($qrData) > 1000) {
            $duration = round((microtime(true) - $scanStartTime) * 1000, 2);
            Log::warning('Scanner: Validation failed - QR data too long', [
                'scanner_id' => $scannerId,
                'qr_length' => strlen($qrData),
                'max_length' => 1000,
                'duration_ms' => $duration,
            ]);

            $this->recordScanHistory('validation_failed', 'data_too_long', null, $duration);
            $this->status = 'error';
            $this->errorMessage = 'Data QR code tidak valid';
            $this->dispatch('scanner-auto-reset', delay: 1000);
            return;
        }

        // Sanitize input
        $originalLength = strlen($qrData);
        $qrData = strip_tags($qrData);
        $sanitizedLength = strlen($qrData);

        if ($originalLength !== $sanitizedLength) {
            Log::info('Scanner: Input sanitized', [
                'scanner_id' => $scannerId,
                'original_length' => $originalLength,
                'sanitized_length' => $sanitizedLength,
            ]);
        }

        // Change status to scanning
        $previousStatus = $this->status;
        $this->status = 'scanning';
        Log::info('Scanner: Status transition', [
            'scanner_id' => $scannerId,
            'from_status' => $previousStatus,
            'to_status' => 'scanning',
        ]);

        // Call AttendanceService to validate and record attendance
        Log::info('Scanner: Calling AttendanceService', [
            'scanner_id' => $scannerId,
            'qr_data_length' => strlen($qrData),
        ]);

        $serviceStartTime = microtime(true);
        $result = $this->attendanceService->recordAttendance($qrData, $scanner);
        $serviceDuration = round((microtime(true) - $serviceStartTime) * 1000, 2);
        
        Log::info('Scanner: AttendanceService completed', [
            'scanner_id' => $scannerId,
            'success' => $result['success'],
            'service_duration_ms' => $serviceDuration,
        ]);
        
        if ($result['success']) {
            $totalDuration = round((microtime(true) - $scanStartTime) * 1000, 2);

            $this->status = 'success';
            $this->scanResult = $result['data'];
            $this->errorMessage = '';

            // Log success with details
            Log::info('Scanner: Scan successful', [
                'scanner_id' => $scannerId,
                'ticket_id' => $result['data']['ticket_id'] ?? null,
                'role' => $result['data']['role'] ?? null,
                'mahasiswa_name' => $result['data']['mahasiswa_name'] ?? null,
                'total_duration_ms' => $totalDuration,
                'service_duration_ms' => $serviceDuration,
                'npm' => $result['data']['npm'] ?? null,
            ]);

            $this->recordScanHistory('success', null, $result['data'], $totalDuration);

            // Dispatch success notification to frontend
            $mahasiswaName = $result['data']['mahasiswa_name'] ?? 'Unknown';
            $role = $result['data']['role'] ?? 'mahasiswa';

            // Format message with role information
            $roleLabel = match($role) {
                'mahasiswa' => 'Mahasiswa',
                'pendamping1' => 'Pendamping 1',
                'pendamping2' => 'Pendamping 2',
                default => 'Peserta'
            };
            $successMessage = "{$mahasiswaName} ({$roleLabel}) - Absensi tercatat";

            Log::info('Scanner: Dispatching success notification', [
                'message' => $successMessage,
            ]);

            $this->dispatch('show-scan-notification', ['type' => 'success', 'message' => $successMessage]);

            // Optimized: 300ms reset for faster scanning
            $this->dispatch('scanner-auto-reset', delay: 300);

            Log::debug('Scanner: Auto-reset scheduled', [
                'scanner_id' => $scannerId,
                'delay_ms' => 300,
            ]);
        } else {
            $totalDuration = round((microtime(true) - $scanStartTime) * 1000, 2);

            $this->status = 'error';
            $this->errorMessage = $result['message'];
            $this->scanResult = null;

            // Log error with details
            Log::warning('Scanner: Scan failed', [
                'scanner_id' => $scannerId,
                'error_message' => $result['message'],
                'error_reason' => $result['reason'] ?? 'unknown',
                'total_duration_ms' => $totalDuration,
                'service_duration_ms' => $serviceDuration,
            ]);

            $this->recordScanHistory('failed', $result['reason'] ?? null, null, $totalDuration);

            // Dispatch error notification to frontend
            $errorMessage = $result['message'];

            Log::info('Scanner: Dispatching error notification', [
                'message' => $errorMessage,
            ]);

            $this->dispatch('show-scan-notification', ['type' => 'error', 'message' => $errorMessage]);

            // Optimized: 300ms reset for faster scanning
            $this->dispatch('scanner-auto-reset', delay: 300);

            Log::debug('Scanner: Auto-reset scheduled after error', [
                'scanner_id' => $scannerId,
                'delay_ms' => 300,
            ]);
        }
        
        // Log scan history summary (only in debug mode)
        if (config('app.debug')) {
            Log::debug('Scanner: Scan history summary', [
                'scanner_id' => $scannerId,
                'total_scans' => count($this->scanHistory),
                'recent_scans' => array_slice($this->scanHistory, -3),
            ]);
        }
    }

    /**
     * Handle konsumsi QR code scan (konsumsi mode)
     * Only scans 1 barcode per student to record konsumsi receipt
     *
     * @param string $qrData
     * @return void
     */
    private function scanKonsumsi(string $qrData): void
    {
        $scanStartTime = microtime(true);
        $scanner = Auth::user();
        $scannerId = $scanner ? $scanner->id : null;
        $scannerName = $scanner ? $scanner->name : 'Unknown';

        // Initial log with context
        Log::info('Scanner: Konsumsi scan initiated', [
            'scanner_id' => $scannerId,
            'scanner_name' => $scannerName,
            'qr_length' => strlen($qrData),
            'current_status' => $this->status,
            'timestamp' => now()->toIso8601String(),
        ]);

        // Validate input
        if (empty($qrData)) {
            $duration = round((microtime(true) - $scanStartTime) * 1000, 2);
            Log::warning('Scanner: Konsumsi - Validation failed - empty QR data', [
                'scanner_id' => $scannerId,
                'duration_ms' => $duration,
            ]);

            $this->recordScanHistory('validation_failed', 'empty_data', null, $duration);
            $this->status = 'error';
            $this->errorMessage = 'Data QR code tidak valid';
            $this->dispatch('scanner-auto-reset', delay: 1000);
            return;
        }

        if (strlen($qrData) > 1000) {
            $duration = round((microtime(true) - $scanStartTime) * 1000, 2);
            Log::warning('Scanner: Konsumsi - Validation failed - QR data too long', [
                'scanner_id' => $scannerId,
                'qr_length' => strlen($qrData),
                'max_length' => 1000,
                'duration_ms' => $duration,
            ]);

            $this->recordScanHistory('validation_failed', 'data_too_long', null, $duration);
            $this->status = 'error';
            $this->errorMessage = 'Data QR code tidak valid';
            $this->dispatch('scanner-auto-reset', delay: 1000);
            return;
        }

        // Sanitize input
        $originalLength = strlen($qrData);
        $qrData = strip_tags($qrData);
        $sanitizedLength = strlen($qrData);

        if ($originalLength !== $sanitizedLength) {
            Log::info('Scanner: Konsumsi - Input sanitized', [
                'scanner_id' => $scannerId,
                'original_length' => $originalLength,
                'sanitized_length' => $sanitizedLength,
            ]);
        }

        // Change status to scanning
        $previousStatus = $this->status;
        $this->status = 'scanning';
        Log::info('Scanner: Konsumsi - Status transition', [
            'scanner_id' => $scannerId,
            'from_status' => $previousStatus,
            'to_status' => 'scanning',
        ]);

        // Call KonsumsiService to validate and record konsumsi
        Log::info('Scanner: Konsumsi - Calling KonsumsiService', [
            'scanner_id' => $scannerId,
            'qr_data_length' => strlen($qrData),
        ]);

        $serviceStartTime = microtime(true);
        $result = $this->konsumsiService->recordKonsumsi($qrData, $scanner);
        $serviceDuration = round((microtime(true) - $serviceStartTime) * 1000, 2);

        Log::info('Scanner: Konsumsi - KonsumsiService completed', [
            'scanner_id' => $scannerId,
            'success' => $result['success'],
            'service_duration_ms' => $serviceDuration,
        ]);

        if ($result['success']) {
            $totalDuration = round((microtime(true) - $scanStartTime) * 1000, 2);

            $this->status = 'success';
            $this->scanResult = $result['data'];
            $this->errorMessage = '';

            // Log success with details
            Log::info('Scanner: Konsumsi - Scan successful', [
                'scanner_id' => $scannerId,
                'ticket_id' => $result['data']['ticket_id'] ?? null,
                'mahasiswa_name' => $result['data']['mahasiswa_nama'] ?? null,
                'total_duration_ms' => $totalDuration,
                'service_duration_ms' => $serviceDuration,
                'npm' => $result['data']['mahasiswa_npm'] ?? null,
            ]);

            $this->recordScanHistory('success', null, $result['data'], $totalDuration);

            // Dispatch success notification to frontend
            $successMessage = $result['message'];

            Log::info('Scanner: Konsumsi - Dispatching success notification', [
                'message' => $successMessage,
            ]);

            $this->dispatch('show-scan-notification', ['type' => 'success', 'message' => $successMessage]);

            // Optimized: 300ms reset for faster scanning
            $this->dispatch('scanner-auto-reset', delay: 300);

            Log::debug('Scanner: Konsumsi - Auto-reset scheduled', [
                'scanner_id' => $scannerId,
                'delay_ms' => 300,
            ]);
        } else {
            $totalDuration = round((microtime(true) - $scanStartTime) * 1000, 2);

            $this->status = 'error';
            $this->errorMessage = $result['message'];
            $this->scanResult = null;

            // Log error with details
            Log::warning('Scanner: Konsumsi - Scan failed', [
                'scanner_id' => $scannerId,
                'error_message' => $result['message'],
                'error_reason' => $result['reason'] ?? 'unknown',
                'total_duration_ms' => $totalDuration,
                'service_duration_ms' => $serviceDuration,
            ]);

            $this->recordScanHistory('failed', $result['reason'] ?? null, null, $totalDuration);

            // Dispatch error notification to frontend
            $errorMessage = $result['message'];

            Log::info('Scanner: Konsumsi - Dispatching error notification', [
                'message' => $errorMessage,
            ]);

            $this->dispatch('show-scan-notification', ['type' => 'error', 'message' => $errorMessage]);

            // Optimized: 300ms reset for faster scanning
            $this->dispatch('scanner-auto-reset', delay: 300);

            Log::debug('Scanner: Konsumsi - Auto-reset scheduled after error', [
                'scanner_id' => $scannerId,
                'delay_ms' => 300,
            ]);
        }

        // Log scan history summary (only in debug mode)
        if (config('app.debug')) {
            Log::debug('Scanner: Konsumsi - Scan history summary', [
                'scanner_id' => $scannerId,
                'total_scans' => count($this->scanHistory),
                'recent_scans' => array_slice($this->scanHistory, -3),
            ]);
        }
    }

    /**
     * Reset scanner to ready state (auto-reset)
     *
     * @return void
     */
    public function doReset(): void
    {
        $resetStartTime = microtime(true);
        $scanner = Auth::user();
        $scannerId = $scanner ? $scanner->id : null;
        $previousStatus = $this->status;
        
        Log::info('Scanner: Auto-reset initiated', [
            'scanner_id' => $scannerId,
            'previous_status' => $previousStatus,
            'had_scan_result' => !is_null($this->scanResult),
            'had_error_message' => !empty($this->errorMessage),
            'timestamp' => now()->toIso8601String(),
        ]);
        
        // Verify we're in a state that should be reset (success or error)
        if (!in_array($previousStatus, ['success', 'error'])) {
            Log::warning('Scanner: Auto-reset called from unexpected state', [
                'scanner_id' => $scannerId,
                'status' => $previousStatus,
            ]);
        }
        
        // Clear all state completely before returning to ready
        $this->scanResult = null;
        $this->errorMessage = '';
        
        // Set status to ready as the final step
        $this->status = 'ready';
        
        $duration = round((microtime(true) - $resetStartTime) * 1000, 2);
        
        Log::info('Scanner: Auto-reset completed', [
            'scanner_id' => $scannerId,
            'previous_status' => $previousStatus,
            'new_status' => 'ready',
            'state_cleared' => true,
            'duration_ms' => $duration,
        ]);
        
        // Dispatch event to resume scanner
        $this->dispatch('scanner-ready');
        
        Log::debug('Scanner: scanner-ready event dispatched', [
            'scanner_id' => $scannerId,
            'ready_for_next_scan' => true,
        ]);
    }
    
    /**
     * Force reset scanner (for manual reset button)
     * Clears all state and returns scanner to ready state immediately
     * Can be called from any state (ready, scanning, success, error)
     *
     * @return void
     */
    public function forceReset(): void
    {
        $resetStartTime = microtime(true);
        $scanner = Auth::user();
        $scannerId = $scanner ? $scanner->id : null;
        $previousStatus = $this->status;
        
        Log::info('Scanner: Force reset triggered by user', [
            'scanner_id' => $scannerId,
            'previous_status' => $previousStatus,
            'had_scan_result' => !is_null($this->scanResult),
            'had_error_message' => !empty($this->errorMessage),
            'scan_history_count' => count($this->scanHistory),
            'timestamp' => now()->toIso8601String(),
        ]);
        
        // Verify all state variables before clearing
        $stateBeforeReset = [
            'status' => $this->status,
            'has_scan_result' => !is_null($this->scanResult),
            'has_error_message' => !empty($this->errorMessage),
            'history_count' => count($this->scanHistory),
        ];
        
        Log::debug('Scanner: State before force reset', $stateBeforeReset);
        
        // Clear all state completely - order matters for proper cleanup
        $historyCount = count($this->scanHistory);
        
        // 1. Clear scan history first
        $this->scanHistory = [];
        
        // 2. Clear result and error data
        $this->scanResult = null;
        $this->errorMessage = '';
        
        // 3. Set status to ready as final step
        $this->status = 'ready';
        
        // Verify state is completely cleared
        $stateAfterReset = [
            'status' => $this->status,
            'scan_result' => $this->scanResult,
            'error_message' => $this->errorMessage,
            'history_count' => count($this->scanHistory),
        ];
        
        $allStateCleared = (
            $this->status === 'ready' &&
            is_null($this->scanResult) &&
            empty($this->errorMessage) &&
            count($this->scanHistory) === 0
        );
        
        $duration = round((microtime(true) - $resetStartTime) * 1000, 2);
        
        Log::info('Scanner: Force reset completed', [
            'scanner_id' => $scannerId,
            'previous_status' => $previousStatus,
            'new_status' => 'ready',
            'state_cleared' => $allStateCleared,
            'history_cleared' => $historyCount > 0,
            'history_count_before' => $historyCount,
            'duration_ms' => $duration,
            'state_after_reset' => $stateAfterReset,
        ]);
        
        // Verify state clearing was successful
        if (!$allStateCleared) {
            Log::error('Scanner: Force reset incomplete - state not fully cleared', [
                'scanner_id' => $scannerId,
                'state_after_reset' => $stateAfterReset,
            ]);
        }
        
        // Dispatch events to resume scanner and show feedback
        $this->dispatch('scanner-ready');
        $this->dispatch('scanner-force-reset-complete');
        
        Log::debug('Scanner: Events dispatched after force reset', [
            'scanner_id' => $scannerId,
            'events' => ['scanner-ready', 'scanner-force-reset-complete'],
            'ready_for_next_scan' => true,
        ]);
    }
    
    /**
     * Record scan attempt in history for debugging
     *
     * @param string $result success|failed|validation_failed
     * @param string|null $reason Error reason if failed
     * @param array|null $data Scan result data if successful
     * @param float $duration Duration in milliseconds
     * @return void
     */
    private function recordScanHistory(string $result, ?string $reason, ?array $data, float $duration): void
    {
        $scanner = Auth::user();
        $isSuccess = $result === 'success';

        // Debug: Log the exact reason being passed
        if (!$isSuccess) {
            Log::debug('Scanner: recordScanHistory - reason parameter', [
                'reason' => $reason,
                'reason_type' => gettype($reason),
                'scan_mode' => $this->scanMode,
            ]);
        }

        // Determine mahasiswa name based on mode and data
        $mahasiswaName = null;
        $roleLabel = 'Peserta';
        $message = 'Kesalahan tidak diketahui';

        if ($isSuccess && $data) {
            // Success case - data has different field names depending on mode
            // For kehadiran: uses 'mahasiswa_name' and 'role'
            // For konsumsi: uses 'mahasiswa_nama' and no role (single scan per student)
            $mahasiswaName = $data['mahasiswa_name'] ?? $data['mahasiswa_nama'] ?? 'Unknown';

            if ($this->scanMode === 'kehadiran') {
                $role = $data['role'] ?? 'mahasiswa';
                $roleLabel = match($role) {
                    'mahasiswa' => 'Mahasiswa',
                    'pendamping1' => 'Pendamping 1',
                    'pendamping2' => 'Pendamping 2',
                    default => 'Peserta'
                };
                $message = 'Absensi tercatat';
            } else {
                // Konsumsi mode - no role, just record the action
                $roleLabel = 'Konsumsi';
                $message = 'Konsumsi tercatat';
            }
        } else if (!$isSuccess) {
            // Error case - format error message with detailed descriptions (same as toast)
            Log::debug('Scanner: recordScanHistory - processing error', [
                'reason' => $reason,
                'message_before_match' => $message,
            ]);

            $message = match($reason) {
                // Validation errors (from Scanner)
                'empty_data' => 'QR Code kosong atau tidak valid',
                'data_too_long' => 'QR Code terlalu panjang',
                'invalid_qr' => 'QR tidak valid',
                'validation_failed' => 'Data QR code tidak valid',

                // Attendance service error codes (from AttendanceService)
                'empty_qr_data' => 'QR Code tidak valid atau rusak',
                'qr_data_too_long' => 'QR Code tidak valid atau rusak',
                'decryption_exception' => 'QR Code tidak dapat dibaca. Pastikan QR Code tidak rusak',
                'decryption_failed' => 'QR Code tidak valid atau rusak',
                'missing_required_fields' => 'QR Code tidak lengkap atau rusak',
                'invalid_role_value' => 'QR Code tidak valid. Tipe peserta tidak dikenali',
                'ticket_not_found' => 'Data tidak ditemukan di database',
                'ticket_expired' => 'Tiket sudah kadaluarsa',
                'duplicate_attendance' => 'Mahasiswa ini sudah melakukan absensi sebelumnya',
                'database_error' => 'Terjadi kesalahan database. Silakan coba lagi',
                'transaction_failed' => 'Gagal menyimpan data. Silakan coba lagi',
                'event_not_active' => 'Acara wisuda belum dimulai atau sudah berakhir',
                'invalid_event' => 'Acara wisuda tidak valid',
                'mahasiswa_not_attended' => 'Wisudawan belum hadir',
                'system_error' => 'Terjadi kesalahan sistem. Silakan coba lagi',

                // Konsumsi service error codes (from KonsumsiService)
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

                // Fallback cases
                'unknown' => 'Kesalahan tidak diketahui',
                null => 'Kesalahan tidak diketahui',
                default => (string) ($reason ?? 'Kesalahan tidak diketahui')
            };

            Log::debug('Scanner: recordScanHistory - message after match', [
                'reason' => $reason,
                'message_after_match' => $message,
            ]);

            // For error cases, show generic placeholder for mahasiswa name
            // since we don't have valid mahasiswa data
            $mahasiswaName = '-';
            $roleLabel = 'Gagal';
        }

        $historyEntry = [
            'success' => $isSuccess,
            'timestamp' => now()->format('H:i:s'),
            'mahasiswa_name' => $mahasiswaName,
            'role_label' => $roleLabel,
            'message' => $message,
            'scanner_id' => $scanner ? $scanner->id : null,
            'result' => $result,
            'reason' => $reason,
            'duration_ms' => $duration,
            'ticket_id' => $data['ticket_id'] ?? null,
            'role' => $data['role'] ?? null,
        ];

        // Add to history
        $this->scanHistory[] = $historyEntry;

        // Keep only last MAX_HISTORY_SIZE entries
        if (count($this->scanHistory) > self::MAX_HISTORY_SIZE) {
            $this->scanHistory = array_slice($this->scanHistory, -self::MAX_HISTORY_SIZE);
        }

        // Log history entry
        Log::info('Scanner: Scan attempt recorded', $historyEntry);

        // Calculate statistics if we have enough history
        if (count($this->scanHistory) >= 3) {
            $successCount = count(array_filter($this->scanHistory, fn($entry) => $entry['success'] === true));
            $failedCount = count(array_filter($this->scanHistory, fn($entry) => $entry['success'] === false));
            $avgDuration = array_sum(array_column($this->scanHistory, 'duration_ms')) / count($this->scanHistory);

            Log::debug('Scanner: Statistics', [
                'scanner_id' => $scanner ? $scanner->id : null,
                'total_scans' => count($this->scanHistory),
                'success_count' => $successCount,
                'failed_count' => $failedCount,
                'success_rate' => count($this->scanHistory) > 0 ? round(($successCount / count($this->scanHistory)) * 100, 2) : 0,
                'avg_duration_ms' => round($avgDuration, 2),
            ]);
        }
    }

    public function render()
    {
        return view('livewire.scanner')
            ->layout('layouts.scanner');
    }
}
