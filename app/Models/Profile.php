<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

/**
 * App\Models\Profile
 *
 * @property int $id
 * @property int $user_id
 * @property string|null $avatar
 * @property string|null $phone
 * @property string|null $department
 * @property string|null $specialty
 * @property string|null $position
 * @property string|null $location
 * @property string|null $about
 * @property array|null $notification_preferences
 * @property bool $is_active
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read User $user
 * @property-read \Illuminate\Database\Eloquent\Collection|Project[] $projects
 * @property-read \Illuminate\Database\Eloquent\Collection|Project[] $assignedProjects
 * @property-read \Illuminate\Database\Eloquent\Collection|Task[] $assignedTasks
 * @property-read int|null $assigned_projects_count
 * @property-read int|null $assigned_tasks_count
 */
class Profile extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'avatar',
        'phone',
        'department',
        'specialty',
        'position',
        'location',
        'about',
        'notification_preferences',
        'is_active',
    ];

    protected $casts = [
        'notification_preferences' => 'array',
        'is_active' => 'boolean',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function projects(): HasMany
    {
        return $this->hasMany(Project::class, 'profile_id');
    }

    public function assignedProjects(): BelongsToMany
    {
        return $this->belongsToMany(Project::class, 'project_profile', 'profile_id', 'project_id')
                    ->withPivot('role')
                    ->withTimestamps();
    }

    public function assignedTasks(): BelongsToMany
    {
        return $this->belongsToMany(Task::class, 'task_profile', 'profile_id', 'task_id')
                    ->withTimestamps();
    }

    public function innovations(): HasMany
    {
        return $this->hasMany(Innovation::class, 'profile_id');
    }

    public function comments(): HasMany
    {
        return $this->hasMany(Comment::class, 'profile_id');
    }

    public function meetings(): BelongsToMany
    {
        return $this->belongsToMany(Meeting::class, 'meeting_profile', 'profile_id', 'meeting_id')
                    ->withPivot('attendance', 'rejection_reason')
                    ->withTimestamps();
    }

    public function createdMeetings(): HasMany
    {
        return $this->hasMany(Meeting::class, 'created_by');
    }

    public function evaluations(): HasMany
    {
        return $this->hasMany(Evaluation::class, 'evaluator_id');
    }

    public function attachments(): HasMany
    {
        return $this->hasMany(Attachment::class, 'uploaded_by');
    }
}
