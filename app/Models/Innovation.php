<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Innovation extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'description',
        'profile_id',
        'innovation_type_id',
        'methodology',
        'expected_results',
        'actual_results',
        'status',
        'impact_score',
    ];

    protected $casts = [
        // 'evidence_files' removed
    ];

    /**
     * Relaciones
     */
    public function profile(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(Profile::class);
    }

    public function innovationType(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(InnovationType::class);
    }

    public function attachments()
    {
        return $this->morphMany(Attachment::class, 'attachable');
    }

    /**
     * Atributos computados
     */
    public function getStatusColorAttribute()
    {
        return match($this->status) {
            'propuesta' => 'info',
            'en_implementacion' => 'warning',
            'completada' => 'success',
            default => 'secondary',
        };
    }

    /**
     * Scopes
     */
    public function scopeCompleted($query)
    {
        return $query->where('status', 'completada');
    }

    public function scopeBestRated($query)
    {
        return $query->orderByDesc('impact_score');
    }

    public function scopeInProgress($query)
    {
        return $query->where('status', 'en_implementacion');
    }
}
