<?php

namespace App\Observers;

use App\Models\Attachment;
use App\Models\Project;
use App\Models\Task;
use App\Notifications\FileUploaded;
use Illuminate\Support\Facades\Auth;

/**
 * Observer for Attachment model to handle automatic notifications.
 */
class AttachmentObserver
{
    /**
     * Notify relevant users when a file is uploaded.
     * 
     * @param Attachment $attachment
     * @return void
     */
    public function created(Attachment $attachment): void
    {
        $attachable = $attachment->attachable;
        if (!$attachable) return;

        $uploader = auth()->user()->name ?? 'Sistema';
        $label = ($attachable instanceof Project) ? 'Proyecto' : 'Tarea';
        
        $users = match(true) {
            $attachable instanceof Task => $attachable->assignees->pluck('user')->push($attachable->project->profile->user ?? null),
            $attachable instanceof Project => $attachable->team->pluck('user'),
            default => collect()
        };

        $users->push($attachable->profile->user ?? null)
            ->unique('id')
            ->filter(fn($x) => $x && $x->id !== auth()->id())
            ->each->notify(new FileUploaded($attachment, $label, 1, $uploader));
    }
}
