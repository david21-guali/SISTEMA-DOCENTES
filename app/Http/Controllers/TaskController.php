<?php

namespace App\Http\Controllers;

use App\Models\Task;
use App\Models\Project;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class TaskController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $user = Auth::user();
        if (!$user->profile) {
             $user->profile()->create();
             $user->refresh();
        }
        $profileId = $user->profile->id;

        if ($user->hasRole(['admin', 'coordinador'])) {
            // Admin y Coordinador ven todas las tareas
            // Eager load assignedProfile and assignees (profiles)
            $query = Task::with(['project', 'assignedProfile.user', 'assignees.user']);
        } else {
            // Docente: solo tareas de proyectos donde participa
            $query = Task::with(['project', 'assignedProfile.user', 'assignees.user'])
                ->where(function($q) use ($profileId) {
                    $q->whereHas('project.team', fn($subQ) => $subQ->where('profiles.id', $profileId))
                      ->orWhereHas('project', fn($subQ) => $subQ->where('profile_id', $profileId));
                });
        }

        if ($request->has('status')) {
            $status = $request->input('status');
            if ($status === 'atrasada') {
                $query->overdue();
            } else {
                $query->where('status', $status);
            }
        }

        $tasks = $query->latest()->paginate(15);
        $tasks->appends($request->all()); // Mantener filtros en paginación

        return view('app.back.tasks.index', compact('tasks'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $user = Auth::user();

        if ($user->hasRole(['admin', 'coordinador'])) {
            $projects = Project::all();
        } else {
            // Docente: solo proyectos donde participa
            $projects = Project::where(function($query) use ($user) {
                // Ensure profile exists
                $profileId = $user->profile->id ?? null; // Should be handled by now
                $query->whereHas('team', fn($q) => $q->where('profiles.id', $profileId))
                      ->orWhere('profile_id', $profileId);
            })->get();
        }

        $users = User::whereHas('profile', function($q) {
            $q->where('is_active', true);
        })->get();
        
        return view('app.back.tasks.create', compact('projects', 'users'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'project_id' => 'required|exists:projects,id',
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'assignees' => 'required|array|min:1',
            'assignees.*' => 'exists:users,id',
            'due_date' => [
                'required',
                'date',
                function ($attribute, $value, $fail) use ($request) {
                    $project = Project::find($request->project_id);
                    if ($project) {
                        $date = \Carbon\Carbon::parse($value);
                        
                        if ($project->start_date && $date->lt($project->start_date->startOfDay())) {
                            $fail('La fecha de vencimiento no puede ser anterior al inicio del proyecto (' . $project->start_date->format('d/m/Y') . ').');
                        }
                        
                        if ($project->end_date && $date->gt($project->end_date->endOfDay())) {
                            $fail('La fecha de vencimiento no puede ser posterior al fin del proyecto (' . $project->end_date->format('d/m/Y') . ').');
                        }
                    }
                },
            ],
            'priority' => 'required|in:baja,media,alta',
        ], [
            'required' => 'El campo :attribute es obligatorio.',
        ], [
            'project_id' => 'proyecto',
            'title' => 'título',
            'description' => 'descripción',
            'assignees' => 'asignados',
            'due_date' => 'fecha límite',
            'priority' => 'prioridad',
        ]);

        $validated['status'] = 'pendiente';

        // Validar que los asignados sean miembros del equipo del proyecto (Profiles)
        $project = Project::with('team')->find($validated['project_id']);
        
        // Members are Profiles. Validated inputs are User IDs.
        // We need to verify if the User IDs correspond to the Profiles in the team.
        // Get User IDs associated with the team profiles.
        
        $teamProfileUserIds = \App\Models\User::whereHas('profile', function($q) use ($project) {
             $q->whereIn('id', $project->team->pluck('id'));
        })->pluck('id')->toArray();
        
        // Include project creator (User ID of the profile owner)
        /** @var \App\Models\User|null $creatorUser */
        /** @phpstan-ignore-next-line */
        $creatorUser = $project->profile->user; // Access user via profile
        if ($creatorUser) $teamProfileUserIds[] = $creatorUser->id;
        
        $teamProfileUserIds = array_unique($teamProfileUserIds);

        $assigneeUserIds = $validated['assignees'];
        $invalidAssignees = array_diff($assigneeUserIds, $teamProfileUserIds);
        
        if (!empty($invalidAssignees)) {
            return back()
                ->withInput()
                ->with('swal_error', 'Solo puedes asignar tareas a miembros del equipo del proyecto.');
        }

        // Convert User IDs to Profile IDs
        $assigneeProfileIds = \App\Models\User::whereIn('id', $assigneeUserIds)
            ->with('profile')
            ->get()
            ->pluck('profile.id')
            ->toArray();

        // assigned_to column (Profile ID)
        if (!empty($assigneeProfileIds)) {
            $validated['assigned_to'] = $assigneeProfileIds[0];
        }

        unset($validated['assignees']);

        $task = Task::create($validated);
        
        // Sync multiple assignees (Profiles)
        if (!empty($assigneeProfileIds)) {
            $task->assignees()->sync($assigneeProfileIds);
            
            // Notify all assignees (via User)
             foreach ($assigneeUserIds as $userId) {
                $user = User::find($userId);
                if ($user) {
                    $user->notify(new \App\Notifications\TaskAssigned($task));
                }
            }
        }

        // Recalcular progreso del proyecto
        // Recalcular progreso del proyecto
        if ($task->project instanceof \App\Models\Project) {
            $task->project->recalculateProgress();
        }

        // Handle file attachments
        if ($request->hasFile('attachments')) {
            foreach ($request->file('attachments') as $file) {
                $path = $file->store('attachments/tasks/' . $task->id, 'public');
                
                $task->attachments()->create([
                    'filename' => basename($path),
                    'original_name' => $file->getClientOriginalName(),
                    'mime_type' => $file->getMimeType(),
                    'size' => $file->getSize(),
                    'path' => $path,
                    'uploaded_by' => Auth::user()->profile->id,
                ]);
            }
        }

        return redirect()->route('tasks.index')
            ->with('success', 'Tarea creada exitosamente.');
    }

    /**
     * Display the specified resource.
     */
    public function show(Task $task)
    {
        $task->load(['project', 'assignedProfile.user', 'assignees.user']);
        return view('app.back.tasks.show', compact('task'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Task $task)
    {
        $user = Auth::user();

        if ($user->hasRole(['admin', 'coordinador'])) {
            $projects = Project::all();
        } else {
            // Docente: solo proyectos donde participa
            $projects = Project::where(function($query) use ($user) {
                $profileId = $user->profile->id ?? null;
                $query->whereHas('team', fn($q) => $q->where('profiles.id', $profileId))
                      ->orWhere('profile_id', $profileId);
            })->get();
        }

        $users = User::whereHas('profile', function($q) {
            $q->where('is_active', true);
        })->get();
        
        return view('app.back.tasks.edit', compact('task', 'projects', 'users'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Task $task)
    {
        $validated = $request->validate([
            'project_id' => 'required|exists:projects,id',
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'assignees' => 'required|array|min:1',
            'assignees.*' => 'exists:users,id',
            'due_date' => [
                'required',
                'date',
                function ($attribute, $value, $fail) use ($request) {
                    $project = Project::find($request->project_id);
                    if ($project) {
                        $date = \Carbon\Carbon::parse($value);
                        
                        /** @phpstan-ignore-next-line */
                        if ($date->lt($project->start_date->startOfDay())) {
                            $fail('La fecha de vencimiento no puede ser anterior al inicio del proyecto (' . $project->start_date->format('d/m/Y') . ').');
                        }
                        
                        /** @phpstan-ignore-next-line */
                        if ($date->gt($project->end_date->endOfDay())) {
                            $fail('La fecha de vencimiento no puede ser posterior al fin del proyecto (' . $project->end_date->format('d/m/Y') . ').');
                        }
                    }
                },
            ],
            'status' => 'required|in:pendiente,en_progreso,completada,atrasada',
            'priority' => 'required|in:baja,media,alta',
        ], [
            'required' => 'El campo :attribute es obligatorio.',
        ], [
            'project_id' => 'proyecto',
            'title' => 'título',
            'description' => 'descripción',
            'assignees' => 'asignados',
            'due_date' => 'fecha límite',
            'status' => 'estado',
            'priority' => 'prioridad',
        ]);

        // Validar que los asignados sean miembros del equipo del proyecto (Profiles)
        $project = Project::with('team')->find($validated['project_id']);
        
        $teamProfileUserIds = \App\Models\User::whereHas('profile', function($q) use ($project) {
             $q->whereIn('id', $project->team->pluck('id'));
        })->pluck('id')->toArray();
        
        // Include project creator
        /** @var \App\Models\User|null $creatorUser */
        /** @phpstan-ignore-next-line */
        $creatorUser = $project->profile->user;
        if ($creatorUser) $teamProfileUserIds[] = $creatorUser->id;
        
        $teamProfileUserIds = array_unique($teamProfileUserIds);

        $assigneeUserIds = $validated['assignees'];
        $invalidAssignees = array_diff($assigneeUserIds, $teamProfileUserIds);

        if (!empty($invalidAssignees)) {
            return back()
                ->withInput()
                ->with('swal_error', 'Solo puedes asignar tareas a miembros del equipo del proyecto.');
        }
        
        // Convert User IDs to Profile IDs
        $assigneeProfileIds = \App\Models\User::whereIn('id', $assigneeUserIds)
            ->with('profile')
            ->get()
            ->pluck('profile.id')
            ->toArray();


        if (!empty($assigneeProfileIds)) {
            $validated['assigned_to'] = $assigneeProfileIds[0];
        } else {
             $validated['assigned_to'] = null;
        }

        unset($validated['assignees']);

        $task->update($validated);
        
        // Sync assignees
        $task->assignees()->sync($assigneeProfileIds);

        // Recalcular progreso del proyecto
        $task->project->recalculateProgress();

        return redirect()->route('tasks.index')
            ->with('success', 'Tarea actualizada exitosamente.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Task $task)
    {
        $projectId = $task->project_id;
        $task->delete();

        // Recalcular progreso del proyecto
        /** @var \App\Models\Project $project */
        $project = Project::find($projectId);
        /** @phpstan-ignore-next-line */
        if ($project) {
            $project->recalculateProgress();
        }

        return redirect()->route('tasks.index')
            ->with('success', 'Tarea eliminada exitosamente.');
    }

    /**
     * Mark task as completed.
     */
    public function complete(Task $task)
    {
        $task->update([
            'status' => 'completada',
            'completion_date' => now(),
        ]);

        // Recalcular progreso del proyecto
        // Recalcular progreso del proyecto
        if ($task->project instanceof \App\Models\Project) {
            $task->project->recalculateProgress();
        }

        return back()->with('success', 'Tarea marcada como completada.');
    }
}
