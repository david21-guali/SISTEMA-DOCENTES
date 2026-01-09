<?php

namespace App\Services;

use App\Models\Attachment;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

use Illuminate\Support\Facades\Notification;
use App\Notifications\FileUploaded;

class AttachmentService
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
        $modelName = class_basename($attachable) === 'Project' ? 'Proyecto' : 'Tarea';
        $uploaderName = Auth::user()->name;

        $usersToNotify = collect();

        if ($attachable instanceof \App\Models\Project) {
            $usersToNotify->push($attachable->profile->user);
        } elseif ($attachable instanceof \App\Models\Task) {
            $usersToNotify->push($attachable->project->profile->user);
            $attachable->assignees->each(fn($p) => $usersToNotify->push($p->user));
        }

        $notification = new FileUploaded($attachment, $modelName, $count, $uploaderName);
        
        Notification::send(
            $usersToNotify->filter()->unique('id')->reject(fn($u) => $u->id === Auth::id()),
            $notification
        );
    }
    /**
     * Handle multiple file uploads (direct).
     *
     * @param Model $attachable
     * @param array<int, UploadedFile> $files
     * @return array<int, Attachment>
     */
    public function handleUploads(Model $attachable, array $files): array
    {
        $uploaded = [];
        foreach ($files as $file) {
            $uploaded[] = $this->storeSingleFile($attachable, $file);
        }
        return $uploaded;
    }

    /**
     * Handle temporary files uploaded via AJAX.
     * 
     * @param Model $attachable
     * @param array<int, string> $tempPaths
     * @return array<int, Attachment>
     */
    public function handleTemporaryFiles(Model $attachable, array $tempPaths): array
    {
        return array_filter(array_map(fn($v) => $this->processTempFile($attachable, $v), $tempPaths));
    }

    /**
     * Process a single temporary file into a permanent attachment.
     */
    private function processTempFile(Model $attachable, string $value): ?Attachment
    {
        $decoded = json_decode($value, true);
        $path = $decoded['path'] ?? $value;

        if (!Storage::disk('public')->exists($path)) return null;

        $slug = strtolower(class_basename($attachable)) . "s";
        $dir = "attachments/{$slug}/{$attachable->getKey()}";
        $newPath = $dir . '/' . basename($path);
        
        Storage::disk('public')->makeDirectory($dir);
        Storage::disk('public')->copy($path, $newPath);
        Storage::disk('public')->delete($path);
        
        /** @var \App\Models\Task|\App\Models\Project|\App\Models\Innovation|\App\Models\Profile $attachable */
        return $attachable->attachments()->create([
            'filename'      => basename($newPath),
            'original_name' => $decoded['name'] ?? basename($value),
            'mime_type'     => Storage::disk('public')->mimeType($newPath),
            'size'          => Storage::disk('public')->size($newPath),
            'path'          => $newPath,
            'uploaded_by'   => Auth::user()->profile->id,
        ]);
    }

    /**
     * Store a single file.
     */
    public function storeSingleFile(Model $attachable, UploadedFile $file): Attachment
    {
        $slug = strtolower(class_basename($attachable)) . "s";
        $dir = "attachments/{$slug}/{$attachable->getKey()}";
        $path = $file->store($dir, 'public');
         
        /** @var \App\Models\Task|\App\Models\Project|\App\Models\Innovation|\App\Models\Profile $attachable */
        return $attachable->attachments()->create([
            'filename'      => basename((string)$path),
            'original_name' => $file->getClientOriginalName(),
            'mime_type'     => $file->getMimeType(),
            'size'          => $file->getSize(),
            'path'          => (string)$path,
            'uploaded_by'   => Auth::user()->profile->id,
        ]);
    }

    /**
     * Delete an attachment from storage and DB.
     */
    public function delete(Attachment $attachment): bool
    {
        Storage::disk('public')->delete($attachment->path);
        return $attachment->delete();
    }

    /**
     * Check if a user can manage an attachment.
     */
    public function canManage(Attachment $attachment, ?\App\Models\User $user): bool
    {
        $id = $user->profile->id ?? null;
        return $user && ($user->hasRole('admin') || $attachment->uploaded_by === $id);
    }
}
