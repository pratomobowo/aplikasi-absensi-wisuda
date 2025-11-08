<?php

namespace App\Observers;

use App\Models\Mahasiswa;
use App\Services\ActivityLogService;

class MahasiswaObserver
{
    /**
     * Handle the Mahasiswa "created" event.
     */
    public function created(Mahasiswa $mahasiswa): void
    {
        // Skip logging password field
        $logData = $mahasiswa->getAttributes();
        unset($logData['password']);

        ActivityLogService::logCreate(
            model: 'Mahasiswa',
            modelId: $mahasiswa->id,
            newValues: $logData,
            modelName: $mahasiswa->nama ?? $mahasiswa->npm
        );
    }

    /**
     * Handle the Mahasiswa "updated" event.
     */
    public function updated(Mahasiswa $mahasiswa): void
    {
        // Get the original values before update
        $original = $mahasiswa->getOriginal();
        $current = $mahasiswa->getAttributes();

        // Skip logging password field
        unset($original['password']);
        unset($current['password']);

        ActivityLogService::logUpdate(
            model: 'Mahasiswa',
            modelId: $mahasiswa->id,
            oldValues: $original,
            newValues: $current,
            modelName: $mahasiswa->nama ?? $mahasiswa->npm
        );
    }

    /**
     * Handle the Mahasiswa "deleted" event.
     */
    public function deleted(Mahasiswa $mahasiswa): void
    {
        // Log the deleted values (without password)
        $logData = $mahasiswa->getAttributes();
        unset($logData['password']);

        ActivityLogService::logDelete(
            model: 'Mahasiswa',
            modelId: $mahasiswa->id,
            oldValues: $logData,
            modelName: $mahasiswa->nama ?? $mahasiswa->npm
        );
    }

    /**
     * Handle the Mahasiswa "restored" event.
     */
    public function restored(Mahasiswa $mahasiswa): void
    {
        //
    }

    /**
     * Handle the Mahasiswa "force deleted" event.
     */
    public function forceDeleted(Mahasiswa $mahasiswa): void
    {
        //
    }
}
