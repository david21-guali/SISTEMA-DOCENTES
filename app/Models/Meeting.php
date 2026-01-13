<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use App\Traits\HasCalendarEvents;
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
    /** @use HasFactory<\Database\Factories\MeetingFactory> */
    use HasFactory, HasCalendarEvents, \App\Traits\CleansNotifications, \App\Traits\HasMeetingExpiration, \App\Traits\HasMeetingStatusColor, \App\Traits\HasMeetingStatusLabel, \App\Traits\HasMeetingIsUpcoming, \App\Traits\HasMeetingIsPast, \App\Traits\HandlesMeetingScopes;

    protected $fillable = [
        'project_id',
        'title',
        'description',
        'meeting_date',
        'location',
        'type',
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
    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo<\App\Models\Project, $this>
     */
    public function project(): BelongsTo
    {
        return $this->belongsTo(Project::class);
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo<\App\Models\Profile, $this>
     */
    public function creator(): BelongsTo
    {
        return $this->belongsTo(Profile::class, 'created_by');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany<\App\Models\Profile, $this, \App\Models\MeetingParticipant>
     */
    public function participants(): BelongsToMany
    {
        /** @var \Illuminate\Database\Eloquent\Relations\BelongsToMany<\App\Models\Profile, $this, \App\Models\MeetingParticipant> $relation */
        $relation = $this->belongsToMany(Profile::class, 'meeting_profile', 'meeting_id', 'profile_id')
                    ->using(MeetingParticipant::class)
                    ->withPivot('attendance')
                    ->withTimestamps();
        
        return $relation;
    }

    /**
     * Atributos computados
     */
    public function getFormattedDateAttribute(): ?string
    {
        return $this->meeting_date?->format('d/m/Y H:i');
    }

    protected function getCalendarUrl(): string { return route('meetings.show', $this->id); }
    protected function getCalendarColor(): string { return '#f59e0b'; }

    /**
     * Scopes
     */
    /**
     * @param \Illuminate\Database\Eloquent\Builder<Meeting> $query
     * @return \Illuminate\Database\Eloquent\Builder<Meeting>
     */
    public function scopeUpcoming($query)
    {
        return $query->where('meeting_date', '>', now())
                     ->where('status', 'pendiente')
                     ->orderBy('meeting_date', 'asc');
    }

    /**
     * @param \Illuminate\Database\Eloquent\Builder<Meeting> $query
     * @return \Illuminate\Database\Eloquent\Builder<Meeting>
     */
    public function scopePast($query)
    {
        return $query->where('meeting_date', '<', now())
                     ->orderBy('meeting_date', 'desc');
    }

    /**
     * @param \Illuminate\Database\Eloquent\Builder<Meeting> $query
     * @return \Illuminate\Database\Eloquent\Builder<Meeting>
     */
    public function scopePending($query)
    {
        return $query->where('status', 'pendiente');
    }

    /**
     * @param \Illuminate\Database\Eloquent\Builder<Meeting> $query
     * @return \Illuminate\Database\Eloquent\Builder<Meeting>
     */
    public function scopeCompleted($query)
    {
        return $query->where('status', 'completada');
    }
}
