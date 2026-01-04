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
    use HasFactory;

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
     * Atributos computados
     */
    public function getStatusColorAttribute()
    {
        if ($this->is_actually_at_risk) {
            return 'danger';
        }

        return match($this->status) {
            'planificacion' => 'secondary',
            'en_progreso' => 'primary',
            'finalizado' => 'success',
            default => 'secondary',
        };
    }

    public function getIsOverdueAttribute()
    {
        return $this->end_date < Carbon::now() && $this->status !== 'finalizado';
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
        return $query->where(function($q) {
            $q->where('status', 'en_riesgo')
              ->orWhere(function($sq) {
                  $sq->where('status', 'en_progreso')
                     ->where('end_date', '<', Carbon::now());
              });
        });
    }

    public function getIsActuallyAtRiskAttribute()
    {
        if ($this->status === 'en_riesgo') return true;
        if ($this->status === 'en_progreso' && $this->end_date < Carbon::now()) return true;
        return false;
    }
    public function recalculateProgress()
    {
        $totalTasks = $this->tasks()->count();
        
        if ($totalTasks == 0) {
            $this->completion_percentage = 0;
        } else {
            $completedTasks = $this->tasks()->where('status', 'completada')->count();
            $this->completion_percentage = (int) round(($completedTasks / $totalTasks) * 100);
        }
        
        // Si llega a 100% y no está finalizado, opcionalmente podríamos sugerir/cambiar estado, 
        // pero por ahora solo actualizamos el porcentaje.
        if ($this->completion_percentage == 100 && $this->status == 'en_progreso') {
            // $this->status = 'finalizado'; // Opcional: automatizar cierre
        }

        $this->save();
    }
    protected static function booted()
    {
        static::deleting(function ($project) {
            // Delete generic notifications related to this project safe method
            // Uses LIKE to find candidates and PHP to verify, avoiding SQL JSON errors
            \Illuminate\Notifications\DatabaseNotification::where('data', 'LIKE', '%"project_id":%')
                ->get()
                ->each(function ($notification) use ($project) {
                    if (($notification->data['project_id'] ?? null) == $project->id) {
                        $notification->delete();
                    }
                });
        });
    }
}
