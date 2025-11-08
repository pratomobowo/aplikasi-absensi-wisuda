<?php

namespace App\Livewire;

use App\Services\AttendanceService;
use Livewire\Component;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class Scanner extends Component
{
    public string $status = 'ready'; // ready, scanning, success, error
    public ?array $scanResult = null;
    public string $errorMessage = '';

    // Scan history for debugging (stores last 10 scans)
    private array $scanHistory = [];
    private const MAX_HISTORY_SIZE = 10;

    protected AttendanceService $attendanceService;

    public function boot(AttendanceService $attendanceService)
    {
        $this->attendanceService = $attendanceService;
    }

    /**
     * Handle QR code scan from JavaScript
     *
     * @param string $qrData
     * @return void
     */
    public function scanQRCode(string $qrData): void
    {
        $scanStartTime = microtime(true);
        $scanner = Auth::user();
        $scannerId = $scanner ? $scanner->id : null;
        $scannerName = $scanner ? $scanner->name : 'Unknown';
        
        // Initial log with context
        Log::info('Scanner: QR scan initiated', [
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

            $this->recordScanHistory('failed', $result['reason'] ?? 'unknown', null, $totalDuration);

            // Dispatch error notification to frontend
            $errorMessage = $result['message'];
            $errorReason = $result['reason'] ?? 'Alasan tidak diketahui';
            $detailMessage = "{$errorMessage} - {$errorReason}";

            Log::info('Scanner: Dispatching error notification', [
                'message' => $detailMessage,
            ]);

            $this->dispatch('show-scan-notification', ['type' => 'error', 'message' => $detailMessage]);

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
        
        $historyEntry = [
            'timestamp' => now()->toIso8601String(),
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
            $successCount = count(array_filter($this->scanHistory, fn($entry) => $entry['result'] === 'success'));
            $failedCount = count(array_filter($this->scanHistory, fn($entry) => $entry['result'] === 'failed'));
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
