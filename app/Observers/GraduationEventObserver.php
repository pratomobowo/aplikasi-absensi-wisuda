<?php

namespace App\Observers;

use App\Models\GraduationEvent;
use App\Services\ScannerCacheService;
use Illuminate\Support\Facades\Log;

class GraduationEventObserver
{
    /**
     * Handle the GraduationEvent "created" event.
     */
    public function created(GraduationEvent $event): void
    {
        Log::info('GraduationEventObserver: Event created, invalidating scanner cache', [
            'event_id' => $event->id,
            'event_name' => $event->name,
        ]);

        ScannerCacheService::invalidateAll();
    }

    /**
     * Handle the GraduationEvent "updated" event.
     */
    public function updated(GraduationEvent $event): void
    {
        Log::info('GraduationEventObserver: Event updated, invalidating scanner cache', [
            'event_id' => $event->id,
            'event_name' => $event->name,
            'is_active' => $event->is_active,
        ]);

        ScannerCacheService::invalidateAll();
    }

    /**
     * Handle the GraduationEvent "deleted" event.
     */
    public function deleted(GraduationEvent $event): void
    {
        Log::info('GraduationEventObserver: Event deleted, invalidating scanner cache', [
            'event_id' => $event->id,
            'event_name' => $event->name,
        ]);

        ScannerCacheService::invalidateAll();
    }

    /**
     * Handle the GraduationEvent "restored" event.
     */
    public function restored(GraduationEvent $event): void
    {
        Log::info('GraduationEventObserver: Event restored, invalidating scanner cache', [
            'event_id' => $event->id,
        ]);

        ScannerCacheService::invalidateAll();
    }

    /**
     * Handle the GraduationEvent "force deleted" event.
     */
    public function forceDeleted(GraduationEvent $event): void
    {
        Log::info('GraduationEventObserver: Event force deleted, invalidating scanner cache', [
            'event_id' => $event->id,
        ]);

        ScannerCacheService::invalidateAll();
    }
}
