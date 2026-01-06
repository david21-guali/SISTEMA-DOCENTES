<?php

namespace App\Http\Controllers;

use App\Models\Project;
use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use App\Models\Resource;
use App\Models\ResourceType;

class ProjectController extends Controller
{
    protected $queryService;
    protected $actionService;

    public function __construct(
        \App\Services\ProjectQueryService $queryService,
        \App\Services\ProjectActionService $actionService
    ) {
        $this->queryService = $queryService;
        $this->actionService = $actionService;
    }

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $projects = $this->queryService->getProjects($request->all());
        $stats = $this->queryService->getStats();

        return view('app.back.projects.index', compact('projects', 'stats'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $categories = Category::all();
        $users = \App\Models\User::whereHas('profile', function($q) {
            $q->where('is_active', true);
        })->get();
        return view('app.back.projects.create', compact('categories', 'users'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(\App\Http\Requests\StoreProjectRequest $request)
    {
        $this->actionService->createProject($request->validated(), $request->file('attachments') ?? []);

        return redirect()->route('projects.index')
            ->with('success', 'Proyecto creado exitosamente.');
    }

    /**
     * Display the specified resource.
     */
    public function show(Project $project)
    {
        $project->load(['category', 'profile', 'tasks', 'team']);
        return view('app.back.projects.show', compact('project'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Project $project)
    {
        $categories = Category::all();
        $users = \App\Models\User::whereHas('profile', function($q) {
            $q->where('is_active', true);
        })->get();
        return view('app.back.projects.edit', compact('project', 'categories', 'users'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(\App\Http\Requests\UpdateProjectRequest $request, Project $project)
    {
        $this->actionService->updateProject($project, $request->validated());

        return redirect()->route('projects.index')
            ->with('success', 'Proyecto actualizado exitosamente.');
    }

    public function uploadFinalReport(Request $request, Project $project)
    {
        if (Auth::user()->profile->id !== $project->profile_id && !Auth::user()->hasRole('admin')) {
            abort(403, 'No tienes permiso.');
        }

        $request->validate(['file' => 'required|file|mimes:pdf|max:10240']);

        if ($request->hasFile('file')) {
            $this->actionService->uploadFinalReport($project, $request->file('file'));
            return back()->with('success', 'Informe final subido.');
        }

        return back()->with('error', 'Error.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Project $project)
    {
        $project->delete();

        return redirect()->route('projects.index')
            ->with('success', 'Proyecto eliminado exitosamente.');
    }
}
