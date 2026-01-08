<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class TemporaryUploadController extends Controller
{
    /**
     * Store a temporary file.
     */
    public function store(Request $request): \Illuminate\Http\JsonResponse
    {
        $request->validate([
            'file' => 'required|file|max:10240', // 10MB
        ]);

        if ($request->hasFile('file')) {
            $file = $request->file('file');
            $filename = $file->getClientOriginalName();
            $extension = $file->getClientOriginalExtension();
            $tempName = Str::uuid() . '.' . $extension;
            
            $path = $file->storeAs('temp', $tempName, 'public');

            return response()->json([
                'success' => true,
                'path' => $path,
                'url' => asset('storage/' . $path),
                'name' => $filename,
                'id' => $tempName, // Use UUID as ID
            ]);
        }

        return response()->json(['success' => false, 'message' => 'No se subió ningún archivo.'], 400);
    }

    /**
     * Delete a temporary file.
     */
    public function destroy(Request $request): \Illuminate\Http\JsonResponse
    {
        $path = $request->input('path');
        
        if ($path && str_starts_with($path, 'temp/')) {
            if (Storage::disk('public')->exists($path)) {
                Storage::disk('public')->delete($path);
                return response()->json(['success' => true]);
            }
        }

        return response()->json(['success' => false, 'message' => 'Archivo no encontrado.'], 404);
    }
}
