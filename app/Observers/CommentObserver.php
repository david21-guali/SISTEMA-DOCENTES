<?php

namespace App\Observers;

use App\Models\Comment;
use App\Models\User;
use App\Notifications\NewCommentAdded;

class CommentObserver
{
    public function created(Comment $comment): void
    {
        $authorId = $comment->profile_id;
        $commentable = $comment->commentable;
        if (!$commentable instanceof \App\Models\Project && !$commentable instanceof \App\Models\Innovation) {
            return;
        }

        // Notificar al dueño si no es el autor
        if ($commentable->profile->user && $commentable->profile_id !== $authorId) {
            $commentable->profile->user->notify(new NewCommentAdded($comment));
        }

        // Notificar a otros participantes
        /** @var \App\Models\Project|\App\Models\Innovation $commentable */
        $participants = $commentable->comments()->where('profile_id', '!=', $authorId)->pluck('profile_id')->unique();
        
        User::whereHas('profile', fn($q) => $q->whereIn('id', $participants))
            ->get()->each->notify(new NewCommentAdded($comment));
    }
}
