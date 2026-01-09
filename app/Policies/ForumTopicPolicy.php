<?php

namespace App\Policies;

use App\Models\ForumTopic;
use App\Models\User;
use Illuminate\Auth\Access\Response;

class ForumTopicPolicy
{
    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, ForumTopic $forumTopic): bool
    {
        return $user->hasRole('admin') || $user->profile->id === $forumTopic->profile_id;
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, ForumTopic $forumTopic): bool
    {
        return $user->hasRole('admin') || $user->profile->id === $forumTopic->profile_id;
    }
}
