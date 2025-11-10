<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Mahasiswa extends Authenticatable
{
    use HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<string>
     */
    protected $fillable = [
        'npm',
        'password',
        'password_changed_at',
        'nama',
        'program_studi',
        'ipk',
        'yudisium',
        'email',
        'phone',
        'nomor_kursi',
        'judul_skripsi',
        'foto_wisuda',
    ];

    /**
     * The attributes that should be hidden.
     *
     * @var array<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'ipk' => 'float',
        'password' => 'hashed',
        'password_changed_at' => 'datetime',
    ];

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'mahasiswa';

    /**
     * Get the graduation tickets for the mahasiswa.
     */
    public function graduationTickets(): HasMany
    {
        return $this->hasMany(GraduationTicket::class);
    }

    /**
     * Get the full name accessor.
     */
    public function getFullNameAttribute(): string
    {
        return $this->nama;
    }

    /**
     * Get the active graduation ticket for this mahasiswa.
     */
    public function getActiveTicket(): ?GraduationTicket
    {
        return $this->graduationTickets()
            ->whereHas('graduationEvent', function ($query) {
                $query->where('is_active', true);
            })
            ->first();
    }

    /**
     * Get the name of the unique identifier for the user.
     */
    public function getAuthIdentifierName(): string
    {
        return 'npm';
    }

    /**
     * Get the password for the user.
     */
    public function getAuthPassword(): ?string
    {
        return $this->password;
    }

    /**
     * Get the foto wisuda URL.
     */
    public function getFotoWisudaUrlAttribute(): ?string
    {
        if ($this->foto_wisuda) {
            return asset('storage/graduation-photos/' . $this->foto_wisuda);
        }
        return null;
    }

    /**
     * Check if mahasiswa has uploaded foto wisuda.
     */
    public function hasFotoWisuda(): bool
    {
        return !empty($this->foto_wisuda);
    }

    /**
     * Check if mahasiswa has changed their password from the default.
     * Returns true if password_changed_at is set, false if NULL (never changed).
     */
    public function hasChangedPassword(): bool
    {
        return !is_null($this->password_changed_at);
    }

    /**
     * Mark password as changed by setting password_changed_at to current timestamp.
     */
    public function markPasswordAsChanged(): void
    {
        $this->update([
            'password_changed_at' => now(),
        ]);
    }
}
