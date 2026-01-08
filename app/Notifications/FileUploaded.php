<?php

namespace App\Notifications;

use App\Models\Attachment;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class FileUploaded extends Notification
{
    use Queueable;

    /** @var \App\Models\Attachment */
    public Attachment $attachment;
    /** @var string */
    public string $modelName; // 'Proyecto' or 'Tarea'
    /** @var int */
    public int $count;
    /** @var string */
    public string $uploaderName;

    public function __construct(Attachment $attachment, string $modelName, int $count = 1, string $uploaderName = 'Alguien')
    {
        $this->attachment = $attachment;
        $this->modelName = $modelName;
        $this->count = $count;
        $this->uploaderName = $uploaderName;
    }

    /**
     * @param \App\Models\User $notifiable
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        return ['database'];
    }

    /**
     * @param object $notifiable
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        /** @var \App\Models\Project|\App\Models\Task $attachable */
        $attachable = $this->attachment->attachable;
        $title = $attachable->title ?? $attachable->name ?? 'Elemento';
        
        $filesText = $this->count > 1 ? "{$this->count} archivos" : "'{$this->attachment->original_name}'";
        $message = "{$this->uploaderName} ha subido {$filesText} en el {$this->modelName}: {$title}";

        return [
            'attachment_id' => $this->attachment->id,
            'title' => $this->count > 1 ? 'Nuevos archivos subidos' : 'Nuevo archivo subido',
            'message' => $message,
            'uploader' => $this->uploaderName,
            'link' => $this->modelName === 'Proyecto' 
                      ? route('projects.show', $attachable->id) 
                      : route('tasks.show', $attachable->id),
        ];
    }
}
