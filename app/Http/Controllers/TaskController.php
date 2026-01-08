<?php

namespace App\Http\Controllers;

use App\Models\Task;
use App\Models\Project;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class TaskController extends Controller
{
    protected \App\Services\TaskQueryService $queryService;
    protected \App\Services\TaskActionService $actionService;

    public function __construct(
        \App\Services\TaskQueryService $queryService,
        \App\Services\TaskActionService $actionService
    ) {
        $this->queryService = $queryService;
        $this->actionService = $actionService;
    }

    /**
     * Display a listing of the resource.
     */
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request): \Illuminate\View\View
    {
        $user = Auth::user();
        if (!$user->profile) {
             $user->profile()->create();
             $user->refresh();
        }

        $tasks = $this->queryService->getTasks($user, $request->all());
        $tasks->appends($request->all());
        $stats = $this->queryService->getTaskStats($user);

        return view('app.back.tasks.index', compact('tasks', 'stats'));
    }

    /**
     * Show the form for creating a new resource.
     */
    /**
     * Show the form for creating a new resource.
     */
    public function create(): \Illuminate\View\View
    {
        $projects = $this->queryService->getProjectsForUser(Auth::user());
        $users = User::whereHas('profile', function($q) {
            /** @phpstan-ignore-next-line */
            $q->where('is_active', true);
        })->get();
        return view('app.back.tasks.create', compact('projects', 'users'));
    }

    /**
     * Store a newly created resource in storage.
     */
    /**
     * Store a newly created resource in storage.
     */
    public function store(\App\Http\Requests\StoreTaskRequest $request): \Illuminate\Http\RedirectResponse
    {
        try {
            $this->actionService->createTask($request->validated(), $request->file('attachments', []));
            return redirect()->route('tasks.index')->with('success', 'Tarea creada exitosamente.');
        } catch (\Exception $e) {
            return back()->withInput()->with('swal_error', $e->getMessage());
        }
    }

    /**
     * Display the specified resource.
     */
    /**
     * Display the specified resource.
     */
    public function show(Task $task): \Illuminate\View\View
    {
        $task->load(['project', 'assignedProfile.user', 'assignees.user']);
        return view('app.back.tasks.show', compact('task'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Task $task): \Illuminate\View\View
    {
        $projects = $this->queryService->getProjectsForUser(Auth::user());
        $users = User::whereHas('profile', function($q) {
            /** @phpstan-ignore-next-line */
            $q->where('is_active', true);
        })->get();
        return view('app.back.tasks.edit', compact('task', 'projects', 'users'));
    }

    /**
     * Update the specified resource in storage.
     */
    /**
     * Update the specified resource in storage.
     */
    public function update(\App\Http\Requests\UpdateTaskRequest $request, Task $task): \Illuminate\Http\RedirectResponse
    {
        try {
            $this->actionService->updateTask($task, $request->validated());
            return redirect()->route('tasks.index')->with('success', 'Tarea actualizada.');
        } catch (\Exception $e) {
            return back()->withInput()->with('swal_error', $e->getMessage());
        }
    }

    public function destroy(Task $task): \Illuminate\Http\RedirectResponse
    {
        $this->actionService->deleteTask($task);
        return redirect()->route('tasks.index')->with('success', 'Tarea eliminada exitosamente.');
    }

    public function complete(Task $task): \Illuminate\Http\RedirectResponse
    {
        $this->actionService->completeTask($task);
        return back()->with('success', 'Tarea marcada como completada.');
    }
}
