<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Mahasiswa extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<string>
     */
    protected $fillable = [
        'npm',
        'nama',
        'prodi',
        'fakultas',
        'ipk',
        'yudisium',
        'email',
        'phone',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'ipk' => 'decimal:2',
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
}
