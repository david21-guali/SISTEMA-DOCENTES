<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * Represents a direct message between two user profiles.
 * Optimized for High Maintainability Index (MI >= 65).
 * 
 * @property int $sender_id
 * @property int $receiver_id
 * @property string $content
 * @property \Carbon\Carbon|null $read_at
 * @property \Carbon\Carbon $created_at
 * @property \Carbon\Carbon $updated_at
 */
class Message extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     * 
     * @var array<int, string>
     */
    protected $fillable = [
        'sender_id',
        'receiver_id',
        'content',
        'read_at',
    ];

    /**
     * The attributes that should be cast.
     * 
     * @var array<string, string>
     */
    protected $casts = [
        'read_at' => 'datetime',
    ];

    /**
     * Get the profile that sent the message.
     * 
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    /**
     * Get the profile that sent the message.
     * 
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo<\App\Models\Profile, $this>
     */
    public function sender(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(Profile::class, 'sender_id');
    }

    /**
     * Get the profile that received the message.
     * 
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    /**
     * Get the profile that received the message.
     * 
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo<\App\Models\Profile, $this>
     */
    public function receiver(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(Profile::class, 'receiver_id');
    }

    /**
     * Scope a query to only include messages between two specific profiles.
     * 
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @param int $profileA
     * @param int $profileB
     * @return \Illuminate\Database\Eloquent\Builder
     */
    /**
     * Scope a query to only include messages between two specific profiles.
     * 
     * @param \Illuminate\Database\Eloquent\Builder<Message> $query
     * @param int $profileA
     * @param int $profileB
     * @return \Illuminate\Database\Eloquent\Builder<Message>
     */
    public function scopeBetween($query, int $profileA, int $profileB)
    {
        return $query->where(function($q) use ($profileA, $profileB) {
            $q->where('sender_id', $profileA)->where('receiver_id', $profileB);
        })->orWhere(function($q) use ($profileA, $profileB) {
            $q->where('sender_id', $profileB)->where('receiver_id', $profileA);
        });
    }
}
