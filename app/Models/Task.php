<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

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
    public function project()
    {
        return $this->belongsTo(Project::class);
    }

    public function assignedProfile()
    {
        return $this->belongsTo(Profile::class, 'assigned_to');
    }

    public function assignees()
    {
        return $this->belongsToMany(Profile::class, 'task_profile')
                    ->withTimestamps();
    }

    public function attachments()
    {
        return $this->morphMany(Attachment::class, 'attachable');
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
