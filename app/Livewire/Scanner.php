<?php

namespace App\Livewire;

use App\Services\AttendanceService;
use Livewire\Component;
use Illuminate\Support\Facades\Auth;

class Scanner extends Component
{
    public string $status = 'ready'; // ready, scanning, success, error
    public ?array $scanResult = null;
    public string $errorMessage = '';

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
        // Validate input - QR data should not be empty and should be reasonable length
        if (empty($qrData) || strlen($qrData) > 1000) {
            $this->status = 'error';
            $this->errorMessage = 'Data QR code tidak valid';
            $this->dispatch('scan-error');
            $this->resetScanner(3000);
            return;
        }

        // Sanitize input - remove any potential XSS attempts
        $qrData = strip_tags($qrData);
        
        $this->status = 'scanning';
        
        // Get current authenticated user (scanner)
        $scanner = Auth::user();
        
        // Call AttendanceService to validate and record attendance
        $result = $this->attendanceService->recordAttendance($qrData, $scanner);
        
        if ($result['success']) {
            $this->status = 'success';
            $this->scanResult = $result['data'];
            $this->errorMessage = '';
            
            // Auto-reset after 3 seconds
            $this->dispatch('scan-success');
            $this->resetScanner(3000);
        } else {
            $this->status = 'error';
            $this->errorMessage = $result['message'];
            $this->scanResult = null;
            
            // Auto-reset after 3 seconds
            $this->dispatch('scan-error');
            $this->resetScanner(3000);
        }
    }

    /**
     * Reset scanner to ready state
     *
     * @param int $delay Delay in milliseconds before reset
     * @return void
     */
    public function resetScanner(int $delay = 0): void
    {
        if ($delay > 0) {
            // Schedule reset using JavaScript
            $this->dispatch('schedule-reset', delay: $delay);
        } else {
            $this->status = 'ready';
            $this->scanResult = null;
            $this->errorMessage = '';
            $this->dispatch('scanner-reset');
        }
    }

    /**
     * Manual reset called from JavaScript after delay
     *
     * @return void
     */
    public function doReset(): void
    {
        $this->status = 'ready';
        $this->scanResult = null;
        $this->errorMessage = '';
        $this->dispatch('scanner-reset');
    }

    public function render()
    {
        return view('livewire.scanner')
            ->layout('layouts.scanner');
    }
}
