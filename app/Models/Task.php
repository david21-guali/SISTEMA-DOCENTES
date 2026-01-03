<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

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
 */
class Task extends Model
{
    use HasFactory;

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
    public function project(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(Project::class);
    }

    public function assignedProfile(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(Profile::class, 'assigned_to');
    }

    public function assignees(): \Illuminate\Database\Eloquent\Relations\BelongsToMany
    {
        return $this->belongsToMany(Profile::class, 'task_profile')
                    ->withTimestamps();
    }

    public function attachments(): \Illuminate\Database\Eloquent\Relations\MorphMany
    {
        return $this->morphMany(Attachment::class, 'attachable');
    }

    /**
     * Accessor: assignedUser (via assignedProfile)
     */
   public function getAssignedUserAttribute()
    {
        return $this->assignedProfile->user ?? null;
    }

    /**
     * Atributos computados
     */
    public function getIsOverdueAttribute()
    {
        return $this->due_date < Carbon::now() && $this->status !== 'completada';
    }

    public function getPriorityColorAttribute()
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
    public function scopePending($query)
    {
        return $query->where('status', 'pendiente');
    }

    public function scopeCompleted($query)
    {
        return $query->where('status', 'completada');
    }

    public function scopeOverdue($query)
    {
        return $query->where('due_date', '<', Carbon::now())
                     ->where('status', '!=', 'completada');
    }

    protected static function booted()
    {
        static::deleting(function ($task) {
            // Delete generic notifications related to this task safe method
            \Illuminate\Notifications\DatabaseNotification::where('data', 'LIKE', '%"task_id":%')
                ->get()
                ->each(function ($notification) use ($task) {
                    if (($notification->data['task_id'] ?? null) == $task->id) {
                        $notification->delete();
                    }
                });
        });
    }
}
