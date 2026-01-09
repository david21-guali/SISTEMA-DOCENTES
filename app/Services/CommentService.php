<?php

namespace App\Services;

use App\Models\Comment;
use App\Models\Project;
use App\Models\Innovation;
use App\Models\User;
use App\Notifications\NewCommentAdded;
use App\Notifications\CommentReplied;
use Illuminate\Support\Facades\Auth;

class CommentService
{
    /**
     * Create a new comment and notify owner and thread participants.
     * 
     * @param array{content: string, parent_id?: int, commentable_type: string, commentable_id: int} $data
     * @return Comment
     */
    public function createComment(array $data): Comment
    {
        $modelClass = $data['commentable_type'] === 'project' ? Project::class : Innovation::class;
        $commentable = $modelClass::findOrFail($data['commentable_id']);

        $comment = Comment::create([
            'content'          => $data['content'],
            'parent_id'        => $data['parent_id'] ?? null,
            'profile_id'       => Auth::user()->profile->id,
            'commentable_type' => $modelClass,
            'commentable_id'   => $commentable->id,
        ]);

        $this->notifyOwner($commentable, $comment);
        
        if ($comment->parent_id) {
            $this->notifyThreadParticipants($comment);
        }

        return $comment;
    }

    /**
     * Notify the owner of the commentable item.
     * 
     * @param Project|Innovation $commentable
     * @param Comment $comment
     */
    private function notifyOwner(object $commentable, Comment $comment): void
    {
        $ownerProfile = $commentable->profile;
        if ($ownerProfile && $ownerProfile->user && $ownerProfile->user->id !== Auth::id()) {
            $ownerProfile->user->notify(new NewCommentAdded($comment));
        }
    }

    /**
     * Notify all participants in the thread when a reply is posted.
     */
    private function notifyThreadParticipants(Comment $comment): void
    {
        $parent = Comment::with(['profile.user', 'replies.profile.user'])->find($comment->parent_id);
        if (!$parent) return;

        collect([$parent])->concat($parent->replies)
            ->pluck('profile.user')
            ->filter()
            ->unique('id')
            ->reject(fn($u) => $u->id === Auth::id())
            ->each->notify(new CommentReplied($comment));
    }
}
