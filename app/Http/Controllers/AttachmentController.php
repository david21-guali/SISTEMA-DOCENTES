<?php

namespace App\Http\Controllers;

use App\Models\Attachment;
use App\Models\Project;
use App\Models\Task;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class AttachmentController extends Controller
{
    /**
     * Store a new attachment.
     */
    public function store(Request $request, $type, $id)
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
                'filename' => basename($path),
                'original_name' => $file->getClientOriginalName(),
                'mime_type' => $file->getMimeType(),
                'size' => $file->getSize(),
                'path' => $path,
                'uploaded_by' => Auth::user()->profile->id,
            ]);

            $uploaded[] = $attachment;
        }

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
    public function download(Attachment $attachment)
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
    public function destroy(Attachment $attachment)
    {
        // Check permission (only uploader or admin can delete)
        if ($attachment->uploaded_by !== Auth::user()->profile->id && !Auth::user()->hasRole('admin')) {
            if (request()->ajax()) {
                return response()->json(['error' => 'No tienes permiso para eliminar este archivo.'], 403);
            }
            return back()->with('error', 'No tienes permiso para eliminar este archivo.');
        }

        $attachment->delete();

        if (request()->ajax()) {
            return response()->json([
                'success' => true,
                'message' => 'Archivo eliminado correctamente.',
            ]);
        }

        return back()->with('success', 'Archivo eliminado correctamente.');
    }
}
