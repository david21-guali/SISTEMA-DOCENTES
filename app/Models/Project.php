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
 */
class Project extends Model
{
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
    public function category(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    public function profile(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(Profile::class);
    }

    public function team(): \Illuminate\Database\Eloquent\Relations\BelongsToMany
    {
        return $this->belongsToMany(Profile::class, 'project_profile')
                    ->withPivot('role')
                    ->withTimestamps();
    }

    public function tasks(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(Task::class);
    }

    public function evaluations(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(Evaluation::class);
    }

    public function attachments()
    {
        return $this->morphMany(Attachment::class, 'attachable');
    }

    public function latestEvaluation(): \Illuminate\Database\Eloquent\Relations\HasOne
    {
        return $this->hasOne(Evaluation::class)->latestOfMany();
    }

    public function comments()
    {
        return $this->hasMany(Comment::class)->latest();
    }

    public function resources()
    {
        return $this->belongsToMany(Resource::class)
                    ->withPivot('quantity', 'assigned_date', 'notes')
                    ->withTimestamps();
    }

    public function meetings()
    {
        return $this->hasMany(Meeting::class);
    }

    /**
     * Scope projects visible to a specific user.
     */
    public function scopeForUser($query, \App\Models\User $user)
    {
        if ($user->hasRole(['admin', 'coordinador'])) {
            return $query;
        }

        return $query->where('profile_id', $user->profile->id)
                     ->orWhereHas('team', function ($q) use ($user) {
                         $q->where('profile_id', $user->profile->id);
                     });
    }

    /**
     * Atributos computados
     */
    public function getStatusColorAttribute()
    {
        if ($this->is_actually_at_risk) return 'danger';

        return match($this->status) {
            'planificacion' => 'secondary',
            'en_progreso' => 'primary',
            'finalizado' => 'success',
            default => 'secondary',
        };
    }

    public function getIsOverdueAttribute()
    {
        return $this->end_date < now() && $this->status !== 'finalizado';
    }

    /**
     * Scopes
     */
    public function scopeActive($query)
    {
        return $query->where('status', 'en_progreso');
    }

    public function scopeFinished($query)
    {
        return $query->where('status', 'finalizado');
    }

    public function scopeAtRisk($query)
    {
        return $query->where('status', 'en_riesgo')
                     ->orWhere(fn($q) => $q->where('status', 'en_progreso')->where('end_date', '<', now()));
    }

    public function getIsActuallyAtRiskAttribute()
    {
        return $this->status === 'en_riesgo' || ($this->status === 'en_progreso' && $this->end_date < now());
    }
}
