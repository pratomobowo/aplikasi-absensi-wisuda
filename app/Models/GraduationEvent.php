<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class GraduationEvent extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<string>
     */
    protected $fillable = [
        'name',
        'date',
        'time',
        'location_name',
        'location_address',
        'location_lat',
        'location_lng',
        'maps_url',
        'is_active',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'date' => 'date',
            'time' => 'datetime',
            'location_lat' => 'decimal:8',
            'location_lng' => 'decimal:8',
            'is_active' => 'boolean',
        ];
    }

    /**
     * Get the graduation tickets for the event.
     */
    public function graduationTickets(): HasMany
    {
        return $this->hasMany(GraduationTicket::class);
    }

    /**
     * Get the buku wisuda for the event.
     */
    public function bukuWisuda(): HasMany
    {
        return $this->hasMany(BukuWisuda::class);
    }

    /**
     * Scope a query to only include active events.
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Scope a query to only include upcoming events.
     */
    public function scopeUpcoming($query)
    {
        return $query->where('date', '>=', now()->toDateString())
            ->orderBy('date', 'asc');
    }

    /**
     * Get the Google Maps embed URL for the event location.
     */
    public function getMapEmbedUrl(): ?string
    {
        if (!$this->location_lat || !$this->location_lng) {
            return null;
        }

        $apiKey = config('services.google_maps.api_key', '');
        
        return sprintf(
            'https://www.google.com/maps/embed/v1/place?key=%s&q=%s,%s',
            $apiKey,
            $this->location_lat,
            $this->location_lng
        );
    }
}
