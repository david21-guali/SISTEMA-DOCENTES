<?php

namespace App\Services;

use App\Models\Attachment;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Notification;
use App\Notifications\FileUploaded;
use Illuminate\Support\Collection;

/**
 * Service to handle notifications for new attachments.
 */
class AttachmentNotificationService
{
    /**
     * Notify relevant users about new uploads.
     * 
     * @param Model $attachable
     * @param array<int, Attachment> $uploaded
     * @return void
     */
    public function notifyUpload(Model $attachable, array $uploaded): void
    {
        if (empty($uploaded)) return;

        $attachment = $uploaded[0];
        $count = count($uploaded);
        
        $modelName = $this->getFriendlyModelName($attachable);
        $uploaderName = Auth::user()->name;

        $usersToNotify = $this->getRecipients($attachable);

        $notification = new FileUploaded($attachment, $modelName, $count, $uploaderName);
        
        Notification::send(
            $usersToNotify->filter()->unique('id')->reject(fn($u) => $u->id === Auth::id()),
            $notification
        );
    }

    /**
     * Determine the friendly name of the model.
     * 
     * @param Model $attachable
     * @return string
     */
    private function getFriendlyModelName(Model $attachable): string
    {
        return class_basename($attachable) === 'Project' ? 'Proyecto' : 'Tarea';
    }

    /**
     * @return \Illuminate\Support\Collection<int, \App\Models\User>
     */
    private function getRecipients(Model $attachable): Collection
    {
        return match (true) {
            $attachable instanceof \App\Models\Project => $this->getProjectRecipients($attachable),
            $attachable instanceof \App\Models\Task => $this->getTaskRecipients($attachable),
            default => collect(),
        };
    }

    /**
     * @return \Illuminate\Support\Collection<int, \App\Models\User>
     */
    private function getProjectRecipients(\App\Models\Project $project): Collection
    {
        // PHPStan treats relations as non-nullable based on strict model docs
        return collect([$project->profile->user ?? null])->filter();
    }

    /**
     * @return \Illuminate\Support\Collection<int, \App\Models\User>
     */
    private function getTaskRecipients(\App\Models\Task $task): Collection
    {
        $leader = $task->project->profile->user ?? null;
        
        $assignees = $task->assignees->map(fn($p) => $p->user);

        return collect([$leader])->concat($assignees)->filter();
    }
}
