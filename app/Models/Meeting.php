<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Carbon\Carbon;

/**
 * App\Models\Meeting
 *
 * @property int $id
 * @property int|null $project_id
 * @property int|null $created_by
 * @property \Illuminate\Support\Carbon|null $meeting_date
 * @property string|null $status
 * @property-read \Illuminate\Database\Eloquent\Collection|Profile[] $participants
 * @property-read Profile|null $creator
 * @property-read string $status_color
 * @property-read string $status_label
 * @property-read bool $is_upcoming
 * @property-read bool $is_past
 */
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
    public function project(): BelongsTo
    {
        return $this->belongsTo(Project::class);
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(Profile::class, 'created_by');
    }

    public function participants(): BelongsToMany
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

    public function getFormattedDateAttribute()
    {
        return $this->meeting_date ? $this->meeting_date->format('d/m/Y H:i') : null;
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
