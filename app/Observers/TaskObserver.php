<?php

namespace App\Observers;

use App\Models\Task;
use App\Notifications\TaskAssigned;

class TaskObserver
{
    public function created(Task $task): void
    {
        $task->assignees->pluck('user')->filter()->unique('id')->each->notify(new TaskAssigned($task));
    }

    /**
     * Handle the Task "updated" event.
     * 
     * @param Task $task
     * @return void
     */
    public function updated(Task $task): void
    {
        if ($task->wasChanged('status')) {
            $task->project->team->pluck('user')
                ->filter()
                ->unique('id')
                ->each->notify(new \App\Notifications\TaskStatusChanged($task, $task->getOriginal('status'), $task->status));
        }
    }

    /**
     * Handle the Task "saved" event to sync project progress.
     * 
     * @param Task $task
     * @return void
     */
    public function saved(Task $task): void
    {
        if ($task->project) {
            app(\App\Services\ProjectActionService::class)->recalculateProgress($task->project);
        }
    }

    /**
     * Handle the Task "deleted" event to sync project progress.
     * 
     * @param Task $task
     * @return void
     */
    public function deleted(Task $task): void
    {
        if ($task->project) {
            app(\App\Services\ProjectActionService::class)->recalculateProgress($task->project);
        }
    }
}
