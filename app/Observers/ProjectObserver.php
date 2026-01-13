<?php

namespace App\Observers;

use App\Models\Project;
use App\Models\User;
use App\Notifications\ProjectAssigned;
use App\Notifications\ProjectStatusChanged;
use App\Notifications\ProjectDeadlineChanged;

class ProjectObserver
{
    /**
     * Handle the Project "updated" event.
     *
     * @param Project $project
     * @return void
     */
    public function created(Project $project): void
    {
        $project->team->pluck('user')
            ->filter()
            ->unique('id')
            ->each->notify(new \App\Notifications\ProjectAssigned($project));
    }

    public function updated(Project $project): void
    {
        if ($project->wasChanged('status')) {
            $project->team->pluck('user')
                ->push($project->profile->user ?? null)
                ->filter()
                ->unique('id')
                ->each->notify(new ProjectStatusChanged($project, $project->getOriginal('status'), $project->status));
        }

        if ($project->wasChanged('end_date')) {
            $old = \Illuminate\Support\Carbon::parse($project->getOriginal('end_date'));
            $project->team->pluck('user')->push($project->profile->user)
                ->unique('id')->filter()
                ->each->notify(new ProjectDeadlineChanged($project, $old, $project->end_date));
        }
    }
}
