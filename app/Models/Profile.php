<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

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
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function projects()
    {
        return $this->hasMany(Project::class, 'profile_id');
    }

    public function assignedProjects()
    {
        return $this->belongsToMany(Project::class, 'project_profile', 'profile_id', 'project_id')
                    ->withPivot('role')
                    ->withTimestamps();
    }

    public function assignedTasks()
    {
        return $this->belongsToMany(Task::class, 'task_profile', 'profile_id', 'task_id')
                    ->withTimestamps();
    }

    public function innovations()
    {
        return $this->hasMany(Innovation::class, 'profile_id');
    }

    public function comments()
    {
        return $this->hasMany(Comment::class, 'profile_id');
    }

    public function meetings()
    {
        return $this->belongsToMany(Meeting::class, 'meeting_profile', 'profile_id', 'meeting_id')
                    ->withPivot('attendance', 'rejection_reason')
                    ->withTimestamps();
    }

    public function createdMeetings()
    {
        return $this->hasMany(Meeting::class, 'created_by');
    }

    public function evaluations()
    {
        return $this->hasMany(Evaluation::class, 'evaluator_id');
    }

    public function attachments()
    {
        return $this->hasMany(Attachment::class, 'uploaded_by');
    }
}
