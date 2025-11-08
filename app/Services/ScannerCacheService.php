<?php

namespace App\Services;

use App\Models\GraduationEvent;
use App\Models\GraduationTicket;
use Illuminate\Support\Facades\Cache;

class ScannerCacheService
{
    /**
     * Cache TTL in minutes
     */
    private const CACHE_TTL_MINUTES = 15;

    /**
     * Get active graduation event with caching
     *
     * @return GraduationEvent|null
     */
    public static function getActiveEvent(): ?GraduationEvent
    {
        return Cache::remember(
            'scanner:active_event',
            self::CACHE_TTL_MINUTES,
            function () {
                return GraduationEvent::where('is_active', true)->first();
            }
        );
    }

    /**
     * Get graduation ticket by ID with caching
     *
     * @param int $ticketId
     * @return GraduationTicket|null
     */
    public static function getTicket(int $ticketId): ?GraduationTicket
    {
        return Cache::remember(
            "scanner:ticket:{$ticketId}",
            self::CACHE_TTL_MINUTES,
            function () use ($ticketId) {
                return GraduationTicket::with(['mahasiswa', 'graduationEvent'])
                    ->find($ticketId);
            }
        );
    }

    /**
     * Get graduation event with all tickets (pre-loaded for bulk validation)
     *
     * @return GraduationEvent|null
     */
    public static function getActiveEventWithTickets(): ?GraduationEvent
    {
        return Cache::remember(
            'scanner:active_event_with_tickets',
            self::CACHE_TTL_MINUTES,
            function () {
                return GraduationEvent::where('is_active', true)
                    ->with('graduationTickets')
                    ->first();
            }
        );
    }

    /**
     * Invalidate all scanner caches (call when event/ticket changes)
     *
     * @return void
     */
    public static function invalidateAll(): void
    {
        Cache::forget('scanner:active_event');
        Cache::forget('scanner:active_event_with_tickets');
        // Note: Individual ticket caches need to be cleared per ID
        // Pattern-based cache clearing would require Redis
    }

    /**
     * Invalidate specific ticket cache
     *
     * @param int $ticketId
     * @return void
     */
    public static function invalidateTicket(int $ticketId): void
    {
        Cache::forget("scanner:ticket:{$ticketId}");
    }
}
