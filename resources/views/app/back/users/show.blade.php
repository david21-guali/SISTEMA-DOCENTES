@extends('layouts.admin')

@section('title', $user->name)

@section('contenido')
<div class="container-fluid">
    <!-- Page Header -->
    <div class="d-flex flex-column flex-sm-row justify-content-between align-items-sm-center gap-3 mb-4">
        <div>
            <h5 class="mb-0 text-dark fw-bold"><i class="fas fa-user me-2 text-primary"></i>Perfil de Usuario</h5>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb mb-0 mt-1" style="font-size: 0.8rem;">
                    <li class="breadcrumb-item"><a href="{{ route('users.index') }}" class="text-decoration-none">Usuarios</a></li>
                    <li class="breadcrumb-item active">{{ $user->name }}</li>
                </ol>
            </nav>
        </div>
        <a href="{{ route('users.index') }}" class="btn btn-outline-secondary btn-sm align-self-start align-self-sm-center">
            <i class="fas fa-arrow-left me-1"></i> Volver
        </a>
    </div>

    <div class="row">
        <!-- Profile Card -->
        <div class="col-lg-4 mb-4">
            <div class="card text-center">
                <div class="card-body pt-4">
                    <div class="rounded-circle d-flex align-items-center justify-content-center text-white mx-auto mb-3" 
                         style="width: 80px; height: 80px; background: var(--primary-gradient); font-size: 2rem; font-weight: 600;">
                        {{ strtoupper(substr($user->name, 0, 1)) }}
                    </div>
                    <h5 class="text-dark mb-1">{{ $user->name }}</h5>
                    <p class="text-muted mb-2">{{ $user->email }}</p>
                    
                    @php
                        $role = $user->roles->first();
                        $roleColors = [
                            'admin' => 'danger',
                            'coordinador' => 'warning',
                            'docente' => 'success'
                        ];
                        $roleColor = $roleColors[$role?->name] ?? 'secondary';
                    @endphp
                    <span class="badge bg-{{ $roleColor }} mb-3">{{ ucfirst($role?->name ?? 'Sin rol') }}</span>
                    
                    <hr>
                    
                    <div class="text-start">
                        <p class="mb-2 small">
                            <i class="fas fa-calendar text-muted me-2" style="width: 16px;"></i>
                            <span class="text-muted">Registrado:</span> 
                            <strong>{{ $user->created_at->translatedFormat('d M, Y') }}</strong>
                        </p>
                    </div>

                    @if($user->id !== auth()->id())
                        <hr>
                        <div class="d-grid gap-2">
                            <a href="{{ route('chat.show', $user) }}" class="btn btn-info btn-sm text-white">
                                <i class="fas fa-envelope me-1"></i> Enviar Mensaje
                            </a>
                            
                            @if(auth()->user()->hasRole('admin'))
                            <a href="{{ route('users.edit', $user) }}" class="btn btn-primary btn-sm">
                                <i class="fas fa-edit me-1"></i> Editar
                            </a>
                            @endif
                        </div>
                    @endif
                </div>
            </div>
        </div>

        <!-- Stats & Projects -->
        <div class="col-lg-8">
            <!-- Stats -->
            <div class="row g-3 mb-4">
                <div class="col-6 col-md-3">
                    <div class="card h-100 text-center">
                        <div class="card-body py-3">
                            <div class="h4 mb-0 text-primary fw-bold">{{ $stats['projects'] }}</div>
                            <small class="text-muted">Proyectos</small>
                        </div>
                    </div>
                </div>
                <div class="col-6 col-md-3">
                    <div class="card h-100 text-center">
                        <div class="card-body py-3">
                            <div class="h4 mb-0 text-warning fw-bold">{{ $stats['tasks'] }}</div>
                            <small class="text-muted">Tareas</small>
                        </div>
                    </div>
                </div>
                <div class="col-6 col-md-3">
                    <div class="card h-100 text-center">
                        <div class="card-body py-3">
                            <div class="h4 mb-0 text-success fw-bold">{{ $stats['completed_tasks'] }}</div>
                            <small class="text-muted">Completadas</small>
                        </div>
                    </div>
                </div>
                <div class="col-6 col-md-3">
                    <div class="card h-100 text-center">
                        <div class="card-body py-3">
                            <div class="h4 mb-0 text-info fw-bold">{{ $stats['innovations'] }}</div>
                            <small class="text-muted">Innovaciones</small>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Projects -->
            <div class="card">
                <div class="card-header py-3">
                    <h6 class="m-0 fw-bold text-primary">
                        <i class="fas fa-folder-open me-1"></i> Proyectos Recientes
                    </h6>
                </div>
                <div class="card-body p-0">
                    @php
                        $userProjects = $user->profile ? $user->profile->projects->take(5) : collect();
                    @endphp
                    @forelse($userProjects as $project)
                        <a href="{{ route('projects.show', $project) }}" class="d-flex justify-content-between align-items-center px-3 py-3 border-bottom text-decoration-none">
                            <div class="pe-3 overflow-hidden">
                                <span class="text-dark fw-medium d-block text-truncate" style="max-width: 200px;" title="{{ $project->title }}">{{ $project->title }}</span>
                                <small class="d-block text-muted text-truncate">{{ $project->category->name ?? 'Sin categoría' }}</small>
                            </div>
                            <span class="badge bg-{{ $project->status_color ?? 'secondary' }} bg-opacity-10 text-{{ $project->status_color ?? 'secondary' }}">
                                {{ ucfirst(str_replace('_', ' ', $project->status)) }}
                            </span>
                        </a>
                    @empty
                        <div class="text-center py-4">
                            <i class="fas fa-folder-open fa-2x text-muted mb-2 d-block"></i>
                            <span class="text-muted">No tiene proyectos</span>
                        </div>
                    @endforelse
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
