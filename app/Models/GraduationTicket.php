<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Str;

class GraduationTicket extends Model
{
    use HasFactory;

    /**
     * The "booting" method of the model.
     */
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($ticket) {
            if (empty($ticket->magic_link_token)) {
                $ticket->magic_link_token = Str::random(64);
            }
            if (empty($ticket->qr_token_mahasiswa)) {
                $ticket->qr_token_mahasiswa = Str::random(64);
            }
            if (empty($ticket->qr_token_pendamping1)) {
                $ticket->qr_token_pendamping1 = Str::random(64);
            }
            if (empty($ticket->qr_token_pendamping2)) {
                $ticket->qr_token_pendamping2 = Str::random(64);
            }
            if (empty($ticket->expires_at)) {
                $ticket->expires_at = $ticket->graduationEvent->event_date ?? now()->addDays(30);
            }
        });
    }

    /**
     * The attributes that are mass assignable.
     *
     * @var array<string>
     */
    protected $fillable = [
        'mahasiswa_id',
        'graduation_event_id',
        'magic_link_token',
        'qr_token_mahasiswa',
        'qr_token_pendamping1',
        'qr_token_pendamping2',
        'is_distributed',
        'distributed_at',
        'expires_at',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'is_distributed' => 'boolean',
            'distributed_at' => 'datetime',
            'expires_at' => 'datetime',
        ];
    }

    /**
     * Get the mahasiswa that owns the ticket.
     */
    public function mahasiswa(): BelongsTo
    {
        return $this->belongsTo(Mahasiswa::class);
    }

    /**
     * Get the graduation event that owns the ticket.
     */
    public function graduationEvent(): BelongsTo
    {
        return $this->belongsTo(GraduationEvent::class);
    }

    /**
     * Get the attendances for the ticket.
     */
    public function attendances(): HasMany
    {
        return $this->hasMany(Attendance::class);
    }

    /**
     * Generate a unique magic link token for the ticket.
     */
    public function generateMagicLink(): string
    {
        $token = Str::random(64);
        $this->magic_link_token = $token;
        $this->save();
        
        return route('invitation.show', ['token' => $token]);
    }

    /**
     * Generate QR tokens for mahasiswa and companions.
     */
    public function generateQRTokens(): array
    {
        $tokens = [
            'mahasiswa' => Str::random(64),
            'pendamping1' => Str::random(64),
            'pendamping2' => Str::random(64),
        ];

        $this->qr_token_mahasiswa = $tokens['mahasiswa'];
        $this->qr_token_pendamping1 = $tokens['pendamping1'];
        $this->qr_token_pendamping2 = $tokens['pendamping2'];
        $this->save();

        return $tokens;
    }

    /**
     * Check if the ticket has expired.
     */
    public function isExpired(): bool
    {
        if (!$this->expires_at) {
            return false;
        }

        return now()->isAfter($this->expires_at);
    }

    /**
     * Get the attendance status for the ticket.
     */
    public function getAttendanceStatus(): array
    {
        $attendances = $this->attendances;

        return [
            'mahasiswa' => $attendances->where('role', 'mahasiswa')->isNotEmpty(),
            'pendamping1' => $attendances->where('role', 'pendamping1')->isNotEmpty(),
            'pendamping2' => $attendances->where('role', 'pendamping2')->isNotEmpty(),
        ];
    }
}
