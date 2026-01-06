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
    use HasFactory, \App\Traits\CleansNotifications;

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
                    ->using(MeetingParticipant::class)
                    ->withPivot('attendance')
                    ->withTimestamps();
    }

    /**
     * Atributos computados
     */
    public function getStatusColorAttribute()
    {
        return match($this->status) {
            'completada' => 'success',
            'cancelada' => 'secondary',
            default => 'primary',
        };
    }

    public function getFormattedDateAttribute()
    {
        return $this->meeting_date?->format('d/m/Y H:i');
    }

    public function getStatusLabelAttribute()
    {
        return ucfirst($this->status ?? 'pendiente');
    }

    public function getIsUpcomingAttribute()
    {
        return $this->meeting_date > now() && $this->status === 'pendiente';
    }

    public function getIsPastAttribute()
    {
        return $this->meeting_date < now();
    }

    /**
     * Scopes
     */
    public function scopeUpcoming($query)
    {
        return $query->where('meeting_date', '>', now())
                     ->where('status', 'pendiente')
                     ->orderBy('meeting_date', 'asc');
    }

    public function scopePast($query)
    {
        return $query->where('meeting_date', '<', now())
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

    public function scopeForUser($query, $profileId)
    {
        return $query->where('created_by', $profileId)
                     ->orWhereHas('participants', fn($q) => $q->where('profiles.id', $profileId));
    }
}
