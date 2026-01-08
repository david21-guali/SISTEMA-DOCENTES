<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

/**
 * @property \Illuminate\Database\Eloquent\Model $attachable
 */
class Attachment extends Model
{
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
     * 
     * @return \Illuminate\Database\Eloquent\Relations\MorphTo<\Illuminate\Database\Eloquent\Model, $this>
     */
    public function attachable(): \Illuminate\Database\Eloquent\Relations\MorphTo
    {
        return $this->morphTo();
    }

    /**
     * Profile who uploaded the file
     */
    /**
     * Profile who uploaded the file
     * 
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo<\App\Models\Profile, $this>
     */
    public function uploader(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(Profile::class, 'uploaded_by');
    }

    /**
     * Get the full URL to the file
     */
    /**
     * Get the full URL to the file
     */
    public function getUrlAttribute(): string
    {
        return Storage::disk('public')->url($this->path);
    }

    /**
     * Check if file is an image (Attribute)
     */
    public function getIsImageAttribute(): bool
    {
        return $this->isImage();
    }

    /**
     * Check if file is a PDF (Attribute)
     */
    public function getIsPdfAttribute(): bool
    {
        return $this->isPdf();
    }

    /**
     * Check if file can be previewed (Image or PDF)
     */
    public function getIsPreviewableAttribute(): bool
    {
        return $this->isImage() || $this->isPdf();
    }

    /**
     * Check if file is an image
     */
    public function isImage(): bool
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
    public function isPdf(): bool
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
    /**
     * Check if file is a Word document
     */
    public function isWord(): bool
    {
        return in_array($this->mime_type, [
            'application/msword',
            'application/vnd.openxmlformats-officedocument.wordprocessingml.document'
        ]);
    }

    /**
     * Check if file is an Excel spreadsheet
     */
    /**
     * Check if file is an Excel spreadsheet
     */
    public function isExcel(): bool
    {
        return in_array($this->mime_type, [
            'application/vnd.ms-excel',
            'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet'
        ]);
    }

    /**
     * Get appropriate icon class based on file type.
     * 
     * @return string
     */
    public function getIconAttribute(): string
    {
        if ($this->isImage()) {
            return 'fas fa-file-image text-success';
        }

        return $this->getNonImageIcon();
    }

    /**
     * Determine icon for non-image files.
     * 
     * @return string
     */
    private function getNonImageIcon(): string
    {
        $icons = [
            'pdf'   => 'fas fa-file-pdf text-danger',
            'word'  => 'fas fa-file-word text-primary',
            'excel' => 'fas fa-file-excel text-success',
        ];

        foreach ($icons as $type => $icon) {
            $method = 'is' . ucfirst($type);
            if ($this->$method()) {
                return $icon;
            }
        }

        return 'fas fa-file text-muted';
    }

    /**
     * Get human-readable file size.
     * 
     * @return string
     */
    public function getHumanSizeAttribute(): string
    {
        $bytes = $this->size;
        $units = ['B', 'KB', 'MB', 'GB'];
        $i = 0;
        
        while ($bytes >= 1024 && $i < count($units) - 1) {
            $bytes /= 1024;
            $i++;
        }
        
        return round($bytes, 0) . ' ' . $units[$i];
    }

    /**
     * Delete file from storage when model is deleted.
     * 
     * @return void
     */
    protected static function booted(): void
    {
        static::deleting(function ($attachment) {
            $attachment->deleteFileFromDisk();
        });
    }

    /**
     * Remove physical file from dedicated disk.
     * 
     * @return void
     */
    protected function deleteFileFromDisk(): void
    {
        Storage::disk('public')->delete($this->path);
    }
}
