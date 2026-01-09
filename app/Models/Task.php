<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;
use App\Traits\HasCalendarEvents;

/**
 * App\Models\Task
 *
 * @property int $id
 * @property int $project_id
 * @property string $title
 * @property string $description
 * @property int $assigned_to
 * @property \Illuminate\Support\Carbon|null $due_date
 * @property string $status
 * @property string $priority
 * @property \Illuminate\Support\Carbon|null $completion_date
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\Project $project
 * @property-read \App\Models\Profile $assignedProfile
 * @property bool $is_active
 */
class Task extends Model
{
    /** @use HasFactory<\Database\Factories\TaskFactory> */
    use HasFactory, \App\Traits\CleansNotifications, HasCalendarEvents;

    protected $fillable = [
        'project_id',
        'title',
        'description',
        'assigned_to',
        'due_date',
        'status',
        'priority',
        'completion_date',
    ];

    protected $casts = [
        'due_date' => 'date',
        'completion_date' => 'datetime',
    ];

    /**
     * Relaciones
     */
    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo<\App\Models\Project, $this>
     */
    public function project(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(Project::class);
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo<\App\Models\Profile, $this>
     */
    public function assignedProfile(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(Profile::class, 'assigned_to');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany<\App\Models\Profile, $this>
     */
    public function assignees(): \Illuminate\Database\Eloquent\Relations\BelongsToMany
    {
        return $this->belongsToMany(Profile::class, 'task_profile')
                    ->withTimestamps();
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\MorphMany<\App\Models\Attachment, $this>
     */
    public function attachments(): \Illuminate\Database\Eloquent\Relations\MorphMany
    {
        return $this->morphMany(Attachment::class, 'attachable');
    }

    /**
     * Accessor: assignedUser (via assignedProfile)
     */
    public function getAssignedUserAttribute(): ?User
    {
        return $this->assignedProfile->user ?? null;
    }

    /**
     * Atributos computados
     */
    public function getIsOverdueAttribute(): bool
    {
        return $this->due_date < now() && $this->status !== 'completada';
    }

    public function getPriorityColorAttribute(): string
    {
        return match($this->priority) {
            'baja' => 'success',
            'media' => 'warning',
            'alta' => 'danger',
            default => 'secondary',
        };
    }

    /**
     * Scopes
     */
    /**
     * Get the calendar URL for the task.
     */
    protected function getCalendarUrl(): string { return route('tasks.show', $this->id); }
    protected function getCalendarColor(): string { return '#10b981'; }

    /**
     * @param \Illuminate\Database\Eloquent\Builder<Task> $query
     * @return \Illuminate\Database\Eloquent\Builder<Task>
     */
    public function scopeCompleted($query)
    {
        return $query->where('status', 'completada');
    }

    /**
     * @param \Illuminate\Database\Eloquent\Builder<Task> $query
     * @return \Illuminate\Database\Eloquent\Builder<Task>
     */
    public function scopeInProgress($query)
    {
        return $query->where('status', 'en_progreso');
    }

    /**
     * @param \Illuminate\Database\Eloquent\Builder<Task> $query
     * @return \Illuminate\Database\Eloquent\Builder<Task>
     */
    public function scopeOverdue($query)
    {
        return $query->where('due_date', '<', now())
                     ->where('status', '!=', 'completada');
    }

    /**
     * Scope a query to only include pending tasks.
     * 
     * @param \Illuminate\Database\Eloquent\Builder<Task> $query
     * @return \Illuminate\Database\Eloquent\Builder<Task>
     */
    public function scopePending($query)
    {
        return $query->where('status', 'pendiente');
    }

    /**
     * Scope tasks visible to a specific user.
     * 
     * @param \Illuminate\Database\Eloquent\Builder<Task> $query
     * @param \App\Models\User|int $user
     * @return \Illuminate\Database\Eloquent\Builder<Task>
     */
    public function scopeForUser($query, $user)
    {
        // Handle if only an ID is passed
        if (is_numeric($user)) {
            $user = \App\Models\User::find($user);
        }

        if (!$user) {
            return $query;
        }

        if ($user->hasRole(['admin', 'coordinador'])) {
            return $query;
        }

        $profileId = $user->profile?->id;

        if (!$profileId) {
            return $query->whereRaw('1 = 0');
        }

        return $query->where('assigned_to', $profileId)
                     ->orWhereHas('project', function ($q) use ($profileId) {
                         /** @var \Illuminate\Database\Eloquent\Builder<\Illuminate\Database\Eloquent\Model> $q */
                         $q->where('projects.profile_id', $profileId);
                     });
    }
}
