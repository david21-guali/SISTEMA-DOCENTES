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
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $user = Auth::user();
        if (!$user->profile) {
            // Fallback or error if no profile
             $user->profile()->create();
             $user->refresh();
        }
        $profileId = $user->profile->id;

        if ($user->hasRole(['admin', 'coordinador'])) {
            // Admin y Coordinador ven todos los proyectos
            $projects = Project::with(['category', 'profile', 'team'])
                ->latest()
                ->get();
                
            $allProjects = Project::all();
        } else {
            // Docente: solo proyectos donde es miembro del equipo o creador
            $projects = Project::with(['category', 'profile', 'team'])
                ->where(function($query) use ($profileId) {
                    $query->whereHas('team', fn($q) => $q->where('profiles.id', $profileId))
                          ->orWhere('profile_id', $profileId);
                })
                ->latest()
                ->get();
                
            $allProjects = $projects;
        }

        // Calculate Stats
        $stats = [
            'total' => $allProjects->count(),
            'finalizado' => $allProjects->where('status', 'finalizado')->count(),
            'en_progreso' => $allProjects->where('status', 'en_progreso')->count(),
            'planificacion' => $allProjects->where('status', 'planificacion')->count(),
            'en_riesgo' => $allProjects->filter(fn($p) => $p->is_actually_at_risk)->count(),
        ];

        return view('app.back.projects.index', compact('projects', 'stats'));
    }

    /**
     * Show the form for creating a new resource.
     */
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
    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'objectives' => 'required|string',
            'category_id' => 'required|exists:categories,id',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after:start_date',
            'budget' => 'nullable|numeric|min:0|max:9999999.99',
            'impact_description' => 'required|string',
            'team_members' => 'required|array|min:1',
            'team_members.*' => 'exists:users,id',
        ], [
            'title.required' => 'El título del proyecto es obligatorio.',
            'title.max' => 'El título no puede exceder 255 caracteres.',
            'description.required' => 'La descripción del proyecto es obligatoria.',
            'objectives.required' => 'Los objetivos del proyecto son obligatorios.',
            'category_id.required' => 'Debes seleccionar una categoría.',
            'category_id.exists' => 'La categoría seleccionada no es válida.',
            'start_date.required' => 'La fecha de inicio es obligatoria.',
            'start_date.date' => 'La fecha de inicio debe ser una fecha válida.',
            'end_date.required' => 'La fecha de fin es obligatoria.',
            'end_date.date' => 'La fecha de fin debe ser una fecha válida.',
            'end_date.after' => 'La fecha de fin debe ser posterior a la fecha de inicio.',
            'budget.numeric' => 'El presupuesto debe ser un número.',
            'budget.min' => 'El presupuesto no puede ser negativo.',
            'budget.max' => 'El presupuesto no puede exceder $9,999,999.99.',
            'impact_description.required' => 'La descripción del impacto esperado es obligatoria.',
            'team_members.required' => 'Debes seleccionar al menos un miembro del equipo.',
            'team_members.min' => 'Debes seleccionar al menos un miembro del equipo.',
            'team_members.*.exists' => 'Uno o más miembros seleccionados no son válidos.',
        ]);

        $validated['profile_id'] = Auth::user()->profile->id;
        $validated['status'] = 'planificacion';

        // Remove team_members from validated array before creation
        $teamUserIds = $validated['team_members'] ?? [];
        unset($validated['team_members']);

        // Convert User IDs to Profile IDs
        $teamProfileIds = \App\Models\User::whereIn('id', $teamUserIds)
            ->with('profile')
            ->get()
            ->pluck('profile.id')
            ->toArray();

        // Ensure creator is in the team? Usually yes, but depends on logic.
        // Let's stick to syncing what was selected.
        
        $project = Project::create($validated);

        // Sync team members (profiles)
        if (!empty($teamProfileIds)) {
            $project->team()->sync($teamProfileIds);
            
            // Notify team members (via User)
            // Need to get Users back to notify them
             $teamUsers = \App\Models\User::whereIn('id', $teamUserIds)->get();
             foreach ($teamUsers as $member) {
                if ($member->id !== Auth::id()) {
                    $member->notify(new \App\Notifications\ProjectAssigned($project));
                }
            }
        }

        // Handle file attachments
        if ($request->hasFile('attachments')) {
            foreach ($request->file('attachments') as $file) {
                $path = $file->store('attachments/projects/' . $project->id, 'public');
                
                $project->attachments()->create([
                    'filename' => basename($path),
                    'original_name' => $file->getClientOriginalName(),
                    'mime_type' => $file->getMimeType(),
                    'size' => $file->getSize(),
                    'path' => $path,
                    'uploaded_by' => Auth::user()->profile->id,
                ]);
            }
        }

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
    public function update(Request $request, Project $project)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'objectives' => 'nullable|string',
            'category_id' => 'required|exists:categories,id',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after:start_date',
            'status' => 'required|in:planificacion,en_progreso,finalizado,en_riesgo',
            'budget' => 'nullable|numeric|min:0|max:9999999.99',
            'impact_description' => 'nullable|string',
            'completion_percentage' => 'nullable|integer|min:0|max:100',
            'team_members' => 'required|array|min:1',
            'team_members.*' => 'exists:users,id',
        ]);

        $teamUserIds = $validated['team_members'] ?? [];
        unset($validated['team_members']);

        $oldStatus = $project->status;
        $project->update($validated);
        
        // Convert User IDs to Profile IDs
        $teamProfileIds = \App\Models\User::whereIn('id', $teamUserIds)
            ->with('profile')
            ->get()
            ->pluck('profile.id')
            ->toArray();

        // Sync team members and get changes
        $changes = $project->team()->sync($teamProfileIds);
        
        // Notify ONLY newly attached members
        // Changes returns detached/attached profile IDs
        if (!empty($changes['attached'])) {
            // Find users for these profiles
            $newMemberUsers = \App\Models\User::whereHas('profile', fn($q) => $q->whereIn('id', $changes['attached']))->get();
            
            foreach ($newMemberUsers as $member) {
                if ($member->id !== Auth::id()) {
                    $member->notify(new \App\Notifications\ProjectAssigned($project));
                }
            }
        }

        $newStatus = $project->status;

        if ($oldStatus !== $newStatus) {
            // Notify the project owner (via User relation on Profile)
            /** @var \App\Models\User $user */
            $user = $project->profile->user;
            $user->notify(new \App\Notifications\ProjectStatusChanged($project, $oldStatus, $newStatus));
        }

        return redirect()->route('projects.index')
            ->with('success', 'Proyecto actualizado exitosamente.');
    }

    /**
     * Store final report as a resource.
     */
    public function uploadFinalReport(Request $request, Project $project)
    {
        // Solo el responsable o admin pueden subir el informe final
        if (Auth::user()->profile->id !== $project->profile_id && !Auth::user()->hasRole('admin')) {
            abort(403, 'No tienes permiso para subir el informe de este proyecto.');
        }

        $request->validate([
            'file' => 'required|file|mimes:pdf|max:10240',
        ], [], [
            'file' => 'archivo',
        ]);

        if ($request->hasFile('file')) {
            $file = $request->file('file');
            $path = $file->store('reports/final', 'public');

            // Asegurar que existe el tipo 'digital' (slug o name)
            // La vista busca por ->where('type_slug', 'digital'), así que el slu/name debe coincidir
            $type = ResourceType::where('slug', 'digital')
                ->orWhere('name', 'digital')
                ->first();
            
            if (!$type) {
                $type = ResourceType::create([
                    'name' => 'Digital',
                    'slug' => 'digital',
                    'description' => 'Documentos y recursos digitales'
                ]);
            }

            // Crear el recurso
            $resource = Resource::create([
                'name' => 'Informe Final - ' . $project->title,
                'resource_type_id' => $type->id,
                'description' => 'Informe Final de Resultados',
                'file_path' => $path,
            ]);

            // Vincular al proyecto
            $project->resources()->attach($resource->id, [
                'quantity' => 1,
                'assigned_date' => now(),
                'notes' => 'Informe Final',
            ]);

            return back()->with('success', 'Informe final subido correctamente.');
        }

        return back()->with('error', 'No se pudo subir el archivo.');
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
