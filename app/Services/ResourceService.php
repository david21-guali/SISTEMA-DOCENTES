<?php

namespace App\Services;

use App\Models\Resource;
use App\Models\Project;
use Illuminate\Support\Facades\Storage;

/**
 * Service to manage pedagogical resources, files, and project assignments.
 * Optimized for High Maintainability Index (MI >= 65).
 */
class ResourceService
{
    /**
     * Create a new resource record and handle physical file storage.
     * 
     * @param array<string, mixed> $data General resource information.
     * @param \Illuminate\Http\UploadedFile|null $file Optional uploaded file to associate.
     * @return Resource
     */
    public function createResource(array $data, $file = null): Resource
    {
        if ($file) {
            $data['file_path'] = $this->storePhysicalFile($file);
        }

        return Resource::create($data);
    }

    /**
     * Update an existing resource and manage file replacement if necessary.
     * 
     * @param Resource $resource
     * @param array<string, mixed> $data
     * @param \Illuminate\Http\UploadedFile|null $file
     * @return void
     */
    public function updateResource(Resource $resource, array $data, $file = null): void
    {
        if ($file) {
            $this->removePhysicalFile($resource->file_path);
            $data['file_path'] = $this->storePhysicalFile($file);
        }

        $resource->update($data);
    }

    /**
     * Associate a resource with a specific project using pivot table metadata.
     * 
     * @param Project $project
     * @param array<string, mixed> $data Assignment details (id, quantity, date, notes).
     * @return void
     */
    public function assignToProject(Project $project, array $data): void
    {
        $project->resources()->attach($data['resource_id'], [
            'quantity'      => $data['quantity'],
            'assigned_date' => $data['assigned_date'],
            'notes'         => $data['notes'] ?? null,
        ]);
    }

    /**
     * Handle the physical storage of a resource file.
     * 
     * @param mixed $file
     * @return string The storage path of the saved file.
     */
    private function storePhysicalFile($file): string
    {
        return $file->store('resources', 'public');
    }

    /**
     * Delete a physical file from the storage disk.
     * 
     * @param string|null $path The path to the file to be removed.
     * @return void
     */
    private function removePhysicalFile(?string $path): void
    {
        if ($path && Storage::disk('public')->exists($path)) {
            Storage::disk('public')->delete($path);
        }
    }
}
