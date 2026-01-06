<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Notifications\Notifiable;

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
 * @property string|null $about
 * @property array<string, mixed>|null $notification_preferences
 * @property bool $is_active
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read User $user
 * @property-read \Illuminate\Database\Eloquent\Collection|Project[] $projects
 * @property-read \Illuminate\Database\Eloquent\Collection|Project[] $assignedProjects
 * @property-read \Illuminate\Database\Eloquent\Collection|Task[] $assignedTasks
 * @property-read int|null $assigned_projects_count
 * @property-read int|null $assigned_tasks_count
 * @property-read string $name
 * @property \Illuminate\Database\Eloquent\Relations\Pivot|null $pivot
 */
class Profile extends Model
{
    /** @use HasFactory<\Database\Factories\ProfileFactory> */
    use HasFactory, Notifiable;

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

    /**
     * Accessor for Name (delegates to User)
     */
    public function getNameAttribute(): string
    {
        return $this->user->name ?? 'Usuario';
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo<\App\Models\User, $this>
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany<\App\Models\Project, $this>
     */
    public function projects(): HasMany
    {
        return $this->hasMany(Project::class, 'profile_id');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany<\App\Models\Project, $this>
     */
    public function assignedProjects(): BelongsToMany
    {
        return $this->belongsToMany(Project::class, 'project_profile', 'profile_id', 'project_id')
                    ->withPivot('role')
                    ->withTimestamps();
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany<\App\Models\Task, $this>
     */
    public function assignedTasks(): BelongsToMany
    {
        return $this->belongsToMany(Task::class, 'task_profile', 'profile_id', 'task_id')
                    ->withTimestamps();
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany<\App\Models\Innovation, $this>
     */
    public function innovations(): HasMany
    {
        return $this->hasMany(Innovation::class, 'profile_id');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany<\App\Models\Comment, $this>
     */
    public function comments(): HasMany
    {
        return $this->hasMany(Comment::class, 'profile_id');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany<\App\Models\Meeting, $this, \App\Models\MeetingParticipant>
     */
    public function meetings(): BelongsToMany
    {
        /** @var \Illuminate\Database\Eloquent\Relations\BelongsToMany<\App\Models\Meeting, $this, \App\Models\MeetingParticipant> $relation */
        $relation = $this->belongsToMany(Meeting::class, 'meeting_profile', 'profile_id', 'meeting_id')
                    ->using(MeetingParticipant::class)
                    ->withPivot('attendance', 'rejection_reason')
                    ->withTimestamps();
        return $relation;
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany<\App\Models\Meeting, $this>
     */
    public function createdMeetings(): HasMany
    {
        return $this->hasMany(Meeting::class, 'created_by');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany<\App\Models\Evaluation, $this>
     */
    public function evaluations(): HasMany
    {
        return $this->hasMany(Evaluation::class, 'evaluator_id');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany<\App\Models\Attachment, $this>
     */
    public function attachments(): HasMany
    {
        return $this->hasMany(Attachment::class, 'uploaded_by');
    }
}
