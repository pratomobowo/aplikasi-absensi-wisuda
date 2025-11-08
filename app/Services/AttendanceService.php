<?php

namespace App\Services;

use App\Models\Attendance;
use App\Models\GraduationEvent;
use App\Models\GraduationTicket;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use App\Services\ScannerCacheService;
use App\Services\ParallelValidationService;

class AttendanceService
{
    protected QRCodeService $qrCodeService;

    // Error reason codes for logging and debugging
    public const ERROR_EMPTY_QR = 'empty_qr_data';
    public const ERROR_QR_TOO_LONG = 'qr_data_too_long';
    public const ERROR_DECRYPTION_EXCEPTION = 'decryption_exception';
    public const ERROR_DECRYPTION_FAILED = 'decryption_failed';
    public const ERROR_MISSING_FIELDS = 'missing_required_fields';
    public const ERROR_INVALID_ROLE = 'invalid_role_value';
    public const ERROR_TICKET_NOT_FOUND = 'ticket_not_found';
    public const ERROR_TICKET_EXPIRED = 'ticket_expired';
    public const ERROR_DUPLICATE = 'duplicate_attendance';
    public const ERROR_DATABASE = 'database_error';
    public const ERROR_TRANSACTION_FAILED = 'transaction_failed';
    public const ERROR_SYSTEM = 'system_error';
    public const ERROR_EVENT_NOT_ACTIVE = 'event_not_active';
    public const ERROR_INVALID_EVENT = 'invalid_event';

    // User-facing error messages in Bahasa Indonesia
    protected array $errorMessages = [
        self::ERROR_EMPTY_QR => 'QR Code tidak valid atau rusak',
        self::ERROR_QR_TOO_LONG => 'QR Code tidak valid atau rusak',
        self::ERROR_DECRYPTION_EXCEPTION => 'QR Code tidak dapat dibaca. Pastikan QR Code tidak rusak',
        self::ERROR_DECRYPTION_FAILED => 'QR Code tidak valid atau rusak',
        self::ERROR_MISSING_FIELDS => 'QR Code tidak lengkap atau rusak',
        self::ERROR_INVALID_ROLE => 'QR Code tidak valid. Tipe peserta tidak dikenali',
        self::ERROR_TICKET_NOT_FOUND => 'Data tidak ditemukan di database',
        self::ERROR_TICKET_EXPIRED => 'Tiket sudah kadaluarsa',
        self::ERROR_DUPLICATE => 'Sudah melakukan absensi sebelumnya',
        self::ERROR_DATABASE => 'Terjadi kesalahan database. Silakan coba lagi',
        self::ERROR_TRANSACTION_FAILED => 'Gagal menyimpan data. Silakan coba lagi',
        self::ERROR_SYSTEM => 'Terjadi kesalahan sistem. Silakan coba lagi',
        self::ERROR_EVENT_NOT_ACTIVE => 'Acara wisuda belum dimulai atau sudah berakhir',
        self::ERROR_INVALID_EVENT => 'Acara wisuda tidak valid',
    ];

    public function __construct(QRCodeService $qrCodeService)
    {
        $this->qrCodeService = $qrCodeService;
    }

    /**
     * Get user-facing error message for a given error code
     *
     * @param string $errorCode
     * @return string
     */
    protected function getErrorMessage(string $errorCode): string
    {
        return $this->errorMessages[$errorCode] ?? $this->errorMessages[self::ERROR_SYSTEM];
    }

    /**
     * Record attendance from scanned QR code
     *
     * @param string $qrData Encrypted QR data
     * @param User|null $scanner
     * @return array ['success' => bool, 'message' => string, 'data' => array|null, 'debug' => array (only in development)]
     */
    public function recordAttendance(string $qrData, ?User $scanner = null): array
    {
        $startTime = microtime(true);
        
        try {
            Log::info('AttendanceService: Starting attendance recording', [
                'scanner_id' => $scanner?->id,
                'qr_length' => strlen($qrData),
            ]);

            // Validate QR code with detailed pipeline
            $validationResult = $this->validateQRCode($qrData);
            
            if (!$validationResult['valid']) {
                $duration = round((microtime(true) - $startTime) * 1000, 2);
                $this->logScanAttempt($qrData, $scanner, false, $validationResult['reason'], $duration);
                
                $response = [
                    'success' => false,
                    'message' => $validationResult['message'],
                    'data' => null,
                ];
                
                if (config('app.debug')) {
                    $response['debug'] = [
                        'step' => $validationResult['step'],
                        'reason' => $validationResult['reason'],
                        'duration_ms' => $duration,
                    ];
                }
                
                return $response;
            }

            $data = $validationResult['data'];
            $ticketId = $data['ticket_id'];
            $role = $data['role'];

            // Get ticket with mahasiswa and event (using cache for performance)
            try {
                $ticket = ScannerCacheService::getTicket($ticketId);
            } catch (\Exception $e) {
                $duration = round((microtime(true) - $startTime) * 1000, 2);
                Log::error('AttendanceService: Database error while fetching ticket', [
                    'error' => $e->getMessage(),
                    'ticket_id' => $ticketId,
                ]);
                $this->logScanAttempt($qrData, $scanner, false, self::ERROR_DATABASE, $duration);
                
                return $this->buildErrorResponse(self::ERROR_DATABASE, $duration);
            }
            
            if (!$ticket) {
                $duration = round((microtime(true) - $startTime) * 1000, 2);
                $this->logScanAttempt($qrData, $scanner, false, self::ERROR_TICKET_NOT_FOUND, $duration);
                
                return $this->buildErrorResponse(self::ERROR_TICKET_NOT_FOUND, $duration);
            }

            // Validate event is active
            if ($ticket->graduationEvent) {
                $event = $ticket->graduationEvent;
                $now = now();
                
                if ($event->date && $event->date->isFuture()) {
                    $duration = round((microtime(true) - $startTime) * 1000, 2);
                    Log::warning('AttendanceService: Event not started yet', [
                        'event_id' => $event->id,
                        'event_date' => $event->date->toIso8601String(),
                    ]);
                    $this->logScanAttempt($qrData, $scanner, false, self::ERROR_EVENT_NOT_ACTIVE, $duration);
                    
                    return $this->buildErrorResponse(self::ERROR_EVENT_NOT_ACTIVE, $duration);
                }
            }

            // Record attendance with transaction
            DB::beginTransaction();
            
            try {
                Log::info('AttendanceService: Recording attendance in transaction', [
                    'ticket_id' => $ticketId,
                    'role' => $role,
                ]);
                
                $attendance = new Attendance();
                $attendance->graduation_ticket_id = $ticketId;
                $attendance->role = $role;
                $attendance->scanned_by = $scanner?->id;
                $attendance->scanned_at = now();
                $attendance->save();
                
                DB::commit();
                
                $duration = round((microtime(true) - $startTime) * 1000, 2);
                $this->logScanAttempt($qrData, $scanner, true, 'success', $duration);
                
                Log::info('AttendanceService: Attendance recorded successfully', [
                    'ticket_id' => $ticketId,
                    'role' => $role,
                    'duration_ms' => $duration,
                ]);

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
            } catch (\Illuminate\Database\QueryException $e) {
                DB::rollBack();
                $duration = round((microtime(true) - $startTime) * 1000, 2);
                
                Log::error('AttendanceService: Database transaction failed', [
                    'error' => $e->getMessage(),
                    'error_code' => $e->getCode(),
                    'ticket_id' => $ticketId,
                    'role' => $role,
                ]);
                
                $this->logScanAttempt($qrData, $scanner, false, self::ERROR_TRANSACTION_FAILED, $duration);
                
                return $this->buildErrorResponse(self::ERROR_TRANSACTION_FAILED, $duration);
            } catch (\Exception $e) {
                DB::rollBack();
                $duration = round((microtime(true) - $startTime) * 1000, 2);
                
                Log::error('AttendanceService: Transaction failed with exception', [
                    'error' => $e->getMessage(),
                    'error_type' => get_class($e),
                    'ticket_id' => $ticketId,
                    'role' => $role,
                ]);
                
                $this->logScanAttempt($qrData, $scanner, false, self::ERROR_TRANSACTION_FAILED, $duration);
                
                return $this->buildErrorResponse(self::ERROR_TRANSACTION_FAILED, $duration);
            }
        } catch (\Throwable $e) {
            $duration = round((microtime(true) - $startTime) * 1000, 2);
            
            Log::error('AttendanceService: Unexpected exception occurred', [
                'error' => $e->getMessage(),
                'error_type' => get_class($e),
                'file' => $e->getFile(),
                'line' => $e->getLine(),
                'trace' => $e->getTraceAsString(),
            ]);
            
            $this->logScanAttempt($qrData, $scanner, false, self::ERROR_SYSTEM . ': ' . $e->getMessage(), $duration);
            
            return $this->buildErrorResponse(self::ERROR_SYSTEM, $duration);
        }
    }

    /**
     * Validate scanned QR code with step-by-step validation pipeline
     *
     * @param string $qrData
     * @return array ['valid' => bool, 'message' => string, 'data' => array|null, 'reason' => string, 'step' => string]
     */
    public function validateQRCode(string $qrData): array
    {
        // Step 1: Format Validation
        Log::debug('AttendanceService: Validation Step 1 - Format check');
        
        if (empty($qrData)) {
            Log::warning('AttendanceService: Validation failed at Step 1 - Empty QR data');
            return $this->buildValidationError(
                self::ERROR_EMPTY_QR,
                'format_check'
            );
        }
        
        if (strlen($qrData) > 1000) {
            Log::warning('AttendanceService: Validation failed at Step 1 - QR data too long', [
                'length' => strlen($qrData),
            ]);
            return $this->buildValidationError(
                self::ERROR_QR_TOO_LONG,
                'format_check',
                ['length' => strlen($qrData)]
            );
        }
        
        Log::debug('AttendanceService: Validation Step 1 passed', [
            'qr_length' => strlen($qrData),
        ]);

        // Step 2: Decryption
        Log::debug('AttendanceService: Validation Step 2 - Decryption');
        
        if (config('app.debug')) {
            // Log partial raw data for debugging (first 50 chars)
            Log::debug('AttendanceService: Raw QR data (partial)', [
                'data_preview' => substr($qrData, 0, 50) . '...',
            ]);
        }
        
        try {
            $data = $this->qrCodeService->decryptQRData($qrData);
        } catch (\InvalidArgumentException $e) {
            Log::error('AttendanceService: Decryption failed - Invalid argument', [
                'error' => $e->getMessage(),
            ]);
            return $this->buildValidationError(
                self::ERROR_DECRYPTION_FAILED,
                'decryption',
                ['error_type' => 'InvalidArgumentException']
            );
        } catch (\RuntimeException $e) {
            Log::error('AttendanceService: Decryption failed - Runtime error', [
                'error' => $e->getMessage(),
            ]);
            return $this->buildValidationError(
                self::ERROR_DECRYPTION_EXCEPTION,
                'decryption',
                ['error_type' => 'RuntimeException']
            );
        } catch (\Exception $e) {
            Log::error('AttendanceService: Decryption failed - Unexpected exception', [
                'error' => $e->getMessage(),
                'error_type' => get_class($e),
            ]);
            return $this->buildValidationError(
                self::ERROR_DECRYPTION_EXCEPTION,
                'decryption',
                ['error_type' => get_class($e)]
            );
        }

        if (!$data || !is_array($data)) {
            Log::warning('AttendanceService: Validation failed at Step 2 - Decryption returned invalid data', [
                'data_type' => gettype($data),
            ]);
            return $this->buildValidationError(
                self::ERROR_DECRYPTION_FAILED,
                'decryption'
            );
        }
        
        Log::debug('AttendanceService: Validation Step 2 passed', [
            'decrypted_keys' => array_keys($data),
        ]);

        // Step 3: Structure Validation
        Log::debug('AttendanceService: Validation Step 3 - Structure validation');
        
        $requiredFields = ['ticket_id', 'role', 'event_id'];
        $missingFields = [];
        
        foreach ($requiredFields as $field) {
            if (!isset($data[$field]) || $data[$field] === null || $data[$field] === '') {
                $missingFields[] = $field;
            }
        }
        
        if (!empty($missingFields)) {
            Log::warning('AttendanceService: Validation failed at Step 3 - Missing required fields', [
                'missing_fields' => $missingFields,
                'provided_fields' => array_keys($data),
            ]);
            return $this->buildValidationError(
                self::ERROR_MISSING_FIELDS,
                'structure_validation',
                ['missing_fields' => $missingFields]
            );
        }
        
        // Validate role value
        $validRoles = ['mahasiswa', 'pendamping1', 'pendamping2'];
        if (!in_array($data['role'], $validRoles, true)) {
            Log::warning('AttendanceService: Validation failed at Step 3 - Invalid role', [
                'role' => $data['role'],
                'valid_roles' => $validRoles,
            ]);
            return $this->buildValidationError(
                self::ERROR_INVALID_ROLE,
                'structure_validation',
                ['provided_role' => $data['role'], 'valid_roles' => $validRoles]
            );
        }
        
        // Validate ticket_id is numeric
        if (!is_numeric($data['ticket_id']) || $data['ticket_id'] <= 0) {
            Log::warning('AttendanceService: Validation failed at Step 3 - Invalid ticket_id', [
                'ticket_id' => $data['ticket_id'],
            ]);
            return $this->buildValidationError(
                self::ERROR_MISSING_FIELDS,
                'structure_validation',
                ['invalid_field' => 'ticket_id']
            );
        }
        
        Log::debug('AttendanceService: Validation Step 3 passed', [
            'ticket_id' => $data['ticket_id'],
            'role' => $data['role'],
            'event_id' => $data['event_id'],
        ]);

        // Step 4: Database Lookup
        Log::debug('AttendanceService: Validation Step 4 - Database lookup', [
            'ticket_id' => $data['ticket_id'],
        ]);
        
        try {
            $ticket = GraduationTicket::find($data['ticket_id']);
        } catch (\Exception $e) {
            Log::error('AttendanceService: Database error during ticket lookup', [
                'error' => $e->getMessage(),
                'ticket_id' => $data['ticket_id'],
            ]);
            return $this->buildValidationError(
                self::ERROR_DATABASE,
                'database_lookup',
                ['error' => $e->getMessage()]
            );
        }
        
        if (!$ticket) {
            Log::warning('AttendanceService: Validation failed at Step 4 - Ticket not found', [
                'ticket_id' => $data['ticket_id'],
            ]);
            return $this->buildValidationError(
                self::ERROR_TICKET_NOT_FOUND,
                'database_lookup',
                ['ticket_id' => $data['ticket_id']]
            );
        }
        
        // Check if ticket is expired
        if ($ticket->expires_at && $ticket->expires_at->isPast()) {
            Log::warning('AttendanceService: Validation failed at Step 4 - Ticket expired', [
                'ticket_id' => $data['ticket_id'],
                'expires_at' => $ticket->expires_at->toIso8601String(),
                'now' => now()->toIso8601String(),
            ]);
            return $this->buildValidationError(
                self::ERROR_TICKET_EXPIRED,
                'database_lookup',
                [
                    'ticket_id' => $data['ticket_id'],
                    'expires_at' => $ticket->expires_at->toIso8601String(),
                ]
            );
        }
        
        Log::debug('AttendanceService: Validation Step 4 passed', [
            'ticket_id' => $ticket->id,
            'ticket_status' => 'active',
        ]);

        // Step 5: Duplicate Check
        Log::debug('AttendanceService: Validation Step 5 - Duplicate check', [
            'ticket_id' => $data['ticket_id'],
            'role' => $data['role'],
        ]);
        
        try {
            $isDuplicate = $this->checkDuplicate($data['ticket_id'], $data['role']);
        } catch (\Exception $e) {
            Log::error('AttendanceService: Database error during duplicate check', [
                'error' => $e->getMessage(),
                'ticket_id' => $data['ticket_id'],
                'role' => $data['role'],
            ]);
            return $this->buildValidationError(
                self::ERROR_DATABASE,
                'duplicate_check',
                ['error' => $e->getMessage()]
            );
        }
        
        if ($isDuplicate) {
            Log::warning('AttendanceService: Validation failed at Step 5 - Duplicate attendance', [
                'ticket_id' => $data['ticket_id'],
                'role' => $data['role'],
            ]);
            return $this->buildValidationError(
                self::ERROR_DUPLICATE,
                'duplicate_check',
                ['ticket_id' => $data['ticket_id'], 'role' => $data['role']]
            );
        }
        
        Log::debug('AttendanceService: Validation Step 5 passed - No duplicate found');

        // All validation steps passed
        Log::info('AttendanceService: All validation steps passed', [
            'ticket_id' => $data['ticket_id'],
            'role' => $data['role'],
        ]);

        return [
            'valid' => true,
            'message' => 'QR Code valid',
            'data' => $data,
            'reason' => 'valid',
            'step' => 'complete',
        ];
    }

    /**
     * Check for duplicate attendance using optimized parallel validation
     *
     * @param int $ticketId
     * @param string $role
     * @return bool
     */
    public function checkDuplicate(int $ticketId, string $role): bool
    {
        // Use optimized parallel validation for faster duplicate detection
        $result = ParallelValidationService::optimizedValidation($ticketId, $role);

        if (isset($result['error'])) {
            // Fallback to simple query if optimization fails
            Log::warning('AttendanceService: Parallel validation failed, falling back to simple query', [
                'error' => $result['error'],
                'ticket_id' => $ticketId,
            ]);

            return Attendance::where('graduation_ticket_id', $ticketId)
                ->where('role', $role)
                ->exists();
        }

        return $result['isDuplicate'] ?? false;
    }

    /**
     * Build validation error response
     *
     * @param string $errorCode
     * @param string $step
     * @param array $context
     * @return array
     */
    protected function buildValidationError(string $errorCode, string $step, array $context = []): array
    {
        return [
            'valid' => false,
            'message' => $this->getErrorMessage($errorCode),
            'data' => null,
            'reason' => $errorCode,
            'step' => $step,
            'context' => config('app.debug') ? $context : [],
        ];
    }

    /**
     * Build error response for recordAttendance
     *
     * @param string $errorCode
     * @param float|null $durationMs
     * @return array
     */
    protected function buildErrorResponse(string $errorCode, ?float $durationMs = null): array
    {
        $response = [
            'success' => false,
            'message' => $this->getErrorMessage($errorCode),
            'data' => null,
        ];

        if (config('app.debug') && $durationMs !== null) {
            $response['debug'] = [
                'error_code' => $errorCode,
                'duration_ms' => $durationMs,
            ];
        }

        return $response;
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
     * Record attendance manually by NIM (for admin fallback when QR fails)
     *
     * @param string $nim Student NIM/NPM
     * @param ?int $eventId Graduation event ID (if null, uses active event)
     * @param User|null $scanner Admin user doing the manual check-in
     * @return array ['success' => bool, 'message' => string, 'data' => array|null]
     */
    public function recordManualAttendance(string $nim, ?int $eventId = null, ?User $scanner = null): array
    {
        $startTime = microtime(true);

        try {
            Log::info('AttendanceService: Starting manual attendance recording', [
                'nim' => $nim,
                'event_id' => $eventId,
                'admin_id' => $scanner?->id,
            ]);

            // Step 1: Validate NIM
            if (empty(trim($nim))) {
                $duration = round((microtime(true) - $startTime) * 1000, 2);
                Log::warning('AttendanceService: Manual attendance failed - Empty NIM', ['duration_ms' => $duration]);
                return [
                    'success' => false,
                    'message' => 'NIM/NPM tidak boleh kosong',
                    'data' => null,
                ];
            }

            // Step 2: Find mahasiswa by NIM
            $mahasiswa = \App\Models\Mahasiswa::where('npm', $nim)->first();

            if (!$mahasiswa) {
                $duration = round((microtime(true) - $startTime) * 1000, 2);
                Log::warning('AttendanceService: Manual attendance failed - Mahasiswa not found', [
                    'nim' => $nim,
                    'duration_ms' => $duration,
                ]);
                return [
                    'success' => false,
                    'message' => 'Mahasiswa dengan NIM ' . $nim . ' tidak ditemukan',
                    'data' => null,
                ];
            }

            // Step 3: Find or determine event (using cache for performance)
            $event = null;
            if ($eventId) {
                // For specific event ID, use direct cache lookup
                try {
                    $event = ScannerCacheService::getActiveEvent();
                    // Verify it matches requested ID if different from active
                    if ($event && $event->id !== $eventId) {
                        $event = GraduationEvent::find($eventId);
                    }
                } catch (\Exception $e) {
                    Log::warning('AttendanceService: Cache lookup failed, falling back to database', [
                        'error' => $e->getMessage(),
                    ]);
                    $event = GraduationEvent::find($eventId);
                }
            } else {
                // For active event, use cache
                try {
                    $event = ScannerCacheService::getActiveEvent();
                } catch (\Exception $e) {
                    Log::warning('AttendanceService: Cache lookup failed, falling back to database', [
                        'error' => $e->getMessage(),
                    ]);
                    $event = GraduationEvent::where('is_active', true)->first();
                }
            }

            if (!$event) {
                $duration = round((microtime(true) - $startTime) * 1000, 2);
                Log::warning('AttendanceService: Manual attendance failed - No active event', [
                    'event_id' => $eventId,
                    'duration_ms' => $duration,
                ]);
                return [
                    'success' => false,
                    'message' => 'Tidak ada acara wisuda aktif',
                    'data' => null,
                ];
            }

            // Step 4: Find graduation ticket
            $ticket = GraduationTicket::where('mahasiswa_id', $mahasiswa->id)
                ->where('graduation_event_id', $event->id)
                ->first();

            if (!$ticket) {
                $duration = round((microtime(true) - $startTime) * 1000, 2);
                Log::warning('AttendanceService: Manual attendance failed - No ticket found', [
                    'mahasiswa_id' => $mahasiswa->id,
                    'event_id' => $event->id,
                    'duration_ms' => $duration,
                ]);
                return [
                    'success' => false,
                    'message' => 'Mahasiswa tidak terdaftar untuk acara ini',
                    'data' => null,
                ];
            }

            // Step 5: Check if ticket is expired
            if ($ticket->expires_at && $ticket->expires_at->isPast()) {
                $duration = round((microtime(true) - $startTime) * 1000, 2);
                Log::warning('AttendanceService: Manual attendance failed - Ticket expired', [
                    'ticket_id' => $ticket->id,
                    'expires_at' => $ticket->expires_at->toIso8601String(),
                    'duration_ms' => $duration,
                ]);
                return [
                    'success' => false,
                    'message' => 'Tiket sudah kadaluarsa',
                    'data' => null,
                ];
            }

            // Step 6: Record attendance for all three roles in transaction
            DB::beginTransaction();

            try {
                $recordedRoles = [];
                $roles = ['mahasiswa', 'pendamping1', 'pendamping2'];

                foreach ($roles as $role) {
                    // Check if already recorded
                    $exists = Attendance::where('graduation_ticket_id', $ticket->id)
                        ->where('role', $role)
                        ->exists();

                    if (!$exists) {
                        $attendance = new Attendance();
                        $attendance->graduation_ticket_id = $ticket->id;
                        $attendance->role = $role;
                        $attendance->scanned_by = $scanner?->id;
                        $attendance->scanned_at = now();
                        $attendance->save();

                        $recordedRoles[] = $role;

                        Log::info('AttendanceService: Manual attendance recorded for role', [
                            'ticket_id' => $ticket->id,
                            'role' => $role,
                            'admin_id' => $scanner?->id,
                        ]);
                    }
                }

                DB::commit();

                $duration = round((microtime(true) - $startTime) * 1000, 2);

                Log::info('AttendanceService: Manual attendance completed successfully', [
                    'mahasiswa_id' => $mahasiswa->id,
                    'ticket_id' => $ticket->id,
                    'recorded_roles' => $recordedRoles,
                    'admin_id' => $scanner?->id,
                    'duration_ms' => $duration,
                ]);

                return [
                    'success' => true,
                    'message' => 'Absensi manual berhasil dicatat untuk ' . $mahasiswa->nama . ' (semua peran)',
                    'data' => [
                        'mahasiswa_id' => $mahasiswa->id,
                        'mahasiswa_name' => $mahasiswa->nama,
                        'npm' => $mahasiswa->npm,
                        'program_studi' => $mahasiswa->program_studi,
                        'event_name' => $event->name,
                        'recorded_roles' => $recordedRoles,
                        'recorded_at' => now()->format('Y-m-d H:i:s'),
                    ],
                ];
            } catch (\Illuminate\Database\QueryException $e) {
                DB::rollBack();
                $duration = round((microtime(true) - $startTime) * 1000, 2);

                Log::error('AttendanceService: Manual attendance - Database transaction failed', [
                    'error' => $e->getMessage(),
                    'error_code' => $e->getCode(),
                    'mahasiswa_id' => $mahasiswa->id,
                    'ticket_id' => $ticket->id,
                    'duration_ms' => $duration,
                ]);

                return [
                    'success' => false,
                    'message' => 'Gagal menyimpan data. Silakan coba lagi',
                    'data' => null,
                ];
            } catch (\Exception $e) {
                DB::rollBack();
                $duration = round((microtime(true) - $startTime) * 1000, 2);

                Log::error('AttendanceService: Manual attendance - Transaction failed', [
                    'error' => $e->getMessage(),
                    'error_type' => get_class($e),
                    'mahasiswa_id' => $mahasiswa->id,
                    'ticket_id' => $ticket->id,
                    'duration_ms' => $duration,
                ]);

                return [
                    'success' => false,
                    'message' => 'Terjadi kesalahan sistem. Silakan coba lagi',
                    'data' => null,
                ];
            }
        } catch (\Throwable $e) {
            $duration = round((microtime(true) - $startTime) * 1000, 2);

            Log::error('AttendanceService: Manual attendance - Unexpected exception', [
                'error' => $e->getMessage(),
                'error_type' => get_class($e),
                'file' => $e->getFile(),
                'line' => $e->getLine(),
                'duration_ms' => $duration,
            ]);

            return [
                'success' => false,
                'message' => 'Terjadi kesalahan sistem. Silakan coba lagi',
                'data' => null,
            ];
        }
    }

    /**
     * Log scan attempt for audit trail
     *
     * @param string $qrData
     * @param User|null $scanner
     * @param bool $success
     * @param string $reason
     * @param float|null $durationMs
     * @return void
     */
    protected function logScanAttempt(string $qrData, ?User $scanner, bool $success, string $reason, ?float $durationMs = null): void
    {
        $logData = [
            'scanner_id' => $scanner?->id,
            'scanner_name' => $scanner?->name,
            'result' => $success ? 'success' : 'failed',
            'reason' => $reason,
            'timestamp' => now()->toIso8601String(),
            'ip' => request()->ip(),
            'user_agent' => request()->userAgent(),
        ];

        if ($durationMs !== null) {
            $logData['duration_ms'] = $durationMs;
        }

        if ($success) {
            Log::info('QR Scan Attempt', $logData);
        } else {
            Log::warning('QR Scan Attempt', $logData);
        }
    }
}
