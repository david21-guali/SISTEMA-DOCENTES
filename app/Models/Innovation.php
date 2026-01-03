<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * App\Models\Innovation
 *
 * @property int $id
 * @property int $profile_id
 * @property int $innovation_type_id
 * @property string $title
 * @property string $description
 * @property string $methodology
 * @property string $expected_results
 * @property string $actual_results
 * @property string $status
 * @property int|null $impact_score
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\Profile $profile
 * @property-read \App\Models\InnovationType $innovationType
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\Attachment[] $attachments
 * @property-read int|null $attachments_count
 * @property-read \App\Models\User|null $user
 */
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
     * Accessor: user (via profile)
     */
    public function getUserAttribute()
    {
        return $this->profile->user ?? null;
    }

    /**
     * Atributos computados
     */
    public function getStatusColorAttribute()
    {
        return match($this->status) {
            'propuesta' => 'info',
            'en_implementacion', 'en_revision' => 'warning',
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
