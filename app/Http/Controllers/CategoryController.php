<?php

namespace App\Http\Controllers;

use App\Models\Category;
use Illuminate\Http\Request;

class CategoryController extends Controller
{
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255|unique:categories,name',
            'color' => 'nullable|string|max:7', // Hex code like #ff0000
            'description' => 'nullable|string'
        ]);

        // Default gray if no color provided
        if(empty($validated['color'])) {
            $validated['color'] = '#6c757d'; 
        }

        $category = Category::create($validated);

        if($request->wantsJson()) {
            return response()->json([
                'success' => true,
                'category' => $category,
                'message' => 'Categoría creada exitosamente'
            ]);
        }

        return back()->with('success', 'Categoría creada exitosamente');
    }

    public function destroy(Category $category)
    {
        // Only admins can delete
        if (!auth()->user()->hasRole('admin')) {
            return response()->json(['error' => 'No autorizado'], 403);
        }

        // Check if category is in use
        if ($category->projects()->count() > 0) {
            return response()->json([
                'success' => false,
                'message' => 'No se puede eliminar esta categoría porque tiene proyectos asociados.'
            ], 400);
        }

        $category->delete();

        return response()->json([
            'success' => true,
            'message' => 'Categoría eliminada correctamente.'
        ]);
    }
}
