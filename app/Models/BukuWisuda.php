<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class BukuWisuda extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<string>
     */
    protected $fillable = [
        'graduation_event_id',
        'filename',
        'file_path',
        'file_size',
        'mime_type',
        'download_count',
        'uploaded_at',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'uploaded_at' => 'datetime',
        ];
    }

    /**
     * Get the graduation event that owns the buku wisuda.
     */
    public function graduationEvent(): BelongsTo
    {
        return $this->belongsTo(GraduationEvent::class);
    }

    /**
     * Get the download URL for the buku wisuda.
     */
    public function getDownloadUrl(): string
    {
        return route('buku-wisuda.download', ['id' => $this->id, 'token' => base64_encode($this->file_path)]);
    }

    /**
     * Increment download count.
     */
    public function recordDownload(): void
    {
        $this->increment('download_count');
    }

    /**
     * Get human-readable file size.
     */
    public function getHumanFileSize(): string
    {
        $size = $this->file_size;
        $units = ['B', 'KB', 'MB', 'GB'];
        $unitIndex = 0;

        while ($size >= 1024 && $unitIndex < count($units) - 1) {
            $size /= 1024;
            $unitIndex++;
        }

        return round($size, 2) . ' ' . $units[$unitIndex];
    }
}
