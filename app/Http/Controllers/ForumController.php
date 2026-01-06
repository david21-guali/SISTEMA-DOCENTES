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

        ForumTopic::create([
            'profile_id' => Auth::user()->profile->id,
            'user_id' => Auth::id(),
            'title' => $request->title,
            'description' => $request->description,
        ]);

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

    public function storePost(Request $request, $forum): \Illuminate\Http\RedirectResponse
    {
        $request->validate(['content' => 'required']);

        // Handle both bound model or raw ID (fallback)
        $topicId = ($forum instanceof ForumTopic) ? $forum->id : $forum;

        ForumPost::create([
            'topic_id' => $topicId,
            'profile_id' => Auth::user()->profile->id,
            'user_id' => Auth::id(),
            'content' => $request->content,
        ]);

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
