<?php

namespace App\Services;

use App\Models\ForumTopic;
use App\Models\ForumPost;
use App\Models\User;
use App\Notifications\NewForumTopic;
use App\Notifications\NewForumReply;
use Illuminate\Support\Facades\Auth;

class ForumService
{
    /**
     * Create a new forum topic.
     * 
     * @param array{title: string, description: string, category_id: int} $data
     * @return \App\Models\ForumTopic
     */
    public function createTopic(array $data): \App\Models\ForumTopic
    {
        $topic = ForumTopic::create([
            'profile_id'  => Auth::user()->profile->id,
            'title'       => $data['title'],
            'description' => $data['description'],
        ]);

        $this->notifyNewTopic($topic);

        return $topic;
    }

    /**
     * Create a new forum post/reply and notify thread participants.
     */
    public function createPost(ForumTopic|int $topic, string $content): ForumPost
    {
        $topic = $topic instanceof ForumTopic ? $topic : ForumTopic::findOrFail($topic);

        $post = ForumPost::create([
            'topic_id'   => $topic->id,
            'profile_id' => Auth::user()->profile->id,
            'content'    => $content,
        ]);

        $this->notifyThreadParticipants($topic, $post);

        return $post;
    }

    /**
     * Notify community about a new topic.
     */
    private function notifyNewTopic(ForumTopic $topic): void
    {
        $users = User::role(['admin', 'coordinador', 'docente'])
            ->where('id', '!=', Auth::id())
            ->whereHas('profile', fn($q) => $q->where('profiles.is_active', true))
            ->get();

        foreach ($users as $user) {
            $user->notify(new NewForumTopic($topic));
        }
    }

    /**
     * Notify all participants in a thread about a new reply.
     */
    private function notifyThreadParticipants(ForumTopic $topic, ForumPost $post): void
    {
        $usersToNotify = collect();

        // Topic Creator
        if ($topic->profile && $topic->profile->user) {
            $usersToNotify->push($topic->profile->user);
        }

        // Reply Authors
        foreach ($topic->posts()->with('profile.user')->get() as $existingPost) {
            if ($existingPost->profile && $existingPost->profile->user) {
                $usersToNotify->push($existingPost->profile->user);
            }
        }

        $usersToNotify->unique('id')
            ->reject(fn($u) => $u->id === Auth::id())
            ->each(fn($u) => $u->notify(new NewForumReply($post)));
    }
}
