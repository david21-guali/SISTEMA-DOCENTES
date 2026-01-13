<?php

namespace App\Notifications;

use App\Models\Attachment;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class FileUploaded extends Notification
{
    use \App\Traits\HasNotificationPreferences;

    public string $category = 'resources';

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


    /**
     * @param object $notifiable
     * @return MailMessage
     */
    public function toMail(object $notifiable): MailMessage
    {
        $meta = $this->getNotificationMeta();
        return (new MailMessage)
            ->subject("{$meta['subject']}: {$meta['title']}")
            ->line("{$this->uploaderName} ha subido {$meta['filesText']} en el {$this->modelName}: {$meta['title']}")
            ->action('Ver elemento', route($meta['route'], $meta['id']))
            ->line('Gracias por tu colaboración.');
    }

    /**
     * @param object $notifiable
     * @return array{attachment_id: int, title: string, message: string, uploader: string, link: string}
     */
    public function toArray(object $notifiable): array
    {
        $meta = $this->getNotificationMeta();
        return [
            'attachment_id' => $this->attachment->id,
            'title' => $this->count > 1 ? 'Nuevos archivos subidos' : 'Nuevo archivo subido',
            'message' => "{$this->uploaderName} ha subido {$meta['filesText']} en el {$this->modelName}: {$meta['title']}",
            'uploader' => $this->uploaderName,
            'link' => route($meta['route'], $meta['id']),
        ];
    }

    /**
     * @return array{id: int, title: string, filesText: string, subject: string, route: string}
     */
    private function getNotificationMeta(): array
    {
        $m = $this->attachment->attachable;
        
        $title = 'Elemento';
        if ($m instanceof \App\Models\Project || $m instanceof \App\Models\Task) {
            $title = $m->title ?: 'Elemento';
        }

        return [
            'id' => (int) ($m->id ?? 0),
            'title' => $title,
            'filesText' => $this->count > 1 ? "{$this->count} archivos" : "'{$this->attachment->original_name}'",
            'subject' => ($this->count > 1 ? "Nuevos archivos" : "Nuevo archivo") . " en {$this->modelName}",
            'route' => $this->modelName === 'Proyecto' ? 'projects.show' : 'tasks.show'
        ];
    }
}
