<?php

namespace App\Http\Controllers;

use App\Models\Resource;
use App\Models\Project;
use Illuminate\Http\Request;

class ResourceController extends Controller
{
    protected $resourceService;

    public function __construct(\App\Services\ResourceService $resourceService)
    {
        $this->resourceService = $resourceService;
    }

    /**
     * Display a listing of the resource catalog.
     */
    /**
     * Display a listing of the resource catalog.
     */
    public function index(): \Illuminate\View\View
    {
        $resources = Resource::with('type')->get();
        $types = \App\Models\ResourceType::all();
        
        // Prepare data for distribution chart
        $distributionData = $types->map(function($type) use ($resources) {
            return [
                'label' => $type->name,
                'count' => $resources->where('resource_type_id', $type->id)->count()
            ];
        })->filter(function($item) {
            return $item['count'] > 0; // Only show types with resources
        })->values();
        
        return view('resources.index', compact('resources', 'types', 'distributionData'));
    }

    /**
     * Store a newly created resource in storage.
     */
    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request): \Illuminate\Http\RedirectResponse
    {
        $validated = $request->validate([
            'name'             => 'required|string|max:255',
            'resource_type_id' => 'required|exists:resource_types,id',
            'description'      => 'nullable|string|max:1000',
            'cost'             => 'nullable|numeric|max:9999999.99',
            'file'             => 'nullable|file|mimes:pdf,doc,docx,xls,xlsx,ppt,pptx|max:10240',
        ]);

        $this->resourceService->createResource($validated, $request->file('file'));

        return back()->with('success', 'Recurso creado exitosamente.');
    }

    /**
     * Show the form for editing the specified resource.
     */
    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Resource $resource): \Illuminate\View\View
    {
        $types = \App\Models\ResourceType::all();
        return view('resources.edit', compact('resource', 'types'));
    }

    /**
     * Update the specified resource in storage.
     */
    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Resource $resource): \Illuminate\Http\RedirectResponse
    {
        $validated = $request->validate([
            'name'             => 'required|string|max:255',
            'resource_type_id' => 'required|exists:resource_types,id',
            'description'      => 'nullable|string|max:1000',
            'cost'             => 'nullable|numeric|max:9999999.99',
            'file'             => 'nullable|file|mimes:pdf,doc,docx,xls,xlsx,ppt,pptx|max:10240',
        ]);

        $this->resourceService->updateResource($resource, $validated, $request->file('file'));

        return redirect()->route('resources.index')->with('success', 'Recurso actualizado.');
    }

    /**
     * Remove the specified resource from storage.
     */
    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Resource $resource): \Illuminate\Http\RedirectResponse
    {
        $resource->delete();
        return redirect()->route('resources.index')->with('success', 'Recurso eliminado.');
    }

    /**
     * Assign a resource to a project.
     */
    /**
     * Assign a resource to a project.
     */
    public function assignToProject(Request $request, Project $project): \Illuminate\Http\RedirectResponse
    {
        $validated = $request->validate([
            'resource_id' => 'required|exists:resources,id',
            'quantity' => 'required|integer|min:1',
            'assigned_date' => 'required|date',
            'notes' => 'nullable|string',
        ]);

        $this->resourceService->assignToProject($project, $validated);

        return back()->with('success', 'Recurso asignado al proyecto.');
    }

    /**
     * Remove a resource from a project.
     */
    /**
     * Remove a resource from a project.
     */
    public function removeFromProject(Project $project, Resource $resource): \Illuminate\Http\RedirectResponse
    {
        $project->resources()->detach($resource->id);
        return back()->with('success', 'Recurso eliminado del proyecto.');
    }

    /**
     * Download the specified resource file.
     */
    /**
     * Download the specified resource file.
     */
    public function download(Resource $resource): \Symfony\Component\HttpFoundation\StreamedResponse|\Symfony\Component\HttpFoundation\BinaryFileResponse|\Illuminate\Http\RedirectResponse
    {
        if (!$resource->file_path || !\Illuminate\Support\Facades\Storage::disk('public')->exists($resource->file_path)) {
            return back()->with('error', 'El archivo no existe.');
        }

        $extension = pathinfo($resource->file_path, PATHINFO_EXTENSION);
        $filename = \Illuminate\Support\Str::finish($resource->name, '.' . $extension);
        
        return \Illuminate\Support\Facades\Storage::disk('public')->download($resource->file_path, $filename);
    }
}
