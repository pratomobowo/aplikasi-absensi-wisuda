<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Attendance extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<string>
     */
    protected $fillable = [
        'graduation_ticket_id',
        'role',
        'scanned_by',
        'scanned_at',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'role' => 'string',
            'scanned_at' => 'datetime',
        ];
    }

    /**
     * Get the graduation ticket that owns the attendance.
     */
    public function graduationTicket(): BelongsTo
    {
        return $this->belongsTo(GraduationTicket::class);
    }

    /**
     * Get the user who scanned the QR code.
     */
    public function scannedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'scanned_by');
    }

    /**
     * Scope a query to filter by role.
     */
    public function scopeByRole($query, string $role)
    {
        return $query->where('role', $role);
    }

    /**
     * Scope a query to only include today's attendances.
     */
    public function scopeToday($query)
    {
        return $query->whereDate('scanned_at', now()->toDateString());
    }
}
