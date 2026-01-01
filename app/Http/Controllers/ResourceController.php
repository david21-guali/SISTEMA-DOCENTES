<?php

namespace App\Http\Controllers;

use App\Models\Resource;
use App\Models\Project;
use Illuminate\Http\Request;

class ResourceController extends Controller
{
    /**
     * Display a listing of the resource catalog.
     */
    public function index()
    {
        $resources = Resource::with('type')->get();
        $types = \App\Models\ResourceType::all();
        return view('resources.index', compact('resources', 'types'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'resource_type_id' => 'required|exists:resource_types,id',
            'cost' => 'nullable|numeric|max:9999999.99',
            'file' => 'nullable|file|mimes:pdf,doc,docx,xls,xlsx,ppt,pptx|max:10240', // 10MB max
        ], [], [
            'name' => 'nombre',
            'resource_type_id' => 'tipo de recurso',
            'cost' => 'costo',
            'file' => 'archivo',
        ]);

        $data = $request->all();

        if ($request->hasFile('file')) {
            $path = $request->file('file')->store('resources', 'public');
            $data['file_path'] = $path;
        }

        Resource::create($data);

        return back()->with('success', 'Recurso creado exitosamente.');
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Resource $resource)
    {
        $types = \App\Models\ResourceType::all();
        return view('resources.edit', compact('resource', 'types'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Resource $resource)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'resource_type_id' => 'required|exists:resource_types,id',
            'cost' => 'nullable|numeric|max:9999999.99',
            'file' => 'nullable|file|mimes:pdf,doc,docx,xls,xlsx,ppt,pptx|max:10240',
        ], [], [
            'name' => 'nombre',
            'resource_type_id' => 'tipo de recurso',
            'cost' => 'costo',
            'file' => 'archivo',
        ]);

        $data = $request->all();

        if ($request->hasFile('file')) {
            // Delete old file if exists
            if ($resource->file_path && \Illuminate\Support\Facades\Storage::disk('public')->exists($resource->file_path)) {
                \Illuminate\Support\Facades\Storage::disk('public')->delete($resource->file_path);
            }
            $path = $request->file('file')->store('resources', 'public');
            $data['file_path'] = $path;
        }

        $resource->update($data);

        return redirect()->route('resources.index')->with('success', 'Recurso actualizado.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Resource $resource)
    {
        $resource->delete();
        return redirect()->route('resources.index')->with('success', 'Recurso eliminado.');
    }

    /**
     * Assign a resource to a project.
     */
    public function assignToProject(Request $request, Project $project)
    {
        $request->validate([
            'resource_id' => 'required|exists:resources,id',
            'quantity' => 'required|integer|min:1',
            'assigned_date' => 'required|date',
            'notes' => 'nullable|string',
        ], [], [
            'resource_id' => 'recurso',
            'quantity' => 'cantidad',
            'assigned_date' => 'fecha de asignación',
            'notes' => 'notas',
        ]);

        $project->resources()->attach($request->resource_id, [
            'quantity' => $request->quantity,
            'assigned_date' => $request->assigned_date,
            'notes' => $request->notes,
        ]);

        return back()->with('success', 'Recurso asignado al proyecto correctamente.');
    }

    /**
     * Remove a resource from a project.
     */
    public function removeFromProject(Project $project, Resource $resource)
    {
        $project->resources()->detach($resource->id);
        return back()->with('success', 'Recurso eliminado del proyecto.');
    }

    /**
     * Download the specified resource file.
     */
    public function download(Resource $resource)
    {
        if (!$resource->file_path || !\Illuminate\Support\Facades\Storage::disk('public')->exists($resource->file_path)) {
            return back()->with('error', 'El archivo no existe o no pudo ser encontrado.');
        }

        return \Illuminate\Support\Facades\Storage::disk('public')->download($resource->file_path, $resource->name . '.' . pathinfo($resource->file_path, PATHINFO_EXTENSION));
    }
}
