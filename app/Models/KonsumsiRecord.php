<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class KonsumsiRecord extends Model
{
    protected $fillable = [
        'graduation_ticket_id',
        'scanned_by',
        'scanned_at',
    ];

    protected $casts = [
        'scanned_at' => 'datetime',
    ];

    /**
     * Get the graduation ticket associated with this konsumsi record
     */
    public function graduationTicket(): BelongsTo
    {
        return $this->belongsTo(GraduationTicket::class);
    }

    /**
     * Get the user who scanned this konsumsi
     */
    public function scannedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'scanned_by');
    }
}
