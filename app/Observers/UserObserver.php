<?php

namespace App\Observers;

use App\Models\User;
use App\Services\ActivityLogService;

class UserObserver
{
    /**
     * Handle the User "created" event.
     */
    public function created(User $user): void
    {
        // Skip logging password field
        $logData = $user->getAttributes();
        unset($logData['password']);

        ActivityLogService::logCreate(
            model: 'User',
            modelId: $user->id,
            newValues: $logData,
            modelName: $user->name
        );
    }

    /**
     * Handle the User "updated" event.
     */
    public function updated(User $user): void
    {
        // Get the original values before update
        $original = $user->getOriginal();
        $current = $user->getAttributes();

        // Skip logging password field
        unset($original['password']);
        unset($current['password']);

        ActivityLogService::logUpdate(
            model: 'User',
            modelId: $user->id,
            oldValues: $original,
            newValues: $current,
            modelName: $user->name
        );
    }

    /**
     * Handle the User "deleted" event.
     */
    public function deleted(User $user): void
    {
        // Log the deleted values (without password)
        $logData = $user->getAttributes();
        unset($logData['password']);

        ActivityLogService::logDelete(
            model: 'User',
            modelId: $user->id,
            oldValues: $logData,
            modelName: $user->name
        );
    }

    /**
     * Handle the User "restored" event.
     */
    public function restored(User $user): void
    {
        //
    }

    /**
     * Handle the User "force deleted" event.
     */
    public function forceDeleted(User $user): void
    {
        //
    }
}
