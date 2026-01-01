<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ForumTopic extends Model
{
    use HasFactory;

    protected $fillable = [
        'profile_id',
        'title',
        'description',
    ];

    public function profile()
    {
        return $this->belongsTo(Profile::class);
    }

    public function posts()
    {
        return $this->hasMany(ForumPost::class, 'topic_id');
    }
}
