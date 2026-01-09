<?php

namespace App\Services;

use App\Models\Innovation;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

/**
 * Service to manage pedagogical innovation initiatives and their documentation.
 * Optimized for High Maintainability Index (MI >= 65).
 */
class InnovationService
{
    /**
     * Get statistics for innovations by status.
     * 
     * @return array<string, int>
     */
    public function getStats(): array
    {
        return [
            'total'             => Innovation::count(),
            'aprobada'          => Innovation::where('status', 'aprobada')->count(),
            'en_revision'       => Innovation::where('status', 'en_revision')->count(),
            'rechazada'         => Innovation::where('status', 'rechazada')->count(),
            'propuesta'         => Innovation::where('status', 'propuesta')->count(),
            'en_implementacion' => Innovation::where('status', 'en_implementacion')->count(),
        ];
    }

    /**
     * Create a new innovation proposal and handle initial attachments.
     * 
     * @param array<string, mixed> $data Basic innovation details.
     * @param array<int, \Illuminate\Http\UploadedFile> $files Uploaded evidence documents.
     * @return Innovation
     */
    public function createInnovation(array $data, array $files = []): Innovation
    {
        return \Illuminate\Support\Facades\DB::transaction(function () use ($data, $files) {
            $payload = array_merge($data, [
                'profile_id' => Auth::user()->profile->id,
                'status'     => 'propuesta'
            ]);

            $innovation = Innovation::create($payload);
            
            $this->processAttachments($innovation, $files);

            return $innovation;
        });
    }

    /**
     * Update an existing innovation and manage new evidence files.
     * 
     * @param Innovation $innovation
     * @param array<string, mixed> $data Updated fields.
     * @param array<int, \Illuminate\Http\UploadedFile> $files New evidence documents.
     * @return void
     */
    public function updateInnovation(Innovation $innovation, array $data, array $files = []): void
    {
        \Illuminate\Support\Facades\DB::transaction(function () use ($innovation, $data, $files) {
            $innovation->update($data);
            
            $this->processAttachments($innovation, $files);
        });
    }

    /**
     * Permanently remove an innovation and its associated documents.
     * 
     * @param Innovation $innovation
     * @return void
     */
    public function deleteInnovation(Innovation $innovation): void
    {
        $this->cleanupAttachments($innovation);
        
        $innovation->delete();
    }

    /**
     * Delete a single attachment associated with an innovation.
     * 
     * @param Innovation $innovation
     * @param int $attachmentId
     * @return void
     */
    public function deleteAttachment(Innovation $innovation, int $attachmentId): void
    {
        $innovation->attachments()->findOrFail($attachmentId)->delete();
    }

    /**
     * Approve an innovation and save review notes.
     * 
     * @param array{review_notes: string} $data
     */
    public function approve(Innovation $innovation, array $data): void
    {
        $innovation->update(array_merge($data, [
            'status' => 'aprobada', 'reviewed_by' => Auth::id(), 'reviewed_at' => now()
        ]));
    }

    /**
     * Reject an innovation and save review notes.
     * 
     * @param array{review_notes: string} $data
     */
    public function reject(Innovation $innovation, array $data): void
    {
        $innovation->update(array_merge($data, [
            'status' => 'rechazada', 'reviewed_by' => Auth::id(), 'reviewed_at' => now()
        ]));
    }

    public function requestReview(Innovation $innovation): void
    {
        abort_if(in_array($innovation->status, ['aprobada', 'en_revision']), 400);
        $innovation->update(['status' => 'en_revision']);
    }

    /**
     * Process evidence attachments.
     * 
     * @param Innovation $innovation
     * @param array<int, \Illuminate\Http\UploadedFile> $files
     */
    private function processAttachments(Innovation $innovation, array $files): void
    {
        collect($files)->each(fn($f) => $this->storeAndLinkAttachment($innovation, $f));
    }

    /**
     * Store a file on disk and create an attachment record.
     * 
     * @param Innovation $innovation
     * @param mixed $file
     * @return void
     */
    private function storeAndLinkAttachment(Innovation $innovation, $file): void
    {
        $path = $file->store('innovations/evidence', 'public');

        $innovation->attachments()->create([
            'filename'      => $file->hashName(),
            'original_name' => $file->getClientOriginalName(),
            'mime_type'     => $file->getMimeType(),
            'size'          => $file->getSize(),
            'path'          => $path,
            'uploaded_by'   => Auth::user()->profile->id,
        ]);
    }

    /**
     * Remove all files and records related to an innovation's evidence.
     * 
     * @param Innovation $innovation
     * @return void
     */
    private function cleanupAttachments(Innovation $innovation): void
    {
        foreach ($innovation->attachments as $attachment) {
            $attachment->delete();
        }
    }
}
