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
    use \App\Traits\HandlesAttachmentLogic;

    protected \App\Services\AttachmentService $attachmentService;

    public function __construct(\App\Services\AttachmentService $attachmentService)
    {
        $this->attachmentService = $attachmentService;
    }

    /**
     * Store a new attachment.
     */
    public function store(Request $request, string $type, int $id): \Illuminate\Http\JsonResponse|\Illuminate\Http\RedirectResponse
    {
        $request->validate([
            'files'   => 'required',
            'files.*' => 'file|max:10240',
        ]);

        $attachable = $this->getAttachable($type, $id);
        $uploaded = $this->attachmentService->handleUploads($attachable, $request->file('files'));

        $this->attachmentService->notifyUpload($attachable, $uploaded);

        return $this->formatResponse(
            count($uploaded) . ' archivo(s) subido(s) correctamente.',
            ['attachments' => $uploaded]
        );
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
        $file = Attachment::findOrFail($id);
        if (!$this->attachmentService->canManage($file, Auth::user())) {
            return $this->formatResponse('No autorizado', [], 403);
        }
        return $this->formatResponse($this->attachmentService->delete($file) ? 'Archivo eliminado' : 'Error');
    }

    /**
     * Preview an attachment.
     */
    public function preview(string $path): \Symfony\Component\HttpFoundation\BinaryFileResponse
    {
        $this->validatePath($path);
        
        $fullPath = Storage::disk('public')->path($path);
        
        if (!Storage::disk('public')->exists($path) || !file_exists($fullPath)) {
            abort(404, 'Archivo no encontrado');
        }

        return response()->file($fullPath);
    }

    /**
     * Ensure path is valid for preview.
     */
    private function validatePath(string $path): void
    {
        if (!str_starts_with($path, 'attachments/') && !str_starts_with($path, 'temp/')) {
            abort(403, 'Acceso no permitido');
        }
    }

}
