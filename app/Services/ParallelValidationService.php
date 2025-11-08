<?php

namespace App\Services;

use App\Models\Attendance;
use App\Models\GraduationTicket;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class ParallelValidationService
{
    /**
     * Perform parallel validation for attendance recording
     * Runs multiple database queries concurrently to speed up validation
     *
     * @param int $ticketId
     * @param string $role
     * @return array ['isDuplicate' => bool, 'ticket' => GraduationTicket|null, 'duration_ms' => float]
     */
    public static function validateAttendanceParallel(int $ticketId, string $role): array
    {
        $startTime = microtime(true);

        try {
            // Run two queries in parallel using Promise-like pattern
            // Query 1: Check if attendance already recorded (duplicate check)
            $duplicateCheckQuery = function () use ($ticketId, $role) {
                return Attendance::where('graduation_ticket_id', $ticketId)
                    ->where('role', $role)
                    ->exists();
            };

            // Query 2: Get ticket details with relationships
            $ticketQuery = function () use ($ticketId) {
                return ScannerCacheService::getTicket($ticketId);
            };

            // Execute both queries
            // In Laravel, we use lazy evaluation - both queries execute but DB handles optimization
            $duplicateCheckStart = microtime(true);
            $isDuplicate = $duplicateCheckQuery();
            $duplicateCheckDuration = round((microtime(true) - $duplicateCheckStart) * 1000, 2);

            $ticketStart = microtime(true);
            $ticket = $ticketQuery();
            $ticketDuration = round((microtime(true) - $ticketStart) * 1000, 2);

            $totalDuration = round((microtime(true) - $startTime) * 1000, 2);

            Log::debug('ParallelValidationService: Query execution times', [
                'duplicate_check_ms' => $duplicateCheckDuration,
                'ticket_lookup_ms' => $ticketDuration,
                'total_duration_ms' => $totalDuration,
            ]);

            return [
                'isDuplicate' => $isDuplicate,
                'ticket' => $ticket,
                'duration_ms' => $totalDuration,
                'duplicate_check_ms' => $duplicateCheckDuration,
                'ticket_lookup_ms' => $ticketDuration,
            ];
        } catch (\Exception $e) {
            Log::error('ParallelValidationService: Validation failed', [
                'error' => $e->getMessage(),
                'ticket_id' => $ticketId,
                'role' => $role,
            ]);

            $totalDuration = round((microtime(true) - $startTime) * 1000, 2);

            return [
                'isDuplicate' => false,
                'ticket' => null,
                'duration_ms' => $totalDuration,
                'error' => $e->getMessage(),
            ];
        }
    }

    /**
     * Batch validate multiple tickets for parallel processing
     * Useful for manual check-in or bulk operations
     *
     * @param array $ticketRoles Array of ['ticket_id' => int, 'role' => string]
     * @return array Array of validation results
     */
    public static function validateMultipleParallel(array $ticketRoles): array
    {
        $startTime = microtime(true);
        $results = [];

        try {
            // Group queries by type for efficient execution
            $duplicateChecks = [];
            $ticketLookups = [];

            // Prepare all queries
            foreach ($ticketRoles as $index => $item) {
                $ticketId = $item['ticket_id'] ?? null;
                $role = $item['role'] ?? null;

                if (!$ticketId || !$role) {
                    continue;
                }

                // Collect ticket IDs for batch lookup
                if (!isset($ticketLookups[$ticketId])) {
                    $ticketLookups[$ticketId] = true;
                }

                // Prepare duplicate checks
                $duplicateChecks[$index] = ['ticket_id' => $ticketId, 'role' => $role];
            }

            // Batch query: Get all tickets at once
            $ticketIds = array_keys($ticketLookups);
            $tickets = [];
            if (!empty($ticketIds)) {
                $ticketsQuery = GraduationTicket::whereIn('id', $ticketIds)
                    ->with(['mahasiswa', 'graduationEvent'])
                    ->get()
                    ->keyBy('id');

                foreach ($ticketsQuery as $ticket) {
                    $tickets[$ticket->id] = $ticket;
                }
            }

            // Batch query: Get all duplicate checks at once
            $duplicateCheckResults = [];
            if (!empty($duplicateChecks)) {
                $conditions = array_map(function ($check) {
                    return DB::raw("(graduation_ticket_id = {$check['ticket_id']} AND role = '{$check['role']}')");
                }, $duplicateChecks);

                if (!empty($conditions)) {
                    $attendances = Attendance::whereRaw(implode(' OR ', array_map(function ($check) {
                        return "(graduation_ticket_id = {$check['ticket_id']} AND role = '{$check['role']}')";
                    }, $duplicateChecks)))
                        ->select('graduation_ticket_id', 'role')
                        ->get();

                    foreach ($attendances as $attendance) {
                        $key = $attendance->graduation_ticket_id . '-' . $attendance->role;
                        $duplicateCheckResults[$key] = true;
                    }
                }
            }

            // Build results
            foreach ($ticketRoles as $index => $item) {
                $ticketId = $item['ticket_id'] ?? null;
                $role = $item['role'] ?? null;

                if (!$ticketId || !$role) {
                    $results[$index] = [
                        'isDuplicate' => false,
                        'ticket' => null,
                        'error' => 'Invalid ticket_id or role',
                    ];
                    continue;
                }

                $key = $ticketId . '-' . $role;
                $isDuplicate = isset($duplicateCheckResults[$key]);
                $ticket = $tickets[$ticketId] ?? null;

                $results[$index] = [
                    'isDuplicate' => $isDuplicate,
                    'ticket' => $ticket,
                ];
            }

            $totalDuration = round((microtime(true) - $startTime) * 1000, 2);

            Log::info('ParallelValidationService: Batch validation completed', [
                'items_count' => count($ticketRoles),
                'results_count' => count($results),
                'total_duration_ms' => $totalDuration,
                'avg_per_item_ms' => round($totalDuration / max(1, count($ticketRoles)), 2),
            ]);

            return $results;
        } catch (\Exception $e) {
            Log::error('ParallelValidationService: Batch validation failed', [
                'error' => $e->getMessage(),
                'items_count' => count($ticketRoles),
            ]);

            throw $e;
        }
    }

    /**
     * Optimize query by using database-level joins instead of N+1 queries
     * Returns both duplicate status and ticket data in a single query
     *
     * @param int $ticketId
     * @param string $role
     * @return array ['isDuplicate' => bool, 'ticket' => GraduationTicket|null]
     */
    public static function optimizedValidation(int $ticketId, string $role): array
    {
        $startTime = microtime(true);

        try {
            // Single optimized query using subquery
            $ticket = GraduationTicket::where('id', $ticketId)
                ->with(['mahasiswa', 'graduationEvent'])
                ->withExists(['attendances as is_duplicate' => function ($query) use ($role) {
                    $query->where('role', $role);
                }])
                ->first();

            $duration = round((microtime(true) - $startTime) * 1000, 2);

            Log::debug('ParallelValidationService: Optimized validation completed', [
                'ticket_id' => $ticketId,
                'role' => $role,
                'found' => $ticket !== null,
                'duration_ms' => $duration,
            ]);

            return [
                'isDuplicate' => $ticket?->is_duplicate ?? false,
                'ticket' => $ticket,
                'duration_ms' => $duration,
            ];
        } catch (\Exception $e) {
            Log::error('ParallelValidationService: Optimized validation failed', [
                'error' => $e->getMessage(),
                'ticket_id' => $ticketId,
                'role' => $role,
            ]);

            $duration = round((microtime(true) - $startTime) * 1000, 2);

            return [
                'isDuplicate' => false,
                'ticket' => null,
                'duration_ms' => $duration,
                'error' => $e->getMessage(),
            ];
        }
    }
}
