<?php

namespace App\Http\Controllers;

use App\Models\Comment;
use App\Models\Project;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CommentController extends Controller
{
    /**
     * Store a newly created comment in storage.
     */
    public function store(Request $request, Project $project)
    {
        $validated = $request->validate([
            'content' => 'required|string|max:1000',
            'parent_id' => 'nullable|exists:comments,id',
        ]);

        $validated['project_id'] = $project->id;
        $validated['user_id'] = null; // Removed
        $validated['profile_id'] = Auth::user()->profile->id;

        $comment = Comment::create($validated);

        // Notify project owner if someone else comments
        // Project owner is via profile now
        if ($project->profile->user_id !== Auth::id()) {
            $user = $project->profile->user;
            $user->notify(new \App\Notifications\NewCommentAdded($comment));
        }

        return back()->with('success', 'Comentario publicado exitosamente.');
    }

    /**
     * Remove the specified comment from storage.
     */
    public function destroy(Comment $comment)
    {
        // Solo el autor o admin puede eliminar
        // Author is profile. Check if auth user profile id matches comment profile id
        if (Auth::user()->profile->id !== $comment->profile_id && !Auth::user()->hasRole('admin')) {
            return back()->with('error', 'No tienes permiso para eliminar este comentario.');
        }

        $comment->delete();

        return back()->with('success', 'Comentario eliminado exitosamente.');
    }
}
