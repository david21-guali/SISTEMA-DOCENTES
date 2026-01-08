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
    /** @use HasFactory<\Database\Factories\InnovationFactory> */
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
        'reviewed_by',
        'review_notes',
        'reviewed_at',
    ];

    protected $casts = [
        'reviewed_at' => 'datetime',
    ];

    /**
     * Relaciones
     */
    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo<\App\Models\Profile, $this>
     */
    public function profile(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(Profile::class);
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo<\App\Models\InnovationType, $this>
     */
    public function innovationType(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(InnovationType::class);
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\MorphMany<\App\Models\Attachment, $this>
     */
    public function attachments(): \Illuminate\Database\Eloquent\Relations\MorphMany
    {
        return $this->morphMany(Attachment::class, 'attachable');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\MorphMany<\App\Models\Comment, $this>
     */
    public function comments(): \Illuminate\Database\Eloquent\Relations\MorphMany
    {
        return $this->morphMany(Comment::class, 'commentable')->latest();
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo<\App\Models\User, $this>
     */
    public function reviewer(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(User::class, 'reviewed_by');
    }

    /**
     * Accessor: user (via profile)
     */
    public function getUserAttribute(): ?User
    {
        return $this->profile->user;
    }

    /**
     * Accessor: evidence_files
     * Alias for attachments to satisfy legacy usage or export logic.
     * 
     * @return \Illuminate\Database\Eloquent\Collection
     */
    /**
     * @return \Illuminate\Database\Eloquent\Collection<int, \App\Models\Attachment>
     */
    public function getEvidenceFilesAttribute(): \Illuminate\Database\Eloquent\Collection
    {
        return $this->attachments;
    }

    /**
     * Atributos computados
     */
    public function getStatusColorAttribute(): string
    {
        return match($this->status) {
            'aprobada' => 'success',
            'rechazada' => 'danger',
            'en_revision' => 'warning',
            'propuesta' => 'info',
            'en_implementacion' => 'primary',
            'completada' => 'primary',
            default => 'secondary',
        };
    }

    /**
     * Scopes
     */
    /**
     * @param \Illuminate\Database\Eloquent\Builder<Innovation> $query
     * @return \Illuminate\Database\Eloquent\Builder<Innovation>
     */
    public function scopePropuesta($query)
    {
        return $query->where('status', 'propuesta');
    }

    /**
     * @param \Illuminate\Database\Eloquent\Builder<Innovation> $query
     * @return \Illuminate\Database\Eloquent\Builder<Innovation>
     */
    public function scopeCompleted($query)
    {
        return $query->where('status', 'completada');
    }

    /**
     * @param \Illuminate\Database\Eloquent\Builder<Innovation> $query
     * @return \Illuminate\Database\Eloquent\Builder<Innovation>
     */
    public function scopeBestRated($query)
    {
        return $query->where('impact_score', '>=', 4);
    }

    /**
     * @param \Illuminate\Database\Eloquent\Builder<Innovation> $query
     * @return \Illuminate\Database\Eloquent\Builder<Innovation>
     */
    public function scopeInProgress($query)
    {
        return $query->where('status', 'en_proceso');
    }

    /**
     * @param \Illuminate\Database\Eloquent\Builder<Innovation> $query
     * @return \Illuminate\Database\Eloquent\Builder<Innovation>
     */
    public function scopeApproved($query)
    {
        return $query->where('status', 'aprobada');
    }

    /**
     * @param \Illuminate\Database\Eloquent\Builder<Innovation> $query
     * @return \Illuminate\Database\Eloquent\Builder<Innovation>
     */
    public function scopePendingReview($query)
    {
        return $query->where('status', 'en_revision');
    }
}
