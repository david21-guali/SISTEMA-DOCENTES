<?php

namespace App\Services;

use App\Models\Task;
use App\Models\Project;
use App\Models\User;
use App\Notifications\TaskAssigned;
use App\Notifications\TaskStatusChanged;
use App\Notifications\TaskDeadlineChanged;
use Illuminate\Support\Facades\Auth;

/**
 * Service to handle write operations for Tasks.
 */
class TaskActionService
{
    use \App\Traits\HandlesNotifications;

    protected \App\Services\AttachmentService $attachmentService;

    public function __construct(AttachmentService $attachmentService)
    {
        $this->attachmentService = $attachmentService;
    }

    /**
     * Create a new task and handle all associated side effects.
     * 
     * @param array{project_id: int, assignees: array<int, int>, temp_attachments?: array<int, string>, title: string} $data
     * @param array<int, \Illuminate\Http\UploadedFile> $files
     * @return Task
     */
    public function createTask(array $data, array $files = []): Task
    {
        return \Illuminate\Support\Facades\DB::transaction(function () use ($data, $files) {
            $project = Project::with('team')->findOrFail($data['project_id']);
            $this->ensureValidTeamAssignment($project, $data['assignees']);

            $profiles = $this->getUserProfileIds($data['assignees']);
            $taskData = \Illuminate\Support\Arr::except($data, ['temp_attachments', 'assignees']);
            $task = Task::create(array_merge($taskData, ['assigned_to' => $profiles[0] ?? null, 'status' => 'pendiente']));

            $task->assignees()->sync($profiles);

            $this->notifyProfiles($profiles, new TaskAssigned($task));
            $this->attachmentService->handleUploads($task, $files);
            $this->attachmentService->handleTemporaryFiles($task, $data['temp_attachments'] ?? []);

            return $task;
        });
    }

    /**
     * Update an existing task's data and affiliations.
     * 
     * @param Task $task
     * @param array{assignees: array<int, int>, temp_attachments?: array<int, string>} $data
     */
    public function updateTask(Task $task, array $data): void
    {
        \Illuminate\Support\Facades\DB::transaction(function () use ($task, $data) {
            $this->ensureValidTeamAssignment($task->project, $data['assignees']);
            $profiles = $this->getUserProfileIds($data['assignees']);

            $task->update(array_merge(\Illuminate\Support\Arr::except($data, ['temp_attachments', 'assignees']), ['assigned_to' => $profiles[0] ?? null]));
            $task->assignees()->sync($profiles);

            $this->notifyProfiles($profiles, new TaskAssigned($task));

            $this->attachmentService->handleTemporaryFiles($task, $data['temp_attachments'] ?? []);
        });
    }

    /**
     * Remove a task.
     */
    public function deleteTask(Task $task): void
    {
        $task->delete();
    }

    /**
     * Mark a task as completed.
     */
    public function completeTask(Task $task): void
    {
        $task->update(['status' => 'completada', 'completion_date' => now()]);
    }

    /**
     * Ensure assignees belong to the project team.
     * 
     * @param Project $project
     * @param array<int, int> $userIds
     */
    private function ensureValidTeamAssignment(Project $project, array $userIds): void
    {
        $ids = $project->team->pluck('user_id')->push($project->profile->user_id)->unique()->toArray();
        if (!empty(array_diff($userIds, $ids))) {
            throw new \Exception('Solo puedes asignar tareas a miembros del equipo.');
        }
    }

    /**
     * Get profile IDs for a list of user IDs.
     * 
     * @param array<int, int> $userIds
     * @return array<int, int>
     */
    private function getUserProfileIds(array $userIds): array
    {
        return User::whereIn('id', $userIds)->with('profile')->get()->pluck('profile.id')->toArray();
    }


}
