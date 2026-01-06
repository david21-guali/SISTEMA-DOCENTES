<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * Represents an individual response or comment within a forum topic.
 * Optimized for High Maintainability Index (MI >= 65).
 * 
 * @property int $topic_id
 * @property int $profile_id
 * @property string $content
 * @property \Carbon\Carbon $created_at
 * @property \Carbon\Carbon $updated_at
 */
class ForumPost extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     * 
     * @var array<int, string>
     */
    protected $fillable = [
        'topic_id',
        'profile_id',
        'user_id',
        'content',
    ];

    /**
     * Get the discussion topic this post belongs to.
     * 
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function topic(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(ForumTopic::class, 'topic_id');
    }

    /**
     * Get the profile that authored this post.
     * 
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function profile(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(Profile::class);
    }
}
