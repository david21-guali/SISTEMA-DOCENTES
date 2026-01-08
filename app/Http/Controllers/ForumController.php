<?php

namespace App\Http\Controllers;

use App\Models\ForumTopic;
use App\Models\ForumPost;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ForumController extends Controller
{
    public function index(): \Illuminate\View\View
    {
        $topics = ForumTopic::with('profile.user')->withCount('posts')->latest()->paginate(10);
        return view('forum.index', compact('topics'));
    }

    public function create(): \Illuminate\View\View
    {
        return view('forum.create');
    }

    public function store(Request $request): \Illuminate\Http\RedirectResponse
    {
        $request->validate(['title' => 'required', 'description' => 'required']);

        $topic = ForumTopic::create([
            'profile_id' => Auth::user()->profile->id,
            'title' => $request->title,
            'description' => $request->description,
        ]);

        // Notify Community (Admins, Coordinators, Teachers) about new topic
        $recipients = \App\Models\User::role(['admin', 'coordinador', 'docente'])->get();
        
        foreach ($recipients as $recipient) {
            if ($recipient->id !== Auth::id()) {
                $recipient->notify(new \App\Notifications\NewForumTopic($topic));
            }
        }

        return redirect()->route('forum.index')->with('success', 'Tema creado.');
    }

    public function show(ForumTopic $forum): \Illuminate\View\View
    {
        // Renamed variable to match route param
        $posts = $forum->posts()->with('profile.user')->oldest()->get();
        // Passing content as 'topic' to view to avoid breaking view variable usage
        $topic = $forum; 
        return view('forum.show', compact('topic', 'posts'));
    }

    public function storePost(Request $request, ForumTopic|string|int $forum): \Illuminate\Http\RedirectResponse
    {
        $request->validate(['content' => 'required']);

        // Handle both bound model or raw ID (fallback)
        $topicId = ($forum instanceof ForumTopic) ? $forum->id : $forum;
        // Fetch topic with relations for notification logic
        $topic = ForumTopic::with(['profile.user', 'posts.profile.user'])->find($topicId);

        $post = ForumPost::create([
            'topic_id' => $topicId,
            'profile_id' => Auth::user()->profile->id,
            'content' => $request->content,
        ]);

        if ($topic) {
            // Collect all unique users to notify (Thread Participants)
            $usersToNotify = collect();

            // 1. Add Topic Creator
            if ($topic->profile && $topic->profile->user) {
                $usersToNotify->push($topic->profile->user);
            }

            // 2. Add Authors of existing reply posts (Siblings in discussion)
            foreach ($topic->posts as $existingPost) {
                if ($existingPost->profile && $existingPost->profile->user) {
                    $usersToNotify->push($existingPost->profile->user);
                }
            }

            // 3. Filter: Unique IDs and Exclude Current Replier
            $usersToNotify = $usersToNotify->unique('id')->reject(function ($user) {
                return $user->id === Auth::id();
            });

            // 4. Send Notifications
            foreach ($usersToNotify as $user) {
                $user->notify(new \App\Notifications\NewForumReply($post));
            }
        }

        return back()->with('success', 'Respuesta publicada.');
    }

    public function destroy(ForumTopic $forum): \Illuminate\Http\RedirectResponse
    {
        // Solo el dueño o admin puede borrar
        if (Auth::user()->profile->id !== $forum->profile_id && !Auth::user()->hasRole('admin')) {
            abort(403);
        }
        $forum->delete();
        return redirect()->route('forum.index')->with('success', 'Tema eliminado.');
    }
}
