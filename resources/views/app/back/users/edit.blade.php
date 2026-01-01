@extends('layouts.admin')

@section('title', 'Editar Usuario')

@section('contenido')
<div class="container-fluid">
    <!-- Page Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h5 class="mb-0 text-dark"><i class="fas fa-user-edit me-2 text-warning"></i>Editar Usuario</h5>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb mb-0 mt-1" style="font-size: 0.8rem;">
                    <li class="breadcrumb-item"><a href="{{ route('users.index') }}" class="text-decoration-none">Usuarios</a></li>
                    <li class="breadcrumb-item active">Editar</li>
                </ol>
            </nav>
        </div>
        <a href="{{ route('users.index') }}" class="btn btn-outline-secondary btn-sm">
            <i class="fas fa-arrow-left me-1"></i> Volver
        </a>
    </div>

    <div class="row justify-content-center">
        <div class="col-lg-6">
            <!-- User Info Card -->
            <div class="card mb-4">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="rounded-circle d-flex align-items-center justify-content-center text-white me-3" 
                             style="width: 50px; height: 50px; background: var(--primary-gradient); font-size: 1.25rem; font-weight: 600;">
                            {{ strtoupper(substr($user->name, 0, 1)) }}
                        </div>
                        <div>
                            <h6 class="mb-0 text-dark">{{ $user->name }}</h6>
                            <small class="text-muted">{{ $user->email }}</small>
                        </div>
                    </div>
                </div>
            </div>

            <div class="card">
                <div class="card-header py-3">
                    <h6 class="m-0 fw-bold text-primary">Información del Usuario</h6>
                </div>
                <div class="card-body">
                    <form action="{{ route('users.update', $user) }}" method="POST">
                        @csrf
                        @method('PUT')

                        <div class="mb-3">
                            <label class="form-label small fw-bold text-muted">Nombre Completo</label>
                            <input type="text" 
                                   class="form-control @error('name') is-invalid @enderror" 
                                   name="name" 
                                   value="{{ old('name', $user->name) }}">
                            @error('name')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label class="form-label small fw-bold text-muted">Correo Electrónico</label>
                            <input type="email" 
                                   class="form-control @error('email') is-invalid @enderror" 
                                   name="email" 
                                   value="{{ old('email', $user->email) }}">
                            @error('email')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label class="form-label small fw-bold text-muted">Rol</label>
                            @if($user->id === auth()->id())
                                <div class="alert alert-warning py-2 small mb-2">
                                    <i class="fas fa-exclamation-triangle me-1"></i>
                                    No puedes cambiar tu propio rol
                                </div>
                                <input type="hidden" name="role" value="{{ $user->roles->first()->name ?? 'docente' }}">
                            @endif
                            <select name="role" class="form-select @error('role') is-invalid @enderror" 
                                    {{ $user->id === auth()->id() ? 'disabled' : '' }}>
                                @foreach($roles as $role)
                                    <option value="{{ $role->name }}" {{ $user->hasRole($role->name) ? 'selected' : '' }}>
                                        {{ ucfirst($role->name) }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <hr class="my-4">
                        <h6 class="text-muted mb-3"><i class="fas fa-key me-1"></i> Cambiar Contraseña <small>(opcional)</small></h6>

                        <div class="mb-3">
                            <label class="form-label small fw-bold text-muted">Nueva Contraseña</label>
                            <input type="password" 
                                   class="form-control @error('password') is-invalid @enderror" 
                                   name="password" 
                                   placeholder="Dejar en blanco para mantener actual">
                            @error('password')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-4">
                            <label class="form-label small fw-bold text-muted">Confirmar Nueva Contraseña</label>
                            <input type="password" 
                                   class="form-control" 
                                   name="password_confirmation" 
                                   placeholder="Repite la nueva contraseña">
                        </div>

                        <div class="d-flex gap-2">
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-save me-1"></i> Guardar Cambios
                            </button>
                            <a href="{{ route('users.index') }}" class="btn btn-light">
                                Cancelar
                            </a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
