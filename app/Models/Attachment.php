<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

class Attachment extends Model
{
    use HasFactory;

    protected $fillable = [
        'attachable_type',
        'attachable_id',
        'filename',
        'original_name',
        'mime_type',
        'size',
        'path',
        'uploaded_by',
    ];

    /**
     * Polymorphic relationship - can belong to Project or Task
     */
    public function attachable()
    {
        return $this->morphTo();
    }

    /**
     * Profile who uploaded the file
     */
    public function uploader()
    {
        return $this->belongsTo(Profile::class, 'uploaded_by');
    }

    /**
     * Get the full URL to the file
     */
    public function getUrlAttribute()
    {
        return Storage::disk('public')->url($this->path);
    }

    /**
     * Check if file is an image
     */
    public function isImage()
    {
        // Check by mime type
        if ($this->mime_type && str_starts_with($this->mime_type, 'image/')) {
            return true;
        }
        
        // Fallback: check by extension
        $extension = strtolower(pathinfo($this->original_name, PATHINFO_EXTENSION));
        return in_array($extension, ['jpg', 'jpeg', 'png', 'gif', 'webp', 'bmp', 'svg']);
    }

    /**
     * Check if file is a PDF
     */
    public function isPdf()
    {
        // Check by mime type
        if ($this->mime_type === 'application/pdf') {
            return true;
        }
        
        // Fallback: check by extension
        $extension = strtolower(pathinfo($this->original_name, PATHINFO_EXTENSION));
        return $extension === 'pdf';
    }

    /**
     * Check if file is a Word document
     */
    public function isWord()
    {
        return in_array($this->mime_type, [
            'application/msword',
            'application/vnd.openxmlformats-officedocument.wordprocessingml.document'
        ]);
    }

    /**
     * Check if file is an Excel spreadsheet
     */
    public function isExcel()
    {
        return in_array($this->mime_type, [
            'application/vnd.ms-excel',
            'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet'
        ]);
    }

    /**
     * Get appropriate icon class based on file type
     */
    public function getIconAttribute()
    {
        if ($this->isImage()) return 'fas fa-file-image text-success';
        if ($this->isPdf()) return 'fas fa-file-pdf text-danger';
        if ($this->isWord()) return 'fas fa-file-word text-primary';
        if ($this->isExcel()) return 'fas fa-file-excel text-success';
        return 'fas fa-file text-muted';
    }

    /**
     * Get human-readable file size
     */
    public function getHumanSizeAttribute()
    {
        $bytes = $this->size;
        $units = ['B', 'KB', 'MB', 'GB'];
        
        for ($i = 0; $bytes >= 1024 && $i < count($units) - 1; $i++) {
            $bytes /= 1024;
        }
        
        return round($bytes, 0) . ' ' . $units[$i];
    }

    /**
     * Delete file from storage when model is deleted
     */
    protected static function booted()
    {
        static::deleting(function ($attachment) {
            Storage::disk('public')->delete($attachment->path);
        });
    }
}
