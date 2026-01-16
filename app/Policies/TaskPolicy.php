<?php

namespace App\Policies;

use App\Models\Task;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class TaskPolicy
{
    use HandlesAuthorization;

    /**
     * Determine whether the user can view the task.
     */
    public function view(User $user, Task $task): bool
    {
        // Administradores y coordinadores pueden ver todo
        if ($user->hasRole(['admin', 'coordinador'])) {
            return true;
        }

        $profileId = $user->profile?->id;

        // El usuario puede ver la tarea si está asignada a él
        // o si es el dueño del proyecto al que pertenece la tarea
        return $task->assignees->contains($profileId) || 
               $task->project->profile_id === $profileId;
    }

    /**
     * Determine whether the user can create tasks.
     * Creation is usually handled at the project level, but we check here if they belong to the project.
     */
    public function create(User $user): bool
    {
        return $user->hasRole(['admin', 'coordinador', 'docente']);
    }

    /**
     * Determine whether the user can update the task.
     */
    public function update(User $user, Task $task): bool
    {
        if ($user->hasRole(['admin', 'coordinador'])) {
            return true;
        }

        // Solo el dueño del proyecto puede editar tareas
        return $task->project->profile_id === $user->profile?->id;
    }

    /**
     * Determine whether the user can delete the task.
     */
    public function delete(User $user, Task $task): bool
    {
        return $this->update($user, $task);
    }

    /**
     * Determine whether the user can complete the task.
     */
    public function complete(User $user, Task $task): bool
    {
        if ($user->hasRole(['admin', 'coordinador'])) {
            return true;
        }

        $profileId = $user->profile?->id;

        // El usuario asignado o el dueño del proyecto pueden completarla
        return $task->assignees->contains($profileId) || 
               $task->project->profile_id === $profileId;
    }
}
