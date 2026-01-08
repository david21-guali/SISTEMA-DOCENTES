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
 * Optimized for High Maintainability Index (MI >= 65).
 */
class ProjectActionService
{
    use \App\Traits\HandlesNotifications;

    /**
     * Create a new project and initialize its team and attachments.
     * 
     * @param array<string, mixed> $data Basic project details and team list.
     * @param array<int, \Illuminate\Http\UploadedFile> $files Uploaded project-level documents.
     * @return Project
     */
    public function createProject(array $data, array $files = []): Project
    {
        return \Illuminate\Support\Facades\DB::transaction(function () use ($data, $files) {
            $data['profile_id'] = Auth::user()->profile->id;
            $data['status'] = $data['status'] ?? 'en_progreso';

            $project = Project::create($data);

            $this->setupProjectTeam($project, $data['team_members'] ?? []);
            $this->handleAttachments($project, $files);
            $this->handleTemporaryAttachments($project, $data['temp_attachments'] ?? []);

            return $project;
        });
    }

    /**
     * Update project information and handle status change notifications.
     * 
     * @param Project $project
     * @param array<string, mixed> $data
     * @return void
     */
    public function updateProject(Project $project, array $data): void
    {
        \Illuminate\Support\Facades\DB::transaction(function () use ($project, $data) {
            $oldStatus = $project->status;
            $oldEndDate = $project->end_date;
            
            $project->update($data);

            $this->updateProjectTeam($project, $data['team_members'] ?? []);
            $this->handleTemporaryAttachments($project, $data['temp_attachments'] ?? []);
            $this->checkStatusChange($project, $oldStatus);
            $this->checkDeadlineChange($project, $oldEndDate);
        });
    }

    /**
     * Check if project end date changed and notify the team.
     * 
     * @param Project $project
     * @param \Carbon\Carbon|string|null $oldEndDate
     */
    private function checkDeadlineChange(Project $project, $oldEndDate): void
    {
        $old = $oldEndDate instanceof \Carbon\Carbon ? $oldEndDate : (\Illuminate\Support\Carbon::parse($oldEndDate));
        
        if ($project->end_date && !$old->isSameDay($project->end_date)) {
            $users = $project->team->pluck('user')->push($project->profile->user)->unique('id')->filter();
            $this->notifyUsers($users, new ProjectDeadlineChanged($project, $old, $project->end_date));
        }
    }

    /**
     * Store the final project report and mark the project as finished.
     * 
     * @param Project $project
     * @param mixed $file
     * @return void
     */
    public function uploadFinalReport(Project $project, $file): void
    {
        $path = $file->store('projects/final-reports', 'public');
        
        $project->update([
            'final_report' => $path, 
            'status'       => 'finalizado'
        ]);

        // Create Resource for the report
        $type = \App\Models\ResourceType::firstOrCreate(
            ['slug' => 'documento'], 
            ['name' => 'Documento', 'description' => 'Documentos generales']
        );

        $resource = \App\Models\Resource::create([
             'name' => 'Informe Final - ' . $project->title,
             'description' => 'Informe final generado automáticamente.',
             'resource_type_id' => $type->id,
             'cost' => 0,
             'file_path' => $path,
        ]);
        
        $project->resources()->attach($resource, ['quantity' => 1, 'assigned_date' => now()]);
    }

    /**
     * Calculate and save the project's progress based on completed tasks.
     * 
     * @param Project $project
     * @return void
     */
    public function recalculateProgress(Project $project): void
    {
        $totalTasks = $project->tasks()->count();
        
        if ($totalTasks === 0) {
            $project->update(['completion_percentage' => 0]);
            return;
        }

        $completedTasks = $project->tasks()->where('status', 'completada')->count();
        $percentage = round(($completedTasks / $totalTasks) * 100);

        $project->update(['completion_percentage' => $percentage]);
    }

    /**
     * Synchronize and notify team members on project creation.
     * 
     * @param Project $project
     * @param array<int, int> $memberIds
     * @return void
     */
    private function setupProjectTeam(Project $project, array $memberIds): void
    {
        if (empty($memberIds)) {
            return;
        }

        $project->team()->sync($memberIds);
        $users = User::whereIn('id', $memberIds)->get();
        
        $this->notifyUsers($users, new ProjectAssigned($project));
    }

    /**
     * Update project team membership.
     * 
     * @param Project $project
     * @param array<int, int> $memberIds
     * @return void
     */
    private function updateProjectTeam(Project $project, array $memberIds): void
    {
        if (!empty($memberIds)) {
            $project->team()->sync($memberIds);
        }
    }

    /**
     * Notify the team if the project status has been updated.
     * 
     * @param Project $project
     * @param string $oldStatus
     * @return void
     */
    private function checkStatusChange(Project $project, string $oldStatus): void
    {
        if ($oldStatus !== $project->status) {
            $this->notifyStatusChange($project, $oldStatus, $project->status);
        }
    }

    /**
     * Execute status change notification.
     * 
     * @param Project $project
     * @param string $oldStatus
     * @param string $newStatus
     * @return void
     */
    private function notifyStatusChange(Project $project, string $oldStatus, string $newStatus): void
    {
        $users = $project->team->pluck('user');
        $this->notifyUsers($users, new ProjectStatusChanged($project, $oldStatus, $newStatus));
    }

    /**
     * Store and associate uploaded files with the project.
     * 
     * @param Project $project
     * @param array<int, \Illuminate\Http\UploadedFile> $files
     * @return void
     */
    private function handleAttachments(Project $project, array $files): void
    {
        foreach ($files as $file) {
            $this->processSingleAttachment($project, $file);
        }
    }

    /**
     * Process files that were uploaded via AJAX to a temporary directory.
     * 
     * @param Project $project
     * @param array<int, string> $tempPaths
     * @return void
     */
    private function handleTemporaryAttachments(Project $project, array $tempPaths): void
    {
        foreach ($tempPaths as $value) {
            $data = json_decode($value, true);
            $path = $data ? $data['path'] : $value;
            $originalName = $data ? $data['name'] : basename($path);

            if (\Illuminate\Support\Facades\Storage::disk('public')->exists($path)) {
                $permanentDir = 'attachments/projects/' . $project->id;
                $permanentPath = $permanentDir . '/' . basename($path);
                
                \Illuminate\Support\Facades\Storage::disk('public')->makeDirectory($permanentDir);
                \Illuminate\Support\Facades\Storage::disk('public')->copy($path, $permanentPath);
                \Illuminate\Support\Facades\Storage::disk('public')->delete($path);

                $project->attachments()->create([
                    'filename'      => basename($permanentPath),
                    'original_name' => $originalName, 
                    'mime_type'     => \Illuminate\Support\Facades\Storage::disk('public')->mimeType($permanentPath),
                    'size'          => \Illuminate\Support\Facades\Storage::disk('public')->size($permanentPath),
                    'path'          => $permanentPath,
                    'uploaded_by'   => Auth::user()->profile->id,
                ]);
            }
        }
    }

    /**
     * Process a single file upload for the project.
     * 
     * @param Project $project
     * @param \Illuminate\Http\UploadedFile $file
     * @return void
     */
    private function processSingleAttachment(Project $project, $file): void
    {
        $path = $file->store('attachments/projects/' . $project->id, 'public');
        
        $project->attachments()->create([
            'filename'      => basename((string)$path),
            'original_name' => $file->getClientOriginalName(),
            'mime_type'     => $file->getMimeType(),
            'size'          => $file->getSize(),
            'path'          => $path,
            'uploaded_by'   => Auth::user()->profile->id,
        ]);
    }
}
