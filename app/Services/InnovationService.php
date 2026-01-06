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
     * Create a new innovation proposal and handle initial attachments.
     * 
     * @param array<string, mixed> $data Basic innovation details.
     * @param array<int, \Illuminate\Http\UploadedFile> $files Uploaded evidence documents.
     * @return Innovation
     */
    public function createInnovation(array $data, array $files = []): Innovation
    {
        $payload = array_merge($data, [
            'profile_id' => Auth::user()->profile->id,
            'status'     => 'propuesta'
        ]);

        $innovation = Innovation::create($payload);
        
        $this->processAttachments($innovation, $files);

        return $innovation;
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
        $innovation->update($data);
        
        $this->processAttachments($innovation, $files);
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
        $attachment = $innovation->attachments()->findOrFail($attachmentId);
        
        $attachment->delete();
    }

    /**
     * Iterate through uploaded files and link them to the innovation.
     * 
     * @param Innovation $innovation
     * @param array<int, \Illuminate\Http\UploadedFile> $files
     * @return void
     */
    private function processAttachments(Innovation $innovation, array $files): void
    {
        foreach ($files as $file) {
            $this->storeAndLinkAttachment($innovation, $file);
        }
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
