<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Role;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules;

class UserController extends Controller
{
    /**
     * Display a listing of users.
     */
    public function index(Request $request)
    {
        $query = User::with('roles');

        // Filtro por rol
        if ($request->role) {
            $query->whereHas('roles', function ($q) use ($request) {
                $q->where('name', $request->role);
            });
        }

        // Búsqueda por nombre o email
        if ($request->search) {
            $query->where(function ($q) use ($request) {
                $q->where('name', 'like', '%' . $request->search . '%')
                  ->orWhere('email', 'like', '%' . $request->search . '%');
            });
        }

        $users = $query->orderBy('name')->paginate(15);
        $roles = Role::all();

        $stats = [
            'total' => User::count(),
            'admins' => User::whereHas('roles', fn($q) => $q->where('name', 'admin'))->count(),
            'coordinadores' => User::whereHas('roles', fn($q) => $q->where('name', 'coordinador'))->count(),
            'docentes' => User::whereHas('roles', fn($q) => $q->where('name', 'docente'))->count(),
        ];

        return view('app.back.users.index', compact('users', 'roles', 'stats'));
    }

    /**
     * Show the form for creating a new user.
     */
    public function create()
    {
        $roles = Role::all();
        return view('app.back.users.create', compact('roles'));
    }

    /**
     * Store a newly created user.
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users'],
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
            'role' => ['required', 'exists:roles,name'],
        ]);

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
        ]);

        $user->assignRole($request->role);

        return redirect()->route('users.index')
            ->with('success', 'Usuario creado exitosamente.');
    }

    /**
     * Display the specified user.
     */
    public function show(User $user)
    {
        $user->load(['roles', 'profile.projects', 'profile.assignedProjects', 'profile.assignedTasks', 'profile.innovations']);
        
        $stats = [
            'projects' => $user->profile ? $user->profile->projects()->count() : 0,
            'tasks' => $user->profile ? $user->profile->assignedTasks()->count() : 0,
            'completed_tasks' => $user->profile ? $user->profile->assignedTasks()->where('status', 'completada')->count() : 0,
            'innovations' => $user->profile ? $user->profile->innovations()->count() : 0,
        ];

        return view('app.back.users.show', compact('user', 'stats'));
    }

    /**
     * Show the form for editing a user.
     */
    public function edit(User $user)
    {
        $roles = Role::all();
        return view('app.back.users.edit', compact('user', 'roles'));
    }

    /**
     * Update the specified user.
     */
    public function update(Request $request, User $user)
    {
        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users,email,' . $user->id],
            'password' => ['nullable', 'confirmed', Rules\Password::defaults()],
            'role' => ['required', 'exists:roles,name'],
        ]);

        $user->update([
            'name' => $request->name,
            'email' => $request->email,
        ]);

        if ($request->filled('password')) {
            $user->update(['password' => Hash::make($request->password)]);
        }

        // Sincronizar rol (quitar todos y asignar el nuevo)
        $user->syncRoles([$request->role]);

        return redirect()->route('users.index')
            ->with('success', 'Usuario actualizado exitosamente.');
    }

    /**
     * Remove the specified user.
     */
    public function destroy(Request $request, User $user)
    {
        // Validar contraseña del administrador
        $request->validate([
            'admin_password' => ['required', 'string'],
        ]);

        if (!Hash::check($request->admin_password, auth()->user()->password)) {
            return back()->with('error', 'La contraseña de confirmación es incorrecta. No se ha realizado ninguna acción.');
        }

        // Evitar eliminar el propio usuario
        if ($user->id === auth()->id()) {
            return back()->with('error', 'No puedes eliminar tu propia cuenta.');
        }

        // Evitar eliminar el último admin
        if ($user->hasRole('admin') && User::whereHas('roles', fn($q) => $q->where('name', 'admin'))->count() <= 1) {
            return back()->with('error', 'No puedes eliminar el último administrador.');
        }

        $user->delete();

        return redirect()->route('users.index')
            ->with('success', 'Usuario eliminado exitosamente.');
    }

    /**
     * Update user role via AJAX.
     */
    public function updateRole(Request $request, User $user)
    {
        $request->validate([
            'role' => ['required', 'exists:roles,name'],
        ]);

        // Evitar cambiar el rol del propio usuario
        if ($user->id === auth()->id()) {
            return response()->json(['error' => 'No puedes cambiar tu propio rol.'], 403);
        }

        // Evitar quitar el último admin
        if ($user->hasRole('admin') && $request->role !== 'admin' && User::whereHas('roles', fn($q) => $q->where('name', 'admin'))->count() <= 1) {
            return response()->json(['error' => 'No puedes quitar el único administrador.'], 403);
        }

        $user->syncRoles([$request->role]);

        return response()->json(['success' => true, 'message' => 'Rol actualizado.']);
    }
    /**
     * Reset user password by admin.
     */
    public function manualPasswordReset(Request $request, User $user)
    {
        $request->validate([
            'password' => ['required', 'string', 'min:8'],
        ]);

        // Evitar cambiar la propia contraseña por aquí (usar perfil)
        if ($user->id === auth()->id()) {
            return back()->with('error', 'Para cambiar tu propia contraseña usa tu perfil.');
        }

        $user->update([
            'password' => Hash::make($request->password),
        ]);

        // Notificar al usuario
        $user->notify(new \App\Notifications\PasswordResetByAdmin($request->password));

        return back()->with('success', 'Contraseña actualizada. Se ha enviado un correo al usuario con la nueva clave.');
    }
}
