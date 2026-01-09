<?php

namespace App\Services;

use App\Models\Project;
use App\Models\User;
use App\Notifications\ProjectAssigned;
use App\Notifications\ProjectStatusChanged;
use App\Notifications\ProjectDeadlineChanged;
use Illuminate\Support\Facades\Auth;

/**
 * Service to handle write operations for Projects.
 */
class ProjectActionService
{
    use \App\Traits\HandlesNotifications;

    protected \App\Services\AttachmentService $attachmentService;

    public function __construct(AttachmentService $attachmentService)
    {
        $this->attachmentService = $attachmentService;
    }

    /**
     * Create a new project and handle all associated side effects.
     * 
     * @param array{temp_attachments?: array<int, string>, title: string, category_id: int} $data
     * @param array<int, \Illuminate\Http\UploadedFile> $files
     * @return Project
     */
    public function createProject(array $data, array $files = []): Project
    {
        return \Illuminate\Support\Facades\DB::transaction(function () use ($data, $files) {
            $data['profile_id'] = Auth::user()->profile->id;
            $data['status'] = $data['status'] ?? 'en_progreso';

            $projectData = \Illuminate\Support\Arr::except($data, ['temp_attachments', 'team_members']);
            $project = Project::create($projectData);

            if (!empty($data['team_members'] ?? [])) {
                $project->team()->sync($data['team_members']);
            }
            
            $this->attachmentService->handleUploads($project, $files);
            $this->attachmentService->handleTemporaryFiles($project, $data['temp_attachments'] ?? []);

            return $project;
        });
    }

    /**
     * Update an existing project's data and affiliations.
     * 
     * @param Project $project
     * @param array{temp_attachments?: array<int, string>} $data
     */
    public function updateProject(Project $project, array $data): void
    {
        \Illuminate\Support\Facades\DB::transaction(function () use ($project, $data) {
            $project->update(\Illuminate\Support\Arr::except($data, ['temp_attachments', 'team_members']));

            if (!empty($data['team_members'] ?? [])) {
                $project->team()->sync($data['team_members']);
            }

            $this->attachmentService->handleTemporaryFiles($project, $data['temp_attachments'] ?? []);
        });
    }

    /**
     * Store the final project report and mark the project as finished.
     */
    /**
     * Upload the final report file for a project.
     * 
     * @param Project $project
     * @param \Illuminate\Http\UploadedFile $file
     */
    public function uploadFinalReport(Project $project, $file): void
    {
        $path = $file->store('projects/final-reports', 'public');
        $project->update(['final_report' => $path, 'status' => 'finalizado']);

        $type = \App\Models\ResourceType::firstOrCreate(['slug' => 'documento'], ['name' => 'Documento']);

        $resource = \App\Models\Resource::create([
             'name'             => 'Informe Final - ' . $project->title,
             'description'      => 'Informe final generado automáticamente.',
             'resource_type_id' => $type->id,
             'cost'             => 0,
             'file_path'        => $path,
        ]);
        
        $project->resources()->attach($resource, ['quantity' => 1, 'assigned_date' => now()]);
    }

    /**
     * Calculate and save the project's progress based on completed tasks.
     */
    public function recalculateProgress(Project $project): void
    {
        $total = $project->tasks()->count();
        $done = $project->tasks()->where('status', 'completada')->count();
        $project->update(['completion_percentage' => $total ? round(($done / $total) * 100) : 0]);
    }
}
