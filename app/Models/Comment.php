<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Comment extends Model
{
    /** @use HasFactory<\Database\Factories\CommentFactory> */
    use HasFactory;

    protected $fillable = [
        'project_id',
        'profile_id',
        'content',
        'parent_id',
    ];

    /**
     * Relaciones
     */
    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo<\App\Models\Project, $this>
     */
    public function project(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(Project::class);
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo<\App\Models\Profile, $this>
     */
    public function profile(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(Profile::class);
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo<\App\Models\Comment, $this>
     */
    public function parent(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(Comment::class, 'parent_id');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany<\App\Models\Comment, $this>
     */
    public function replies(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(Comment::class, 'parent_id');
    }

    /**
     * Scopes
     */
    /**
     * @param \Illuminate\Database\Eloquent\Builder<Comment> $query
     * @return \Illuminate\Database\Eloquent\Builder<Comment>
     */
    public function scopeTopLevel($query)
    {
        return $query->whereNull('parent_id');
    }
}
