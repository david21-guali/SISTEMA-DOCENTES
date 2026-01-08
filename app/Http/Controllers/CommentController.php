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
    /**
     * Store a newly created comment in storage.
     */
    /**
     * Store a newly created comment in storage.
     */
    public function store(Request $request): \Illuminate\Http\RedirectResponse
    {
        $validated = $request->validate([
            'content' => 'required|string|max:1000',
            'parent_id' => 'nullable|exists:comments,id',
            'commentable_type' => 'required|string|in:project,innovation',
            'commentable_id' => 'required|integer',
        ]);

        $modelClass = $validated['commentable_type'] === 'project' ? Project::class : Innovation::class;
        /** @var Innovation|Project $commentable */
        $commentable = $modelClass::findOrFail($validated['commentable_id']);

        $comment = new Comment();
        $comment->content = $validated['content'];
        $comment->parent_id = $validated['parent_id'] ?? null;
        /** @var int<0, max> $profileId */
        $profileId = (int) Auth::user()->profile->id;
        $comment->profile_id = $profileId;
        $comment->commentable_type = $modelClass;
        /** @var int<0, max> $commentableId */
        $commentableId = (int) $commentable->id;
        $comment->commentable_id = $commentableId;
        $comment->save();

        // Notify commentable owner if someone else comments
        /** @var Profile $profile */
        $profile = $commentable->profile;
        $owner = $profile->user;
        if ($owner->id !== Auth::id()) {
            $owner->notify(new \App\Notifications\NewCommentAdded($comment));
        }

        return back()->with('success', 'Comentario publicado exitosamente.');
    }


    /**
     * Remove the specified comment from storage.
     */
    public function destroy(Comment $comment): \Illuminate\Http\RedirectResponse
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
