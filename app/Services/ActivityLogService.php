<?php

namespace App\Services;

use App\Models\ActivityLog;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Request;

class ActivityLogService
{
    /**
     * Log user activity
     *
     * @param string $action Action type (login, logout, view, create, update, delete, etc.)
     * @param string|null $model Model name (e.g., 'Mahasiswa', 'User', 'Attendance')
     * @param int|null $modelId ID of the affected record
     * @param string|null $description Human-readable description
     * @param array|null $oldValues Previous values for updates
     * @param array|null $newValues New values for create/update
     * @param string|null $modelName Human-readable name of the record
     */
    public static function log(
        string $action,
        ?string $model = null,
        ?int $modelId = null,
        ?string $description = null,
        ?array $oldValues = null,
        ?array $newValues = null,
        ?string $modelName = null
    ): ActivityLog {
        // Determine user type and ID
        $userType = null;
        $userId = null;
        $userName = null;
        $userEmail = null;

        // Check if mahasiswa is authenticated
        if (Auth::guard('mahasiswa')->check()) {
            $mahasiswa = Auth::guard('mahasiswa')->user();
            $userType = 'mahasiswa';
            $userId = $mahasiswa->id;
            $userName = $mahasiswa->nama ?? $mahasiswa->npm;
            $userEmail = $mahasiswa->email;
        }
        // Check if admin/user is authenticated
        elseif (Auth::check()) {
            $user = Auth::user();
            $userType = 'user';
            $userId = $user->id;
            $userName = $user->name;
            $userEmail = $user->email;
        }

        // Create activity log record
        return ActivityLog::create([
            'user_type' => $userType,
            'user_id' => $userId,
            'user_name' => $userName,
            'user_email' => $userEmail,
            'action' => $action,
            'model' => $model,
            'model_id' => $modelId,
            'model_name' => $modelName,
            'description' => $description,
            'old_values' => $oldValues,
            'new_values' => $newValues,
            'ip_address' => Request::ip(),
            'user_agent' => Request::header('User-Agent'),
            'url' => Request::url(),
            'method' => Request::method(),
        ]);
    }

    /**
     * Log login activity
     */
    public static function logLogin(?string $userName = null): ActivityLog
    {
        return self::log(
            action: 'login',
            description: "User {$userName} logged in"
        );
    }

    /**
     * Log logout activity
     */
    public static function logLogout(?string $userName = null): ActivityLog
    {
        return self::log(
            action: 'logout',
            description: "User {$userName} logged out"
        );
    }

    /**
     * Log view activity
     */
    public static function logView(
        string $model,
        int $modelId,
        ?string $modelName = null
    ): ActivityLog {
        return self::log(
            action: 'view',
            model: $model,
            modelId: $modelId,
            modelName: $modelName,
            description: "Viewed {$model} #{$modelId}"
        );
    }

    /**
     * Log create activity
     */
    public static function logCreate(
        string $model,
        int $modelId,
        array $newValues,
        ?string $modelName = null
    ): ActivityLog {
        return self::log(
            action: 'create',
            model: $model,
            modelId: $modelId,
            newValues: $newValues,
            modelName: $modelName,
            description: "Created {$model} #{$modelId}"
        );
    }

    /**
     * Log update activity
     */
    public static function logUpdate(
        string $model,
        int $modelId,
        array $oldValues,
        array $newValues,
        ?string $modelName = null
    ): ActivityLog {
        // Only log fields that actually changed
        $changedFields = [];
        foreach ($newValues as $key => $value) {
            if (!isset($oldValues[$key]) || $oldValues[$key] != $value) {
                $changedFields[$key] = [
                    'old' => $oldValues[$key] ?? null,
                    'new' => $value,
                ];
            }
        }

        return self::log(
            action: 'update',
            model: $model,
            modelId: $modelId,
            oldValues: $oldValues,
            newValues: $newValues,
            modelName: $modelName,
            description: "Updated {$model} #{$modelId} with fields: " . implode(', ', array_keys($changedFields))
        );
    }

    /**
     * Log delete activity
     */
    public static function logDelete(
        string $model,
        int $modelId,
        array $oldValues,
        ?string $modelName = null
    ): ActivityLog {
        return self::log(
            action: 'delete',
            model: $model,
            modelId: $modelId,
            oldValues: $oldValues,
            modelName: $modelName,
            description: "Deleted {$model} #{$modelId}"
        );
    }

    /**
     * Log password change activity
     */
    public static function logPasswordChange(?string $description = null): ActivityLog
    {
        return self::log(
            action: 'password_change',
            description: $description ?? 'Changed password'
        );
    }
}
