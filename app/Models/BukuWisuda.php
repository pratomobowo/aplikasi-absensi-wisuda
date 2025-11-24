<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Str;

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
        'slug',
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
     * Boot method to auto-generate slug from filename
     */
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($model) {
            if (!$model->slug) {
                $model->slug = Str::slug($model->filename, '-');

                // Handle duplicate slugs
                $count = static::where('slug', 'like', $model->slug . '%')->count();
                if ($count > 0) {
                    $model->slug = $model->slug . '-' . ($count + 1);
                }
            }
        });

        static::updating(function ($model) {
            if ($model->isDirty('filename') && !$model->isDirty('slug')) {
                $model->slug = Str::slug($model->filename, '-');

                // Handle duplicate slugs when updating
                $count = static::where('slug', 'like', $model->slug . '%')
                    ->where('id', '!=', $model->id)
                    ->count();
                if ($count > 0) {
                    $model->slug = $model->slug . '-' . ($count + 1);
                }
            }
        });
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
        return route('buku-wisuda.download', ['slug' => $this->slug, 'token' => base64_encode($this->file_path)]);
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
