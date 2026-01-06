<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * Represents a specific discussion thread within the community forum.
 * Optimized for High Maintainability Index (MI >= 65).
 * 
 * @property int $profile_id
 * @property string $title
 * @property string $description
 * @property \Carbon\Carbon $created_at
 * @property \Carbon\Carbon $updated_at
 */
class ForumTopic extends Model
{
    /** @use HasFactory<\Database\Factories\ForumTopicFactory> */
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     * 
     * @var list<string>
     */
    protected $fillable = [
        'profile_id',
        'user_id',
        'title',
        'description',
    ];

    /**
     * Get the profile that authored the topic.
     * 
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    /**
     * Get the profile that authored the topic.
     * 
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo<\App\Models\Profile, $this>
     */
    public function profile(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(Profile::class);
    }

    /**
     * Get all posts associated with this discussion topic.
     * 
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    /**
     * Get all posts associated with this discussion topic.
     * 
     * @return \Illuminate\Database\Eloquent\Relations\HasMany<\App\Models\ForumPost, $this>
     */
    public function posts(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(ForumPost::class, 'topic_id');
    }
}
