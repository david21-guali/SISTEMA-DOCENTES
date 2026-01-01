@extends('layouts.admin')

@section('title', 'Gestión de Usuarios')

@section('contenido')
<div class="container-fluid">
    <!-- Page Header -->
    <div class="page-header-gradient d-flex flex-column flex-sm-row justify-content-between align-items-sm-center gap-3">
        <div>
            <h4 class="mb-1"><i class="fas fa-users-cog me-2"></i>Gestión de Usuarios</h4>
            <p class="mb-0 opacity-75" style="font-size: 0.9rem;">Administra los usuarios y sus roles en el sistema</p>
        </div>
        <a href="{{ route('users.create') }}" class="btn btn-light shadow-sm">
            <i class="fas fa-user-plus me-1"></i> Nuevo Usuario
        </a>
    </div>

    <!-- Stats Cards -->
    <div class="row g-3 mb-4">
        <div class="col-xl-3 col-md-6">
            <div class="card border-left-primary h-100">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <div class="text-xs text-uppercase fw-bold text-primary mb-1">Total Usuarios</div>
                            <div class="h4 mb-0 fw-bold text-dark">{{ $stats['total'] }}</div>
                        </div>
                        <div class="text-muted opacity-50">
                            <i class="fas fa-users fa-2x"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-xl-3 col-md-6">
            <div class="card border-left-danger h-100">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <div class="text-xs text-uppercase fw-bold text-danger mb-1">Administradores</div>
                            <div class="h4 mb-0 fw-bold text-dark">{{ $stats['admins'] }}</div>
                        </div>
                        <div class="text-muted opacity-50">
                            <i class="fas fa-user-shield fa-2x"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-xl-3 col-md-6">
            <div class="card border-left-warning h-100">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <div class="text-xs text-uppercase fw-bold text-warning mb-1">Coordinadores</div>
                            <div class="h4 mb-0 fw-bold text-dark">{{ $stats['coordinadores'] }}</div>
                        </div>
                        <div class="text-muted opacity-50">
                            <i class="fas fa-user-tie fa-2x"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-xl-3 col-md-6">
            <div class="card border-left-success h-100">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <div class="text-xs text-uppercase fw-bold text-success mb-1">Docentes</div>
                            <div class="h4 mb-0 fw-bold text-dark">{{ $stats['docentes'] }}</div>
                        </div>
                        <div class="text-muted opacity-50">
                            <i class="fas fa-chalkboard-teacher fa-2x"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Filters -->
    <div class="card mb-4">
        <div class="card-body">
            <form action="{{ route('users.index') }}" method="GET" class="row g-3 align-items-end">
                <div class="col-md-5">
                    <label class="form-label small text-muted fw-bold">
                        <i class="fas fa-search me-1"></i>Buscar
                    </label>
                    <input type="text" name="search" class="form-control" 
                           placeholder="Nombre o email..." value="{{ request('search') }}">
                </div>
                <div class="col-md-4">
                    <label class="form-label small text-muted fw-bold">
                        <i class="fas fa-filter me-1"></i>Filtrar por Rol
                    </label>
                    <select name="role" class="form-select">
                        <option value="">Todos los roles</option>
                        @foreach($roles as $role)
                            <option value="{{ $role->name }}" {{ request('role') == $role->name ? 'selected' : '' }}>
                                {{ ucfirst($role->name) }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-3">
                    <button type="submit" class="btn btn-primary w-100">
                        <i class="fas fa-search me-1"></i> Buscar
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- Users Table -->
    <div class="card">
        <div class="card-header py-3">
            <h6 class="m-0 fw-bold">Lista de Usuarios</h6>
        </div>
        <div class="card-body p-0">
            <!-- Desktop View -->
            <div class="table-responsive d-none d-md-block">
                <table class="table table-hover mb-0">
                    <thead>
                        <tr>
                            <th>Usuario</th>
                            <th>Email</th>
                            <th>Rol</th>
                            <th>Registrado</th>
                            <th class="text-center">Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($users as $user)
                            <tr>
                                <td>
                                    <div class="d-flex align-items-center">
                                        <div class="rounded-circle d-flex align-items-center justify-content-center text-white me-3" 
                                             style="width: 40px; height: 40px; background: var(--primary-gradient); font-weight: 600;">
                                            {{ strtoupper(substr($user->name, 0, 1)) }}
                                        </div>
                                        <div>
                                            <span class="fw-medium text-dark">{{ $user->name }}</span>
                                            @if($user->id === auth()->id())
                                                <span class="badge bg-info bg-opacity-10 text-info ms-2">Tú</span>
                                            @endif
                                        </div>
                                    </div>
                                </td>
                                <td class="text-muted">{{ $user->email }}</td>
                                <td>
                                    @php
                                        $role = $user->roles->first();
                                        $roleColors = [
                                            'admin' => 'danger',
                                            'coordinador' => 'warning',
                                            'docente' => 'success'
                                        ];
                                        $roleColor = $roleColors[$role?->name] ?? 'secondary';
                                    @endphp
                                    <span class="badge bg-{{ $roleColor }} bg-opacity-10 text-{{ $roleColor }}">
                                        {{ ucfirst($role?->name ?? 'Sin rol') }}
                                    </span>
                                </td>
                                <td class="text-muted">
                                    <small>{{ $user->created_at->translatedFormat('d M, Y') }}</small>
                                </td>
                                <td>
                                    <div class="d-flex justify-content-center gap-1">
                                        <a href="{{ route('users.show', $user) }}" class="btn btn-sm btn-outline-primary" title="Ver">
                                            <i class="fas fa-eye"></i>
                                        </a>
                                        <a href="{{ route('users.edit', $user) }}" class="btn btn-sm btn-outline-warning" title="Editar">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                        @if($user->id !== auth()->id())
                                            <button type="button" class="btn btn-sm btn-outline-info" title="Restablecer Contraseña" onclick="resetPassword({{ $user->id }}, '{{ $user->name }}')">
                                                <i class="fas fa-key"></i>
                                            </button>
                                            <form action="{{ route('users.destroy', $user) }}" method="POST" class="d-inline form-delete-user">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="btn btn-sm btn-outline-danger" title="Eliminar">
                                                    <i class="fas fa-trash"></i>
                                                </button>
                                            </form>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="text-center py-4">
                                    <i class="fas fa-users fa-3x text-muted mb-3 d-block"></i>
                                    <span class="text-muted">No se encontraron usuarios</span>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <!-- Mobile View -->
            <div class="d-md-none p-3">
                @forelse($users as $user)
                    <div class="card mb-3 shadow-sm border-0">
                        <div class="card-body p-3">
                            <div class="d-flex align-items-center mb-3">
                                <div class="rounded-circle d-flex align-items-center justify-content-center text-white me-3" 
                                     style="width: 45px; height: 45px; background: var(--primary-gradient); font-weight: 600; font-size: 1.2rem;">
                                    {{ strtoupper(substr($user->name, 0, 1)) }}
                                </div>
                                <div class="overflow-hidden">
                                    <h6 class="fw-bold mb-0 text-dark text-truncate">{{ $user->name }}</h6>
                                    <p class="text-muted small mb-0 text-truncate">{{ $user->email }}</p>
                                </div>
                            </div>
                            
                            <div class="d-flex justify-content-between align-items-center pt-2 border-top">
                                <div>
                                    @php
                                        $role = $user->roles->first();
                                        $roleColors = [
                                            'admin' => 'danger',
                                            'coordinador' => 'warning',
                                            'docente' => 'success'
                                        ];
                                        $roleColor = $roleColors[$role?->name] ?? 'secondary';
                                    @endphp
                                    <span class="badge bg-{{ $roleColor }} bg-opacity-10 text-{{ $roleColor }}">
                                        {{ ucfirst($role?->name ?? 'Sin rol') }}
                                    </span>
                                    @if($user->id === auth()->id())
                                        <span class="badge bg-info bg-opacity-10 text-info">Tú</span>
                                    @endif
                                </div>
                                <div class="dropdown">
                                    <button class="btn btn-light btn-sm rounded-pill px-3" data-bs-toggle="dropdown">
                                        Acciones <i class="fas fa-chevron-down ms-1" style="font-size: 0.6rem;"></i>
                                    </button>
                                    <ul class="dropdown-menu dropdown-menu-end shadow border-0">
                                        <li><a class="dropdown-item" href="{{ route('users.show', $user) }}"><i class="fas fa-eye me-2 text-primary"></i>Ver</a></li>
                                        <li><a class="dropdown-item" href="{{ route('users.edit', $user) }}"><i class="fas fa-edit me-2 text-warning"></i>Editar</a></li>
                                        @if($user->id !== auth()->id())
                                            <li><button type="button" class="dropdown-item" onclick="resetPassword({{ $user->id }}, '{{ $user->name }}')"><i class="fas fa-key me-2 text-info"></i>Clave</button></li>
                                            <li><hr class="dropdown-divider"></li>
                                            <li>
                                                <form action="{{ route('users.destroy', $user) }}" method="POST" class="form-delete-user">
                                                    @csrf @method('DELETE')
                                                    <button type="submit" class="dropdown-item text-danger"><i class="fas fa-trash me-2"></i>Eliminar</button>
                                                </form>
                                            </li>
                                        @endif
                                    </ul>
                                </div>
                            </div>
                        </div>
                    </div>
                @empty
                    <div class="text-center py-4">
                        <i class="fas fa-users fa-3x text-muted mb-3 d-block"></i>
                        <span class="text-muted">No se encontraron usuarios</span>
                    </div>
                @endforelse
            </div>
        </div>
    </div>

    <!-- Pagination -->
    <div class="d-flex justify-content-center mt-4">
        {{ $users->withQueryString()->links() }}
    </div>
</div>

@push('scripts')
<script>
    function resetPassword(userId, userName) {
        Swal.fire({
            title: 'Restablecer Contraseña',
            text: "Ingresa la nueva contraseña para el usuario " + userName,
            input: 'text',
            inputPlaceholder: 'Nueva contraseña...',
            showCancelButton: true,
            confirmButtonText: 'Restablecer y Notificar',
            cancelButtonText: 'Cancelar',
            confirmButtonColor: '#4e73df',
            inputValidator: (value) => {
                if (!value) {
                    return 'Debes escribir una contraseña'
                }
                if (value.length < 8) {
                    return 'La contraseña debe tener al menos 8 caracteres'
                }
            }
        }).then((result) => {
            if (result.isConfirmed) {
                const form = document.createElement('form');
                form.method = 'POST';
                form.action = `/users/${userId}/manual-reset`;
                
                const csrfToken = document.createElement('input');
                csrfToken.type = 'hidden';
                csrfToken.name = '_token';
                csrfToken.value = '{{ csrf_token() }}';
                form.appendChild(csrfToken);
                
                const passwordInput = document.createElement('input');
                passwordInput.type = 'hidden';
                passwordInput.name = 'password';
                passwordInput.value = result.value;
                form.appendChild(passwordInput);
                
                document.body.appendChild(form);
                form.submit();
            }
        });
    }

    // Confirmation for User Delete Buttons with Password (Special Case)
    document.querySelectorAll('.form-delete-user').forEach(form => {
        form.addEventListener('submit', function(e) {
            e.preventDefault();
            const currentForm = this;
            
            Swal.fire({
                title: 'Confirmar Eliminación',
                text: "Esta acción es irreversible. Por seguridad, ingresa TU contraseña de administrador para continuar:",
                icon: 'warning',
                input: 'password',
                inputAttributes: {
                    autocapitalize: 'off',
                    autocorrect: 'off'
                },
                inputPlaceholder: 'Tu contraseña actual...',
                showCancelButton: true,
                confirmButtonColor: '#d33',
                cancelButtonColor: '#3085d6',
                confirmButtonText: 'Sí, eliminar permanentemente',
                cancelButtonText: 'Cancelar',
                inputValidator: (value) => {
                    if (!value) {
                        return 'Debes ingresar tu contraseña para confirmar'
                    }
                }
            }).then((result) => {
                if (result.isConfirmed) {
                    // Añadir la contraseña como campo oculto al formulario
                    const passwordInput = document.createElement('input');
                    passwordInput.type = 'hidden';
                    passwordInput.name = 'admin_password';
                    passwordInput.value = result.value;
                    currentForm.appendChild(passwordInput);
                    
                    currentForm.submit();
                }
            });
        });
    });
</script>
@endpush

@push('styles')
<style>
    .text-xs { font-size: 0.7rem; }
    .border-left-primary { border-left: 4px solid var(--primary-color) !important; }
    .border-left-success { border-left: 4px solid #1cc88a !important; }
    .border-left-warning { border-left: 4px solid #f6c23e !important; }
    .border-left-danger { border-left: 4px solid #e74a3b !important; }
</style>
@endpush
@endsection
