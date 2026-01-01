@extends('layouts.admin')

@section('title', 'Detalle de Tarea')

@section('contenido')
<div class="container-fluid">
    <div class="row justify-content-center">
        <div class="col-lg-8">
            <!-- Header -->
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h2><i class="fas fa-tasks"></i> Detalle de Tarea</h2>
                <div>
                    @if($task->status !== 'completada')
                    <form action="{{ route('tasks.complete', $task) }}" method="POST" class="d-inline">
                        @csrf
                        @method('PATCH')
                        <button type="submit" class="btn btn-success">
                            <i class="fas fa-check"></i> Marcar Completada
                        </button>
                    </form>
                    @endif
                    @can('edit-tasks')
                    <a href="{{ route('tasks.edit', $task) }}" class="btn btn-warning">
                        <i class="fas fa-edit"></i> Editar
                    </a>
                    @endcan
                    <a href="{{ route('tasks.index') }}" class="btn btn-secondary">
                        <i class="fas fa-arrow-left"></i> Volver
                    </a>
                </div>
            </div>

            <!-- Tarjeta Principal -->
            <div class="card shadow mb-4 border-start border-5 border-{{ $task->priority_color }}">
                <div class="card-body">
                    <h3>{{ $task->title }}</h3>
                    
                    <div class="mb-3">
                        <span class="badge bg-{{ $task->status == 'completada' ? 'success' : ($task->is_overdue ? 'danger' : 'warning') }} fs-6">
                            {{ ucfirst($task->status) }}
                        </span>
                        <span class="badge bg-{{ $task->priority_color }} fs-6">
                            Prioridad: {{ ucfirst($task->priority) }}
                        </span>
                    </div>

                    @if($task->description)
                    <div class="mb-4">
                        <h6 class="text-muted">Descripción</h6>
                        <p>{{ $task->description }}</p>
                    </div>
                    @endif

                    <hr>

                    <div class="row">
                        <!-- Proyecto -->
                        <div class="col-md-6 mb-3">
                            <h6 class="text-muted mb-2">
                                <i class="fas fa-project-diagram"></i> Proyecto
                            </h6>
                            <a href="{{ route('projects.show', $task->project) }}" class="text-decoration-none">
                                <strong>{{ $task->project->title }}</strong>
                            </a>
                            <br>
                            <span class="badge" style="background-color: {{ $task->project->category->color }}">
                                {{ $task->project->category->name }}
                            </span>
                        </div>

                        <!-- Usuario Asignado -->
                        <div class="col-md-6 mb-3">
                            <h6 class="text-muted mb-2">
                                <i class="fas fa-user"></i> Asignado a
                            </h6>
                            @if($task->assignedProfile)
                                <strong>{{ $task->assignedProfile->user->name }}</strong><br>
                                <small class="text-muted">{{ $task->assignedProfile->department }}</small><br>
                                <small class="text-muted">{{ $task->assignedProfile->user->email }}</small>
                            @else
                                <span class="text-muted">Sin asignar</span>
                            @endif
                        </div>

                        <!-- Fecha Límite -->
                        <div class="col-md-6 mb-3">
                            <h6 class="text-muted mb-2">
                                <i class="fas fa-calendar"></i> Fecha Límite
                            </h6>
                            <strong class="{{ $task->is_overdue && $task->status !== 'completada' ? 'text-danger' : '' }}">
                                {{ $task->due_date->format('d/m/Y') }}
                            </strong>
                            @if($task->is_overdue && $task->status !== 'completada')
                                <br><span class="badge bg-danger">Tarea Atrasada</span>
                            @endif
                        </div>

                        <!-- Fecha de Completado -->
                        @if($task->completion_date)
                        <div class="col-md-6 mb-3">
                            <h6 class="text-muted mb-2">
                                <i class="fas fa-check-circle"></i> Fecha de Completado
                            </h6>
                            <strong>{{ $task->completion_date->format('d/m/Y H:i') }}</strong>
                        </div>
                        @endif

                        <!-- Fechas de Auditoría -->
                        <div class="col-md-6 mb-3">
                            <h6 class="text-muted mb-2">
                                <i class="fas fa-clock"></i> Creada
                            </h6>
                            <small>{{ $task->created_at->format('d/m/Y H:i') }}</small>
                        </div>

                        <div class="col-md-6 mb-3">
                            <h6 class="text-muted mb-2">
                                <i class="fas fa-history"></i> Última Actualización
                            </h6>
                            <small>{{ $task->updated_at->format('d/m/Y H:i') }}</small>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Archivos Adjuntos Separados -->
            @php
                $projectOwnerId = $task->project->profile_id;
                $resources = $task->attachments->filter(function($att) use ($projectOwnerId) {
                    return $att->uploaded_by == $projectOwnerId || ($att->uploader && $att->uploader->user->hasRole('admin'));
                });
                $deliverables = $task->attachments->reject(function($att) use ($projectOwnerId) {
                    return $att->uploaded_by == $projectOwnerId || ($att->uploader && $att->uploader->user->hasRole('admin'));
                });
                
                $isOwnerOrAdmin = Auth::user()->profile->id == $projectOwnerId || Auth::user()->hasRole('admin');
            @endphp

            <!-- 1. RECURSOS DE LA TAREA (Visible para todos, editable por docente) -->
            <div class="card shadow mb-4">
                <div class="card-header bg-primary text-white d-flex justify-content-between align-items-center">
                    <h5 class="mb-0"><i class="fas fa-book-open"></i> Recursos e Instrucciones</h5>
                </div>
                <div class="card-body">
                    @if($isOwnerOrAdmin)
                        <div class="mb-3">
                            <button class="btn btn-sm btn-outline-primary" type="button" data-bs-toggle="collapse" data-bs-target="#uploadResourceForm">
                                <i class="fas fa-plus"></i> Agregar Recurso
                            </button>
                            <div class="collapse mt-2" id="uploadResourceForm">
                                <form action="{{ route('attachments.store', ['task', $task->id]) }}" method="POST" enctype="multipart/form-data" class="border p-3 rounded">
                                    @csrf
                                    <label class="small fw-bold mb-2">Subir archivo de recurso:</label>
                                    <input type="file" name="files[]" class="form-control mb-2 @error('files') is-invalid @enderror" multiple>
                                    @error('files')
                                        <div class="invalid-feedback d-block">{{ $message }}</div>
                                    @enderror
                                    <button type="submit" class="btn btn-primary btn-sm">Subir</button>
                                </form>
                            </div>
                        </div>
                    @endif

                    @if($resources->count() > 0)
                        <div class="row g-2">
                            @foreach($resources as $attachment)
                                <div class="col-md-6">
                                    <div class="d-flex align-items-center p-2 border rounded bg-light">
                                        <div class="me-3">
                                            <i class="{{ $attachment->icon }} fa-2x"></i>
                                        </div>
                                        <div class="flex-grow-1 text-truncate">
                                            <a href="{{ route('attachments.download', $attachment) }}" class="fw-bold text-dark text-decoration-none" target="_blank">
                                                {{ $attachment->original_name }}
                                            </a>
                                            <div class="small text-muted">{{ $attachment->human_size }}</div>
                                        </div>
                                        @if($isOwnerOrAdmin)
                                            <form action="{{ route('attachments.destroy', $attachment) }}" method="POST" class="ms-2 form-delete">
                                                @csrf @method('DELETE')
                                                <button type="submit" class="btn btn-link text-danger p-0"><i class="fas fa-times"></i></button>
                                            </form>
                                        @endif
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @else
                        <p class="text-muted small fst-italic mb-0">No hay recursos adjuntos.</p>
                    @endif
                </div>
            </div>

            <!-- 2. ENTREGAS Y EVIDENCIAS (Subido por estudiantes/asignados) -->
            <div class="card shadow mb-4">
                <div class="card-header bg-success text-white d-flex justify-content-between align-items-center">
                    <h5 class="mb-0"><i class="fas fa-upload"></i> Entregas / Evidencias</h5>
                </div>
                <div class="card-body">
                    <!-- Formulario de Entrega (Para asignados) -->
                    @if(!$isOwnerOrAdmin || $task->assignedProfile->id == Auth::user()->profile->id) 
                        <div class="mb-4 text-center p-4 border border-2 border-dashed rounded bg-light">
                            <form action="{{ route('attachments.store', ['task', $task->id]) }}" method="POST" enctype="multipart/form-data">
                                @csrf
                                <i class="fas fa-cloud-upload-alt fa-3x text-muted mb-3"></i>
                                <h6 class="fw-bold">Subir Entrega</h6>
                                <p class="small text-muted mb-3">Sube aquí tus archivos de evidencia o resultados.</p>
                                <div class="d-flex justify-content-center gap-2">
                                    <input type="file" name="files[]" class="form-control w-auto @error('files') is-invalid @enderror" multiple>
                                    @error('files')
                                        <div class="invalid-feedback d-block">{{ $message }}</div>
                                    @enderror
                                    <button type="submit" class="btn btn-success"><i class="fas fa-paper-plane"></i> Enviar</button>
                                </div>
                            </form>
                        </div>
                    @endif

                    <h6 class="fw-bold mb-3 border-bottom pb-2">Archivos Entregados</h6>
                    
                    @if($deliverables->count() > 0)
                        <div class="list-group">
                            @foreach($deliverables as $attachment)
                                <div class="list-group-item d-flex justify-content-between align-items-center">
                                    <div class="d-flex align-items-center">
                                        <div class="avatar-sm rounded-circle bg-secondary text-white d-flex align-items-center justify-content-center me-3" style="width:32px; height:32px;">
                                            {{ strtoupper(substr($attachment->uploader->user->name ?? '?', 0, 1)) }}
                                        </div>
                                        <div>
                                            <a href="{{ route('attachments.download', $attachment) }}" class="fw-bold text-dark text-decoration-none">
                                                {{ $attachment->original_name }}
                                            </a>
                                            <div class="small text-muted">
                                                Por: {{ $attachment->uploader->user->name ?? 'Desconocido' }} | {{ $attachment->created_at->format('d/m/Y H:i') }}
                                            </div>
                                        </div>
                                    </div>
                                    <div class="d-flex align-items-center">
                                        <span class="badge bg-light text-dark border me-3">{{ $attachment->human_size }}</span>
                                        @if(Auth::user()->profile->id == $attachment->uploaded_by || $isOwnerOrAdmin)
                                            <form action="{{ route('attachments.destroy', $attachment) }}" method="POST" class="form-delete">
                                                @csrf @method('DELETE')
                                                <button type="submit" class="btn btn-sm btn-outline-danger"><i class="fas fa-trash-alt"></i></button>
                                            </form>
                                        @endif
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @else
                        <div class="alert alert-light text-center">
                            Aún no hay entregas registradas.
                        </div>
                    @endif
                </div>
            </div>

            <!-- Acciones -->
            @can('delete-tasks')
            <div class="card shadow border-danger">
                <div class="card-body">
                    <h6 class="text-danger"><i class="fas fa-exclamation-triangle"></i> Zona Peligrosa</h6>
                    <p class="text-muted mb-2">Una vez eliminada, esta tarea no se puede recuperar.</p>
                    <form action="{{ route('tasks.destroy', $task) }}" method="POST" class="form-delete">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn btn-danger">
                            <i class="fas fa-trash"></i> Eliminar Tarea
                        </button>
                    </form>
                </div>
            </div>
            @endcan
        </div>
    </div>
</div>
@endsection
