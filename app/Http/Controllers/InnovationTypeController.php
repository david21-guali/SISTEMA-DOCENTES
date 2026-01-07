<?php

namespace App\Http\Controllers;

use App\Models\InnovationType;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class InnovationTypeController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(): \Illuminate\View\View
    {
        $types = InnovationType::withCount('innovations')->get();
        return view('app.back.innovation_types.index', compact('types'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request): \Illuminate\Http\JsonResponse
    {
        if (!auth()->user()->hasRole('admin') && !auth()->user()->hasRole('docente')) {
            return response()->json(['error' => 'No autorizado'], 403);
        }
        
        $request->validate([
            'name' => 'required|string|max:50|unique:innovation_types,name',
            'description' => 'nullable|string|max:255',
        ], [], [
            'name' => 'nombre',
            'description' => 'descripción',
        ]);

        $type = InnovationType::create([
            'name' => $request->name,
            'slug' => Str::slug($request->name),
            'description' => $request->description,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Tipo de innovación creado correctamente.',
            'type' => $type
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, InnovationType $innovationType): \Illuminate\Http\JsonResponse
    {
        if (!auth()->user()->hasRole('admin')) {
            return response()->json(['error' => 'No autorizado'], 403);
        }

        $request->validate([
            'name' => 'required|string|max:50|unique:innovation_types,name,' . $innovationType->id,
            'description' => 'nullable|string|max:255',
        ], [], [
            'name' => 'nombre',
            'description' => 'descripción',
        ]);

        $innovationType->update([
            'name' => $request->name,
            'slug' => Str::slug($request->name),
            'description' => $request->description,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Tipo de innovación actualizado correctamente.',
            'type' => $innovationType
        ]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(InnovationType $innovationType): \Illuminate\Http\JsonResponse
    {
        if (!auth()->user()->hasRole('admin')) {
            return response()->json(['error' => 'No autorizado'], 403);
        }

        if ($innovationType->innovations()->count() > 0) {
            return response()->json([
                'success' => false,
                'message' => 'No se puede eliminar este tipo porque tiene innovaciones asociadas.'
            ], 400);
        }

        $innovationType->delete();

        return response()->json([
            'success' => true,
            'message' => 'Tipo de innovación eliminado correctamente.'
        ]);
    }
}
