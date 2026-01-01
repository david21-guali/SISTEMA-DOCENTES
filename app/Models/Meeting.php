<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class Meeting extends Model
{
    use HasFactory;

    protected $fillable = [
        'project_id',
        'title',
        'description',
        'meeting_date',
        'location',
        'created_by',
        'status',
        'notes',
    ];

    protected $casts = [
        'meeting_date' => 'datetime',
    ];

    /**
     * Relaciones
     */
    public function project()
    {
        return $this->belongsTo(Project::class);
    }

    public function creator()
    {
        return $this->belongsTo(Profile::class, 'created_by');
    }

    public function participants()
    {
        return $this->belongsToMany(Profile::class, 'meeting_profile', 'meeting_id', 'profile_id')
                    ->withPivot('attendance')
                    ->withTimestamps();
    }

    /**
     * Atributos computados
     */
    public function getStatusColorAttribute()
    {
        return match($this->status) {
            'pendiente' => 'primary',
            'completada' => 'success',
            'cancelada' => 'secondary',
            default => 'primary',
        };
    }

    public function getStatusLabelAttribute()
    {
        return match($this->status) {
            'pendiente' => 'Pendiente',
            'completada' => 'Completada',
            'cancelada' => 'Cancelada',
            default => $this->status,
        };
    }

    public function getIsUpcomingAttribute()
    {
        return $this->meeting_date > Carbon::now() && $this->status === 'pendiente';
    }

    public function getIsPastAttribute()
    {
        return $this->meeting_date < Carbon::now();
    }

    /**
     * Scopes
     */
    public function scopeUpcoming($query)
    {
        return $query->where('meeting_date', '>', Carbon::now())
                     ->where('status', 'pendiente')
                     ->orderBy('meeting_date', 'asc');
    }

    public function scopePast($query)
    {
        return $query->where('meeting_date', '<', Carbon::now())
                     ->orderBy('meeting_date', 'desc');
    }

    public function scopePending($query)
    {
        return $query->where('status', 'pendiente');
    }

    public function scopeCompleted($query)
    {
        return $query->where('status', 'completada');
    }

    public function scopeForUser($query, $userId)
    {
        // $userId is a User ID (Auth::id()), but relationships use Profile ID.
        // We need to find the profile associated with this user.
        // Alternatively, we can assume the controller passes Auth::user()->profile->id if we change the controller,
        // BUT strict signature says $userId. Let's resolve profile id here to be safe and robust.
        
        $user = \App\Models\User::find($userId);
        if (!$user || !$user->profile) return $query->whereRaw('0 = 1'); // No profile, no meetings

        $profileId = $user->profile->id;

        return $query->where('created_by', $profileId)
                     ->orWhereHas('participants', function($q) use ($profileId) {
                         $q->where('profiles.id', $profileId);
                     });
    }

    protected static function booted()
    {
        static::deleting(function ($meeting) {
            // Delete generic notifications related to this meeting safe method
            \Illuminate\Notifications\DatabaseNotification::where('data', 'LIKE', '%"meeting_id":%')
                ->get()
                ->each(function ($notification) use ($meeting) {
                    if (($notification->data['meeting_id'] ?? null) == $meeting->id) {
                        $notification->delete();
                    }
                });
        });
    }
}
