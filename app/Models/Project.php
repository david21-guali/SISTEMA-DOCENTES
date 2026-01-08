<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

/**
 * App\Models\Project
 *
 * @property int $id
 * @property string $title
 * @property string $description
 * @property string $status
 * @property int $profile_id
 * @property-read \App\Models\Profile $profile
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\Task[] $tasks
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\Profile[] $team
 * @property bool $is_active
 * @property string $final_report
 */
class Project extends Model
{
    /** @use HasFactory<\Database\Factories\ProjectFactory> */
    use HasFactory, \App\Traits\CleansNotifications;

    protected $fillable = [
        'title',
        'description',
        'objectives',
        'category_id',
        'profile_id',
        'start_date',
        'end_date',
        'status',
        'budget',
        'impact_description',
        'completion_percentage',
    ];

    protected $casts = [
        'start_date' => 'date',
        'end_date' => 'date',
    ];

    /**
     * Relaciones
     */
    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo<\App\Models\Category, $this>
     */
    public function category(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo<\App\Models\Profile, $this>
     */
    public function profile(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(Profile::class);
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany<\App\Models\Profile, $this>
     */
    public function team(): \Illuminate\Database\Eloquent\Relations\BelongsToMany
    {
        return $this->belongsToMany(Profile::class, 'project_profile')
                    ->withPivot('role')
                    ->withTimestamps();
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany<\App\Models\Task, $this>
     */
    public function tasks(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(Task::class);
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany<\App\Models\Evaluation, $this>
     */
    public function evaluations(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(Evaluation::class);
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\MorphMany<\App\Models\Attachment, $this>
     */
    public function attachments(): \Illuminate\Database\Eloquent\Relations\MorphMany
    {
        return $this->morphMany(Attachment::class, 'attachable');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasOne<\App\Models\Evaluation, $this>
     */
    public function latestEvaluation(): \Illuminate\Database\Eloquent\Relations\HasOne
    {
        return $this->hasOne(Evaluation::class)->latestOfMany();
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\MorphMany<\App\Models\Comment, $this>
     */
    public function comments(): \Illuminate\Database\Eloquent\Relations\MorphMany
    {
        return $this->morphMany(Comment::class, 'commentable')->latest();
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany<\App\Models\Resource, $this>
     */
    public function resources(): \Illuminate\Database\Eloquent\Relations\BelongsToMany
    {
        return $this->belongsToMany(Resource::class)
                    ->withPivot('quantity', 'assigned_date', 'notes')
                    ->withTimestamps();
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany<\App\Models\Meeting, $this>
     */
    public function meetings(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(Meeting::class);
    }

    /**
     * Scope projects visible to a specific user.
     */
    /**
     * Scope projects visible to a specific user.
     * 
     * @param \Illuminate\Database\Eloquent\Builder<Project> $query
     * @param \App\Models\User $user
     * @return \Illuminate\Database\Eloquent\Builder<Project>
     */
    public function scopeForUser($query, \App\Models\User $user)
    {
        if ($user->hasRole(['admin', 'coordinador'])) {
            return $query;
        }

        return $query->where('profile_id', $user->profile->id)
                     ->orWhereHas('team', function ($q) use ($user) {
                         $q->where('profiles.id', $user->profile->id);
                     });
    }

    /**
     * Atributos computados
     */
    public function getStatusColorAttribute(): string
    {
        if ($this->is_actually_at_risk) return 'danger';

        return match($this->status) {
            'planificacion' => 'secondary',
            'en_progreso' => 'primary',
            'finalizado' => 'success',
            default => 'secondary',
        };
    }

    public function getIsOverdueAttribute(): bool
    {
        return $this->end_date < now() && $this->status !== 'finalizado';
    }

    /**
     * Scopes
     */
    /**
     * @param \Illuminate\Database\Eloquent\Builder<Project> $query
     * @return \Illuminate\Database\Eloquent\Builder<Project>
     */
    public function scopeActive($query)
    {
        return $query->where('status', 'en_progreso');
    }

    /**
     * @param \Illuminate\Database\Eloquent\Builder<Project> $query
     * @return \Illuminate\Database\Eloquent\Builder<Project>
     */
    public function scopeFinished($query)
    {
        return $query->where('status', 'finalizado');
    }

    /**
     * @param \Illuminate\Database\Eloquent\Builder<Project> $query
     * @return \Illuminate\Database\Eloquent\Builder<Project>
     */
    public function scopeAtRisk($query)
    {
        return $query->where('status', 'en_riesgo')
                     ->orWhere(fn($q) => $q->where('status', 'en_progreso')->where('end_date', '<', now()));
    }

    public function getIsActuallyAtRiskAttribute(): bool
    {
        return $this->status === 'en_riesgo' || ($this->status === 'en_progreso' && $this->end_date < now());
    }
}
