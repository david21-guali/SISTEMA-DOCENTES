<?php

namespace App\Http\Controllers;

use App\Models\Attachment;
use App\Models\Project;
use App\Models\Task;
use App\Notifications\FileUploaded;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class AttachmentController extends Controller
{
    /**
     * Store a new attachment.
     */
    /**
     * Store a new attachment.
     * 
     * @param Request $request
     * @param string $type
     * @param int|string $id
     * @return \Illuminate\Http\JsonResponse|\Illuminate\Http\RedirectResponse
     */
    public function store(Request $request, $type, $id): \Illuminate\Http\JsonResponse|\Illuminate\Http\RedirectResponse
    {
        $request->validate([
            'files' => 'required',
            'files.*' => 'file|max:10240', // Max 10MB per file
        ]);

        // Get the attachable model
        $attachable = match($type) {
            'project' => Project::findOrFail($id),
            'task' => Task::findOrFail($id),
            default => abort(404, 'Tipo no válido'),
        };

        $uploaded = [];

        foreach ($request->file('files') as $file) {
            // Store file
            $path = $file->store('attachments/' . $type . 's/' . $id, 'public');
            
            // Create attachment record
            $attachment = $attachable->attachments()->create([
                'filename' => basename((string)$path),
                'original_name' => $file->getClientOriginalName(),
                'mime_type' => $file->getMimeType(),
                'size' => $file->getSize(),
                'path' => $path,
                'uploaded_by' => Auth::user()->profile->id,
            ]);

            $uploaded[] = $attachment;
        }

        // Notify relevant users
        $this->notifyUpload($attachable, $type, $uploaded);

        if ($request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => count($uploaded) . ' archivo(s) subido(s) correctamente.',
                'attachments' => $uploaded,
            ]);
        }

        return back()->with('success', count($uploaded) . ' archivo(s) subido(s) correctamente.');
    }

    /**
     * Download an attachment.
     */
    public function download(Attachment $attachment): \Symfony\Component\HttpFoundation\BinaryFileResponse
    {
        $path = Storage::disk('public')->path($attachment->path);
        
        if (!file_exists($path)) {
            abort(404, 'Archivo no encontrado.');
        }

        return response()->download($path, $attachment->original_name);
    }

    /**
     * Delete an attachment.
     */
    public function destroy(int $id): \Illuminate\Http\JsonResponse|\Illuminate\Http\RedirectResponse
    {
        /** @var \App\Models\Attachment $attachment */
        $attachment = Attachment::find($id);

        if (!$attachment) {
            if (request()->ajax() || request()->wantsJson()) {
                return response()->json(['error' => 'El archivo ya no existe o no se encuentra.'], 404);
            }
            return back()->with('error', 'El archivo no fue encontrado.');
        }

        // Check permission (only uploader or admin can delete)
        $currentUser = Auth::user();
        $canDelete = false;

        // Verificar si es admin
        if ($currentUser && $currentUser->hasRole('admin')) {
            $canDelete = true;
        }
        
        // Verificar si es el que subió el archivo
        if ($currentUser && $currentUser->profile && $attachment->uploaded_by === $currentUser->profile->id) {
            $canDelete = true;
        }

        if (!$canDelete) {
            if (request()->ajax() || request()->wantsJson()) {
                return response()->json(['error' => 'No tienes permiso para eliminar este archivo.'], 403);
            }
            return back()->with('error', 'No tienes permiso para eliminar este archivo.');
        }

        $attachment->delete();

        if (request()->ajax() || request()->wantsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Archivo eliminado correctamente.',
            ]);
        }

        return back()->with('success', 'Archivo eliminado correctamente.');
    }
    /**
     * Preview an attachment.
     */
    public function preview(string $path): \Symfony\Component\HttpFoundation\BinaryFileResponse
    {
        // Basic security check to ensure it's in a known directory
        if (!str_starts_with($path, 'attachments/') && !str_starts_with($path, 'temp/')) {
            abort(403, 'Acceso no permitido');
        }

        if (!\Illuminate\Support\Facades\Storage::disk('public')->exists($path)) {
            abort(404, 'Archivo no encontrado');
        }

        $fullPath = Storage::disk('public')->path($path);
        
        if (!file_exists($fullPath)) {
            abort(404, 'Archivo no encontrado.');
        }

        return response()->file($fullPath);
    }

    /**
     * Notify relevant users about the upload.
     *
     * @param \App\Models\Project|\App\Models\Task $attachable
     * @param string $type
     * @param array<int, \App\Models\Attachment> $attachments
     */
    private function notifyUpload(object $attachable, string $type, array $attachments): void
    {
        if (empty($attachments)) return;

        $modelName = $type === 'project' ? 'Proyecto' : 'Tarea';
        $notification = new FileUploaded($attachments[0], $modelName, count($attachments), Auth::user()->name);
        
        $usersToNotify = collect();

        if ($attachable instanceof Project && $type === 'project') {
            // Notify Project Creator/Responsible
            if ($attachable->profile && $attachable->profile->user) {
                $usersToNotify->push($attachable->profile->user);
            }
            // Notify Team? Maybe better just the responsible one for now to avoid spam.
            // But user said "se deberia modificar al ccreador cuando el designado de un proyecto o tarea sube un archivo de entrega"
            // So definitely the creator.
        } elseif ($attachable instanceof Task) {
            // It's a task. 
            // Notify Project Creator (who usually assigns tasks)
            if ($attachable->project && $attachable->project->profile && $attachable->project->profile->user) {
                $usersToNotify->push($attachable->project->profile->user);
            }
            // Also notify assignees? If someone else in the group sube algo.
            $assignees = $attachable->assignees->pluck('user');
            $usersToNotify = $usersToNotify->concat($assignees);
        }

        $usersToNotify = $usersToNotify->unique('id')->filter(function($u) {
            return $u->id !== Auth::id(); // Don't notify self
        });

        foreach ($usersToNotify as $user) {
            $user->notify($notification);
        }
    }
}
