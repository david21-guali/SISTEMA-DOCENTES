<?php

namespace App\Http\Controllers;

use App\Models\Comment;
use App\Models\Project;
use App\Models\Innovation;
use App\Models\Profile;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CommentController extends Controller
{
    protected \App\Services\CommentService $commentService;

    public function __construct(\App\Services\CommentService $commentService)
    {
        $this->commentService = $commentService;
    }

    /**
     * Store a newly created comment in storage.
     */
    public function store(Request $request): \Illuminate\Http\RedirectResponse
    {
        $validated = $request->validate([
            'content'          => 'required|string|max:1000',
            'parent_id'        => 'nullable|exists:comments,id',
            'commentable_type' => 'required|string|in:project,innovation',
            'commentable_id'   => 'required|integer',
        ]);

        $this->commentService->createComment($validated);

        return back()->with('success', 'Comentario publicado exitosamente.');
    }

    /**
     * Remove the specified comment from storage.
     */
    public function destroy(Comment $comment): \Illuminate\Http\RedirectResponse
    {
        if (Auth::user()->profile->id !== $comment->profile_id && !Auth::user()->hasRole('admin')) {
            return back()->with('error', 'No tienes permiso para eliminar este comentario.');
        }

        $comment->delete();

        return back()->with('success', 'Comentario eliminado exitosamente.');
    }
}
