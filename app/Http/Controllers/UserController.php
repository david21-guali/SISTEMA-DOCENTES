<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Role;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules;

class UserController extends Controller
{
    protected \App\Services\UserQueryService $queryService;
    protected \App\Services\UserActionService $actionService;
    protected \App\Services\UserManagementService $mgmtService;

    public function __construct(
        \App\Services\UserQueryService $queryService,
        \App\Services\UserActionService $actionService,
        \App\Services\UserManagementService $mgmtService
    ) {
        $this->queryService = $queryService;
        $this->actionService = $actionService;
        $this->mgmtService = $mgmtService;
    }

    /**
     * Display a listing of users.
     */
    /**
     * Display a listing of users.
     */
    public function index(Request $request): \Illuminate\View\View
    {
        $users = $this->queryService->getUsers($request->all());
        $roles = $this->queryService->getRoles();
        $stats = $this->queryService->getUserStats();

        return view('app.back.users.index', compact('users', 'roles', 'stats'));
    }

    /**
     * Show the form for creating a new user.
     */
    /**
     * Show the form for creating a new user.
     */
    public function create(): \Illuminate\View\View
    {
        $roles = $this->queryService->getRoles();
        return view('app.back.users.create', compact('roles'));
    }

    /**
     * Store a newly created user.
     */
    /**
     * Store a newly created user.
     */
    public function store(\App\Http\Requests\StoreUserRequest $request): \Illuminate\Http\RedirectResponse
    {
        $this->actionService->createUser($request->validated());
        return redirect()->route('users.index')->with('success', 'Usuario creado exitosamente.');
    }

    /**
     * Display the specified user.
     */
    /**
     * Display the specified user.
     */
    public function show(User $user): \Illuminate\View\View
    {
        $user->load(['roles', 'profile.projects', 'profile.assignedProjects', 'profile.assignedTasks', 'profile.innovations']);
        $stats = $this->queryService->getUserProfileStats($user);

        return view('app.back.users.show', compact('user', 'stats'));
    }

    /**
     * Show the form for editing a user.
     */
    /**
     * Show the form for editing a user.
     */
    public function edit(User $user): \Illuminate\View\View
    {
        $roles = $this->queryService->getRoles();
        return view('app.back.users.edit', compact('user', 'roles'));
    }

    /**
     * Update the specified user.
     */
    /**
     * Update the specified user.
     */
    public function update(\App\Http\Requests\UpdateUserRequest $request, User $user): \Illuminate\Http\RedirectResponse
    {
        $this->actionService->updateUser($user, $request->validated());
        return redirect()->route('users.index')->with('success', 'Usuario actualizado exitosamente.');
    }

    /**
     * Remove the specified user.
     */
    /**
     * Remove the specified user.
     */
    public function destroy(Request $request, User $user): \Illuminate\Http\RedirectResponse
    {
        $request->validate(['admin_password' => ['required', 'string']]);
        $result = $this->actionService->deleteUser($user, $request->admin_password);

        if ($result !== true) return back()->with('error', $result);

        return redirect()->route('users.index')->with('success', 'Usuario eliminado.');
    }

    /**
     * Update user role via AJAX.
     */
    /**
     * Update user role via AJAX.
     */
    public function updateRole(Request $request, User $user): \Illuminate\Http\JsonResponse
    {
        $request->validate(['role' => ['required', 'exists:roles,name']]);
        $result = $this->mgmtService->updateRole($user, $request->role);

        if ($result !== true) return response()->json(['error' => $result], 403);

        return response()->json(['success' => true, 'message' => 'Rol actualizado.']);
    }

    /**
     * Reset user password by admin.
     */
    /**
     * Reset user password by admin.
     */
    public function manualPasswordReset(Request $request, User $user): \Illuminate\Http\RedirectResponse
    {
        $request->validate(['password' => ['required', 'string', 'min:8']]);
        $result = $this->mgmtService->resetPassword($user, $request->password);

        if ($result !== true) return back()->with('error', $result);

        return back()->with('success', 'Contraseña actualizada y correo enviado.');
    }
}
