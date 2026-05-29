<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

class LayoutWisuda extends Model
{
    protected $fillable = [
        'title',
        'filename',
        'original_filename',
        'file_size',
    ];

    public function getUrlAttribute()
    {
        if ($this->filename) {
            return asset('storage/layout-wisuda/' . $this->filename);
        }
        return null;
    }

    public function getHumanFileSizeAttribute()
    {
        $bytes = $this->file_size;
        if ($bytes >= 1048576) {
            return number_format($bytes / 1048576, 2) . ' MB';
        } elseif ($bytes >= 1024) {
            return number_format($bytes / 1024, 2) . ' KB';
        }
        return $bytes . ' B';
    }
}
