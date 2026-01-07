<?php

namespace App\Services;

use App\Models\Task;
use App\Models\Project;
use App\Models\User;
use App\Notifications\TaskAssigned;
use Illuminate\Support\Facades\Auth;

/**
 * Service to handle write operations for Tasks.
 * Optimized for High Maintainability Index (MI >= 65).
 */
class TaskActionService
{
    use \App\Traits\HandlesNotifications;

    /**
     * Create a new task and handle all associated side effects.
     * 
     * @param array<string, mixed> $data Task data including project_id and assignees.
     * @param array<int, \Illuminate\Http\UploadedFile> $files Uploaded attachments.
     * @return Task
     */
    public function createTask(array $data, array $files = []): Task
    {
        return \Illuminate\Support\Facades\DB::transaction(function () use ($data, $files) {
            /** @var \App\Models\Project $project */
            $project = Project::with('team')->findOrFail($data['project_id']);
            $this->ensureValidTeamAssignment($project, $data['assignees']);

            $assigneeProfileIds = $this->getUserProfileIds($data['assignees']);
            
            $task = Task::create(array_merge($data, [
                'assigned_to' => $assigneeProfileIds[0] ?? null,
                'status'      => 'pendiente'
            ]));

            $this->finalizeTaskSetup($task, $project, $assigneeProfileIds, $files, $data['temp_attachments'] ?? []);

            return $task;
        });
    }

    /**
     * Update an existing task's data and affiliations.
     * 
     * @param Task $task
     * @param array<string, mixed> $data
     * @return void
     */
    public function updateTask(Task $task, array $data): void
    {
        \Illuminate\Support\Facades\DB::transaction(function () use ($task, $data) {
            $this->ensureValidTeamAssignment($task->project, $data['assignees']);
            $assigneeProfileIds = $this->getUserProfileIds($data['assignees']);
            
            $task->update(array_merge($data, [
                'assigned_to' => $assigneeProfileIds[0] ?? null
            ]));

            $task->assignees()->sync($assigneeProfileIds);
            $this->handleTemporaryAttachments($task, $data['temp_attachments'] ?? []);
            
            $this->refreshProjectProgress($task->project);
        });
    }

    /**
     * Remove a task and update project progress accordingly.
     * 
     * @param Task $task
     * @return void
     */
    public function deleteTask(Task $task): void
    {
        $project = $task->project;
        $task->delete();

        if ($project) {
            $this->refreshProjectProgress($project);
        }
    }

    /**
     * Mark a task as completed and update project stats.
     * 
     * @param Task $task
     * @return void
     */
    public function completeTask(Task $task): void
    {
        $task->update([
            'status'          => 'completada',
            'completion_date' => now(),
        ]);

        if ($task->project) {
            $this->refreshProjectProgress($task->project);
        }
    }

    /**
     * Complete the setup for a newly created task.
     * 
     * @param Task $task
     * @param Project $project
     * @param array<int, int> $assigneeIds
     * @param array<int, \Illuminate\Http\UploadedFile> $files
     * @param array<int, string> $tempPaths
     * @return void
     */
    private function finalizeTaskSetup(Task $task, Project $project, array $assigneeIds, array $files, array $tempPaths = []): void
    {
        $task->assignees()->sync($assigneeIds);

        $this->notifyAssignedUsers($task, $assigneeIds);
        $this->refreshProjectProgress($project);
        $this->handleAttachments($task, $files);
        $this->handleTemporaryAttachments($task, $tempPaths);
    }

    /**
     * Ensure assignees belong to the project team.
     * 
     * @param Project $project
     * @param array<int, int> $userIds
     * @return void
     * @throws \Exception
     */
    private function ensureValidTeamAssignment(Project $project, array $userIds): void
    {
        $validIds = $this->getValidTeamUserIds($project);
        $invalid  = array_diff($userIds, $validIds);

        if (!empty($invalid)) {
            throw new \Exception('Solo puedes asignar tareas a miembros del equipo.');
        }
    }

    /**
     * Consolidate IDs of users eligible for task assignment.
     * 
     * @param Project $project
     * @return array<int, int>
     */
    private function getValidTeamUserIds(Project $project): array
    {
        $ids = $project->team->pluck('user_id')->toArray();
        
        if ($project->profile->user_id) {
            $ids[] = $project->profile->user_id;
        }

        return array_unique($ids);
    }

    /**
     * Get profile IDs for a list of user IDs.
     * 
     * @param array<int, int> $userIds
     * @return array<int, int>
     */
    private function getUserProfileIds(array $userIds): array
    {
        return User::whereIn('id', $userIds)
            ->with('profile')
            ->get()
            ->pluck('profile.id')
            ->toArray();
    }

    /**
     * Notify users about their new task assignment.
     * 
     * @param Task $task
     * @param array<int, int> $userIds
     * @return void
     */
    private function notifyAssignedUsers(Task $task, array $userIds): void
    {
        $users = User::whereIn('id', $userIds)->get();
        $this->notifyUsers($users, new TaskAssigned($task));
    }

    /**
     * Recalculate progress for the associated project.
     * 
     * @param Project|null $project
     * @return void
     */
    private function refreshProjectProgress(?Project $project): void
    {
        if ($project) {
            (new ProjectActionService())->recalculateProgress($project);
        }
    }

    /**
     * Store and associate uploaded files with the task.
     * 
     * @param Task $task
     * @param array<int, \Illuminate\Http\UploadedFile> $files
     * @return void
     */
    private function handleAttachments(Task $task, array $files): void
    {
        foreach ($files as $file) {
            $this->processSingleAttachment($task, $file);
        }
    }

    /**
     * Process files that were uploaded via AJAX to a temporary directory.
     * 
     * @param Task $task
     * @param array<int, string> $tempPaths
     * @return void
     */
    private function handleTemporaryAttachments(Task $task, array $tempPaths): void
    {
        foreach ($tempPaths as $value) {
            $data = json_decode($value, true);
            $path = $data ? $data['path'] : $value;
            $originalName = $data ? $data['name'] : basename($path);

            if (\Illuminate\Support\Facades\Storage::disk('public')->exists($path)) {
                $permanentDir = 'attachments/tasks/' . $task->id;
                $permanentPath = $permanentDir . '/' . basename($path);
                
                \Illuminate\Support\Facades\Storage::disk('public')->makeDirectory($permanentDir);
                \Illuminate\Support\Facades\Storage::disk('public')->copy($path, $permanentPath);
                \Illuminate\Support\Facades\Storage::disk('public')->delete($path);

                $task->attachments()->create([
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
     * Process a single file upload for the task.
     * 
     * @param Task $task
     * @param \Illuminate\Http\UploadedFile $file
     * @return void
     */
    private function processSingleAttachment(Task $task, $file): void
    {
        $path = $file->store('attachments/tasks/' . $task->id, 'public');
        
        $task->attachments()->create([
            'filename'      => basename((string)$path),
            'original_name' => $file->getClientOriginalName(),
            'mime_type'     => $file->getMimeType(),
            'size'          => $file->getSize(),
            'path'          => $path,
            'uploaded_by'   => Auth::user()->profile->id,
        ]);
    }
}
