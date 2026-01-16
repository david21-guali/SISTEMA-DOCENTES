<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class InnovationReview extends Model
{
    /** @use \Illuminate\Database\Eloquent\Factories\HasFactory<\Database\Factories\InnovationReviewFactory> */
    use HasFactory;

    protected $fillable = [
        'innovation_id',
        'reviewer_id',
        'vote',
        'comment'
    ];

    /**
     * Get the innovation that this review belongs to.
     * 
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo<Innovation, $this>
     */
    public function innovation(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(Innovation::class);
    }

    /**
     * Get the user who performed the review.
     * Note: This is stored but hidden in views to maintain anonymity.
     * 
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo<User, $this>
     */
    public function reviewer(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(User::class, 'reviewer_id');
    }

    /**
     * Hide the reviewer_id by default to protect anonymity in JSON responses.
     */
    protected $hidden = ['reviewer_id'];

    /**
     * Accessor for the vote icon.
     */
    public function getVoteIconAttribute(): string
    {
        return $this->vote === 'approved' ? 'fas fa-check-circle text-success' : 'fas fa-times-circle text-danger';
    }
}
