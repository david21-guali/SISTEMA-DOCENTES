<?php

namespace App\Http\Controllers;

use App\Models\ForumTopic;
use App\Models\ForumPost;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ForumController extends Controller
{
    /**
     * Create a new controller instance.
     * 
     * @param \App\Services\ForumService $fs
     */
    public function __construct(protected \App\Services\ForumService $fs) {}

    /**
     * Display a paginated list of forum topics.
     * 
     * @return \Illuminate\View\View
     */
    public function index(): \Illuminate\View\View 
    { 
        $topics = ForumTopic::with('profile.user')->withCount('posts')->latest()->paginate(10);
        return view('forum.index', compact('topics')); 
    }

    /**
     * Show the form for creating a new topic.
     * 
     * @return \Illuminate\View\View
     */
    public function create(): \Illuminate\View\View 
    { 
        return view('forum.create'); 
    }

    public function store(\App\Http\Requests\StoreForumTopicRequest $request): \Illuminate\Http\RedirectResponse 
    { 
        /** @var array{title: string, description: string, category_id: int} $validated */
        $validated = $request->validated();
        $this->fs->createTopic($validated); 
        return redirect()->route('forum.index')->with('success', 'Tema creado.'); 
    }

    /**
     * Display the specified forum topic and its posts.
     * 
     * @param ForumTopic $forum
     * @return \Illuminate\View\View
     */
    public function show(ForumTopic $forum): \Illuminate\View\View 
    { 
        $posts = $forum->posts()->with('profile.user')->oldest()->get();
        return view('forum.show', ['topic' => $forum, 'posts' => $posts]); 
    }

    /**
     * Store a newly created post in storage.
     * 
     * @param \App\Http\Requests\StoreForumPostRequest $request
     * @param ForumTopic $topic
     * @return \Illuminate\Http\RedirectResponse
     */
    public function storePost(\App\Http\Requests\StoreForumPostRequest $request, ForumTopic $topic): \Illuminate\Http\RedirectResponse 
    { 
        $this->fs->createPost($topic, $request->validated()['content']); 
        return back()->with('success', 'Respuesta publicada.'); 
    }

    /**
     * Remove the specified topic from storage.
     * 
     * @param ForumTopic $forum
     * @return \Illuminate\Http\RedirectResponse
     */
    public function destroy(ForumTopic $forum): \Illuminate\Http\RedirectResponse 
    { 
        if (!auth()->user()->can('delete', $forum)) {
            return redirect()->route('forum.index')->with('error', 'No autorizado.');
        }
        $forum->delete(); 
        return redirect()->route('forum.index')->with('success', 'Tema eliminado.'); 
    }
}
