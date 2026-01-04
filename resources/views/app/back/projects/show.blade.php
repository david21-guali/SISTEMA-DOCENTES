@extends('layouts.admin')

@section('title', 'Detalle del Proyecto')

@section('contenido')
<style>
    /* FIX URGENTE: Desplazar contenido a la derecha porque el sidebar lo tapa */
    @media (min-width: 768px) {
        .content-fix-sidebar {
            margin-left: 260px !important;
            width: calc(100% - 270px) !important;
        }
    }
</style>
<div class="content-fix-sidebar">
<div class="container-fluid">
    @if(config('app.debug'))
        <div class="alert alert-info">
            <small>Debug: Margen corregido {{ date('H:i:s') }}</small>
        </div>
    @endif
    <!-- Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2><i class="fas fa-folder-open"></i> {{ $project->title }}</h2>
        <div>
            @can('edit-project', $project)
            <a href="{{ route('projects.edit', $project) }}" class="btn btn-warning">
                <i class="fas fa-edit"></i> Editar
            </a>
            @endcan
            @can('delete-project', $project)
            <form action="{{ route('projects.destroy', $project) }}" method="POST" class="d-inline form-delete">
                @csrf
                @method('DELETE')
                <button type="submit" class="btn btn-danger">
                    <i class="fas fa-trash"></i> Eliminar
                </button>
            </form>
            @endcan
            <a href="{{ route('projects.index') }}" class="btn btn-secondary">
                <i class="fas fa-arrow-left"></i> Volver
            </a>
        </div>
    </div>

    <div class="row">
        <!-- Información del Proyecto -->
        <div class="col-md-8">
            <div class="card shadow mb-4">
                <div class="p-3 rounded-top bg-primary d-flex align-items-center justify-content-between" style="background-color: #4e73df !important;">
                    <span class="fs-5 fw-bold text-white" style="color: white !important;">Información General</span>
                </div>
                <div class="card-body">
                    <div class="mb-3">
                        <h6 class="text-muted">Descripción</h6>
                        <p>{{ $project->description }}</p>
                    </div>

                    @if($project->objectives)
                    <div class="mb-3">
                        <h6 class="text-muted">Objetivos</h6>
                        <p>{{ $project->objectives }}</p>
                    </div>
                    @endif

                    @if($project->impact_description)
                    <div class="mb-3">
                        <h6 class="text-muted">Impacto Esperado</h6>
                        <p>{{ $project->impact_description }}</p>
                    </div>
                    @endif
                </div>
            </div>

            <!-- Evaluaciones del Proyecto -->
            @php
                $evaluations = $project->evaluations;
            @endphp
            <div class="card shadow mb-4">
                <div class="p-3 rounded-top bg-warning d-flex align-items-center justify-content-between" style="background-color: #f6c23e !important;">
                    <span class="fs-5 fw-bold text-dark" style="color: #343a40 !important;">Evaluaciones</span>
                    <div class="d-flex align-items-center">
                    @can('evaluate-projects')
                    @if($project->status == 'finalizado')
                    <a href="{{ route('evaluations.create', $project) }}" class="btn btn-sm btn-success">
                        <i class="fas fa-plus"></i> Nueva Evaluación
                    </a>
                    @else
                    <span class="badge bg-secondary">Solo proyectos finalizados</span>
                    @endif
                    @endcan
                    </div>
                </div>
                <div class="card-body">
                    @if($evaluations->count() > 0)
                        @foreach($evaluations as $evaluation)
                        <div class="card mb-3 border-{{ $evaluation->score_color }}">
                            <div class="card-body">
                                <div class="d-flex justify-content-between align-items-start mb-3">
                                    <div>
                                        <h6 class="mb-1">Evaluación de {{ $evaluation->evaluator->name }}</h6>
                                        <small class="text-muted">{{ $evaluation->created_at->format('d/m/Y H:i') }}</small>
                                        @if($evaluation->status == 'borrador')
                                            <span class="badge bg-secondary ms-2">Borrador</span>
                                        @endif
                                    </div>
                                    @if($evaluation->final_score)
                                    <h3 class="text-{{ $evaluation->score_color }}">{{ $evaluation->final_score }}/10</h3>
                                    @endif
                                </div>

                                <!-- Puntos Clave en una sola línea -->
                                <div class="d-flex align-items-center flex-wrap gap-2 mb-2">
                                    @if($evaluation->average_rubric_score)
                                    <div class="d-flex align-items-center">
                                        <small class="fw-bold me-1">Rúbrica:</small>
                                        <div class="progress" style="height: 6px; width: 60px;">
                                            <div class="progress-bar bg-{{ $evaluation->score_color }}" style="width: {{ $evaluation->average_rubric_score * 20 }}%"></div>
                                        </div>
                                        <small class="ms-1 fw-bold text-{{ $evaluation->score_color }}">{{ $evaluation->average_rubric_score }}/5</small>
                                    </div>
                                    @endif

                                    <div class="vr mx-1"></div>

                                    <div class="d-flex flex-wrap gap-1">
                                        @if($evaluation->innovation_score)<span class="badge bg-light text-{{ $evaluation->getIndividualScoreClass($evaluation->innovation_score) }} border badge-xs">Inn:{{ $evaluation->innovation_score }}</span>@endif
                                        @if($evaluation->relevance_score)<span class="badge bg-light text-{{ $evaluation->getIndividualScoreClass($evaluation->relevance_score) }} border badge-xs">Per:{{ $evaluation->relevance_score }}</span>@endif
                                        @if($evaluation->results_score)<span class="badge bg-light text-{{ $evaluation->getIndividualScoreClass($evaluation->results_score) }} border badge-xs">Res:{{ $evaluation->results_score }}</span>@endif
                                        @if($evaluation->impact_score)<span class="badge bg-light text-{{ $evaluation->getIndividualScoreClass($evaluation->impact_score) }} border badge-xs">Imp:{{ $evaluation->impact_score }}</span>@endif
                                        @if($evaluation->methodology_score)<span class="badge bg-light text-{{ $evaluation->getIndividualScoreClass($evaluation->methodology_score) }} border badge-xs">Met:{{ $evaluation->methodology_score }}</span>@endif
                                    </div>
                                </div>

                                <style>
                                    .badge-xs { font-size: 0.65rem; padding: 2px 4px; }
                                    .btn-xs-slim { 
                                        padding: 4px 10px !important; 
                                        font-size: 0.8rem !important; 
                                        line-height: 1.2 !important;
                                        display: inline-flex;
                                        align-items: center;
                                        gap: 4px;
                                        height: 30px;
                                    }
                                </style>

                                <!-- Comentarios Resumidos (mas discreto) -->
                                @if($evaluation->general_comments)
                                <div class="mb-2 p-2 bg-light border-start border-3 rounded shadow-sm">
                                    <p class="mb-0 small font-italic" style="font-size: 0.8rem;">"{{ Str::limit($evaluation->general_comments, 120) }}"</p>
                                </div>
                                @endif

                                <!-- Informe PDF -->
                                <div class="mb-2 d-flex gap-2">
                                    @if($evaluation->report_file)
                                    <a href="{{ Storage::url($evaluation->report_file) }}" target="_blank" class="btn btn-sm btn-outline-danger">
                                        <i class="fas fa-file-pdf"></i> Ver Archivo
                                    </a>
                                    @endif
                                </div>

                                <!-- Acciones -->
                                <div class="d-flex align-items-center gap-2">
                                    @if(Auth::user()->profile->id == $evaluation->evaluator_id || Auth::user()->hasRole('admin'))
                                    <a href="{{ route('evaluations.edit', $evaluation) }}" class="btn btn-warning btn-xs-slim shadow-sm">
                                        <i class="fas fa-edit fa-xs"></i> Editar
                                    </a>
                                    @endif
                                    @can('evaluate-projects')
                                    <form action="{{ route('evaluations.destroy', $evaluation) }}" method="POST" class="delete-evaluation-form m-0">
                                        @csrf
                                        @method('DELETE')
                                        <button type="button" class="btn btn-danger btn-xs-slim shadow-sm btn-delete-evaluation">
                                            <i class="fas fa-trash fa-xs"></i> Eliminar
                                        </button>
                                    </form>
                                    @endcan
                                </div>
                            </div>
                        </div>
                        @endforeach
                    @else
                    @endif
                </div>
            </div>

            <!-- Sección de Informe Final (Para el Docente) -->
            @if(Auth::user()->profile->id == $project->profile_id && $project->status == 'finalizado')
            <div class="card shadow mb-4 border-info">
                <div class="p-3 rounded-top bg-info d-flex align-items-center justify-content-between" style="background-color: #36b9cc !important;">
                    <span class="fs-5 fw-bold text-white" style="color: white !important;">Informe Final de Resultados</span>
                </div>
                <div class="card-body">
                    <p>Como responsable del proyecto, debes subir el informe final con evidencias antes de la evaluación.</p>
                    
                    <!-- Buscar si ya existe un recurso tipo 'informe_final' o similar. Usaremos 'digital' por ahora -->
                    @php
                        $finalReport = $project->resources->where('type_slug', 'digital')->sortByDesc('created_at')->first();
                    @endphp

                    @if($finalReport)
                        <div class="alert alert-success d-flex justify-content-between align-items-center">
                            <div>
                                <i class="fas fa-check-circle"></i> Informe subido: <strong>{{ $finalReport->name }}</strong>
                            </div>
                            <div class="d-flex gap-2">
                                <a href="{{ Storage::url($finalReport->file_path) }}" target="_blank" class="btn btn-sm btn-light text-dark">
                                    <i class="fas fa-download"></i> Descargar
                                </a>
                                <form action="{{ route('projects.resources.remove', [$project->id, $finalReport->id]) }}" method="POST" class="form-delete">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-sm btn-danger">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </form>
                            </div>
                        </div>
                    @endif

                    <button class="btn btn-outline-primary" data-bs-toggle="modal" data-bs-target="#uploadFinalReportModal">
                        <i class="fas fa-upload"></i> {{ $finalReport ? 'Subir Nueva Versión' : 'Subir Informe Final' }}
                    </button>
                </div>
            </div>
            @endif

            <!-- Tareas del Proyecto -->
            <div class="card shadow">
                <div class="p-3 rounded-top bg-success d-flex align-items-center justify-content-between" style="background-color: #1cc88a !important;">
                    <span class="fs-5 fw-bold text-white" style="color: white !important;">Tareas del Proyecto</span>
                    <span class="badge bg-light text-dark">{{ $project->tasks->count() }}</span>
                </div>
                <div class="card-body">
                    @if($project->tasks->count() > 0)
                        <div class="list-group">
                            @foreach($project->tasks as $task)
                            <div class="list-group-item">
                                <div class="d-flex justify-content-between align-items-center">
                                    <div>
                                        <h6 class="mb-1">{{ $task->title }}</h6>
                                        <small class="text-muted">
                                            <i class="fas fa-user"></i> {{ $task->assignedProfile->user->name ?? 'Sin asignar' }} |
                                            <i class="fas fa-calendar"></i> {{ $task->due_date->format('d/m/Y') }}
                                        </small>
                                    </div>
                                    <div class="d-flex align-items-center gap-2">
                                        <span class="badge bg-{{ $task->status == 'completada' ? 'success' : ($task->is_overdue ? 'danger' : 'warning') }} me-2">
                                            {{ ucfirst($task->status) }}
                                        </span>
                                        @if(Auth::user()->profile->id == $project->profile_id || Auth::user()->hasRole('admin'))
                                        <div class="btn-group btn-group-sm">
                                            <a href="{{ route('tasks.edit', $task) }}" class="btn btn-outline-secondary" title="Editar">
                                                <i class="fas fa-edit"></i>
                                            </a>
                                            <form action="{{ route('tasks.destroy', $task) }}" method="POST" class="d-inline form-delete">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="btn btn-outline-danger" title="Eliminar">
                                                    <i class="fas fa-trash"></i>
                                                </button>
                                            </form>
                                        </div>
                                        @endif
                                    </div>
                                </div>
                            </div>
                            @endforeach
                        </div>
                    @endif
                </div>
            </div>

            <!-- Recursos del Proyecto -->
            <div class="card shadow mt-4 mb-4">
                <div class="p-3 rounded-top bg-secondary d-flex align-items-center justify-content-between" style="background-color: #858796 !important;">
                    <span class="fs-5 fw-bold text-white" style="color: white !important;">Recursos Asignados</span>
                    <button type="button" class="btn btn-sm btn-light text-dark" data-bs-toggle="modal" data-bs-target="#assignResourceModal">
                        <i class="fas fa-plus"></i> Asignar Recurso
                    </button>
                </div>
                <div class="card-body">
                    @if($project->resources->count() > 0)
                    <div class="table-responsive">
                        <table class="table table-sm">
                            <thead>
                                <tr>
                                    <th>Recurso</th>
                                    <th>Tipo</th>
                                    <th>Cantidad</th>
                                    <th>Notas</th>
                                    <th>Acciones</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($project->resources as $resource)
                                <tr>
                                    <td>{{ $resource->name }}</td>
                                    <td><span class="badge bg-light text-dark border">{{ $resource->type->name ?? 'N/A' }}</span></td>
                                    <td>{{ $resource->pivot->quantity }}</td>
                                    <td>{{ $resource->pivot->notes }}</td>
                                    <td>
                                        <div class="d-flex gap-2">
                                            @if($resource->file_path)
                                            <a href="{{ route('resources.download', $resource) }}" class="btn btn-sm btn-link text-primary p-0" title="Descargar">
                                                <i class="fas fa-download"></i>
                                            </a>
                                            @endif
                                            
                                            @if(Auth::user()->hasRole('admin'))
                                            <a href="{{ route('resources.edit', $resource) }}" class="btn btn-sm btn-link text-warning p-0" title="Editar">
                                                <i class="fas fa-edit"></i>
                                            </a>
                                            @endif

                                            <form action="{{ route('projects.resources.remove', [$project->id, $resource->id]) }}" method="POST" class="d-inline form-delete">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="btn btn-sm btn-link text-danger p-0">
                                                    <i class="fas fa-times"></i>
                                                </button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                    @else
                        <p class="text-muted text-center mb-0">No hay recursos asignados a este proyecto.</p>
                    @endif
                </div>
            </div>

            <!-- Modal Assign Resource -->
            <div class="modal fade" id="assignResourceModal" tabindex="-1">
                <div class="modal-dialog">
                    <form action="{{ route('projects.resources.assign', $project->id) }}" method="POST">
                        @csrf
                        <div class="modal-content">
                            <div class="modal-header">
                                <h5 class="modal-title">Asignar Recurso al Proyecto</h5>
                                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                            </div>
                            <div class="modal-body">
                                <div class="mb-3">
                                    <label class="form-label">Recurso *</label>
                                    <select name="resource_id" class="form-select @error('resource_id') is-invalid @enderror">
                                        <option value="">Seleccione...</option>
                                        @foreach(\App\Models\Resource::with('type')->get() as $r)
                                            <option value="{{ $r->id }}" {{ old('resource_id') == $r->id ? 'selected' : '' }}>{{ $r->name }} ({{ $r->type->name ?? 'N/A' }})</option>
                                        @endforeach
                                    </select>
                                    @error('resource_id')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                                <div class="mb-3">
                                    <label class="form-label">Cantidad *</label>
                                    <input type="number" name="quantity" class="form-control @error('quantity') is-invalid @enderror" value="{{ old('quantity', 1) }}" min="1">
                                    @error('quantity')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                                <div class="mb-3">
                                    <label class="form-label">Fecha de Asignación *</label>
                                    <input type="date" name="assigned_date" class="form-control @error('assigned_date') is-invalid @enderror" value="{{ old('assigned_date', date('Y-m-d')) }}">
                                    @error('assigned_date')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                                <div class="mb-3">
                                    <label class="form-label">Notas</label>
                                    <input type="text" name="notes" class="form-control" placeholder="Ej: Uso exclusivo para fase 1">
                                </div>
                            </div>
                            <div class="modal-footer">
                                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                                <button type="submit" class="btn btn-primary">Asignar</button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>

            <!-- Archivos del Proyecto Separados (Implementación similar a Tareas) -->
            @php
                $projectOwnerId = $project->profile_id;
                $resources = $project->attachments->filter(function($att) use ($projectOwnerId) {
                    return $att->uploaded_by == $projectOwnerId || ($att->uploader && $att->uploader->user->hasRole('admin'));
                });
                $deliverables = $project->attachments->reject(function($att) use ($projectOwnerId) {
                    return $att->uploaded_by == $projectOwnerId || ($att->uploader && $att->uploader->user->hasRole('admin'));
                });
                
                $isOwnerOrAdmin = Auth::user()->profile->id == $projectOwnerId || Auth::user()->hasRole('admin');
            @endphp
            
            <div class="row">
                <!-- 1. Documentos e Información Oficial (Docente/Admin) -->
                <div class="col-lg-6">
                    <div class="card shadow mb-4">
                        <div class="card-header bg-primary text-white d-flex justify-content-between align-items-center">
                            <h5 class="mb-0"><i class="fas fa-file-contract"></i> Documentos Oficiales</h5>
                        </div>
                        <div class="card-body">
                            <p class="small text-muted mb-3">Documentación oficial del proyecto, guías y formatos subidos por el responsable.</p>
                            @if($isOwnerOrAdmin)
                                <div class="mb-3">
                                    <button class="btn btn-sm btn-outline-primary" type="button" data-bs-toggle="collapse" data-bs-target="#uploadOfficialDoc">
                                        <i class="fas fa-plus"></i> Agregar Documento
                                    </button>
                                    <div class="collapse mt-2" id="uploadOfficialDoc">
                                        <form action="{{ route('attachments.store', ['project', $project->id]) }}" method="POST" enctype="multipart/form-data" class="border p-3 rounded">
                                            @csrf
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
                                <div class="list-group list-group-flush">
                                    @foreach($resources as $attachment)
                                        <div class="list-group-item d-flex justify-content-between align-items-center px-0">
                                            <div class="d-flex align-items-center overflow-hidden">
                                                <div class="me-2 text-primary"><i class="{{ $attachment->icon }} fa-lg"></i></div>
                                                <div class="text-truncate">
                                                    <a href="{{ route('attachments.download', $attachment) }}" class="fw-bold text-dark text-decoration-none" target="_blank">
                                                        {{ $attachment->original_name }}
                                                    </a>
                                                    <div class="small text-muted">{{ $attachment->human_size }}</div>
                                                </div>
                                            </div>
                                            @if($isOwnerOrAdmin)
                                            <form action="{{ route('attachments.destroy', $attachment) }}" method="POST" class="ms-2 form-delete">
                                                @csrf @method('DELETE')
                                                <button type="submit" class="btn btn-sm text-danger"><i class="fas fa-times"></i></button>
                                            </form>
                                            @endif
                                        </div>
                                    @endforeach
                                </div>
                            @else
                                <div class="alert alert-light border text-center small">No hay documentos oficiales aún.</div>
                            @endif
                        </div>
                    </div>
                </div>

                <!-- 2. Archivos del Equipo (Colaboradores) -->
                <div class="col-lg-6">
                    <div class="card shadow mb-4">
                        <div class="card-header bg-success text-white d-flex justify-content-between align-items-center">
                            <h5 class="mb-0"><i class="fas fa-folder-open"></i> Archivos del Equipo</h5>
                        </div>
                        <div class="card-body">
                            <p class="small text-muted mb-3">Espacio compartido para archivos de trabajo del equipo.</p>
                            
                            <!-- Botón de subida para miembros del equipo -->
                            <div class="mb-3">
                                <button class="btn btn-sm btn-outline-success" type="button" data-bs-toggle="collapse" data-bs-target="#uploadTeamFile">
                                    <i class="fas fa-cloud-upload-alt"></i> Subir Archivo
                                </button>
                                <div class="collapse mt-2" id="uploadTeamFile">
                                    <form action="{{ route('attachments.store', ['project', $project->id]) }}" method="POST" enctype="multipart/form-data" class="border p-3 rounded bg-light">
                                        @csrf
                                        <input type="file" name="files[]" class="form-control mb-2 @error('files') is-invalid @enderror" multiple>
                                        @error('files')
                                            <div class="invalid-feedback d-block">{{ $message }}</div>
                                        @enderror
                                        <button type="submit" class="btn btn-success btn-sm">Subir al Espacio de Equipo</button>
                                    </form>
                                </div>
                            </div>

                            @if($deliverables->count() > 0)
                                <div class="list-group list-group-flush">
                                    @foreach($deliverables as $attachment)
                                        <div class="list-group-item d-flex justify-content-between align-items-center px-0">
                                            <div class="d-flex align-items-center overflow-hidden">
                                                <div class="avatar-xs rounded-circle bg-secondary text-white d-flex align-items-center justify-content-center me-2 flex-shrink-0" 
                                                     style="width:24px; height:24px; font-size:10px;" 
                                                     title="{{ $attachment->uploader->user->name ?? '?' }}">
                                                    {{ strtoupper(substr($attachment->uploader->user->name ?? '?', 0, 1)) }}
                                                </div>
                                                <div class="text-truncate">
                                                    <a href="{{ route('attachments.download', $attachment) }}" class="text-dark text-decoration-none" target="_blank">
                                                        {{ $attachment->original_name }}
                                                    </a>
                                                    <div class="small text-muted" style="font-size: 0.75rem;">
                                                        {{ $attachment->created_at->format('d/m H:i') }} - {{ Str::limit($attachment->uploader->user->name ?? '', 10) }}
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="d-flex align-items-center">
                                                @if(Auth::user()->profile->id == $attachment->uploaded_by || $isOwnerOrAdmin)
                                                <form action="{{ route('attachments.destroy', $attachment) }}" method="POST" class="ms-1 form-delete">
                                                    @csrf @method('DELETE')
                                                    <button type="submit" class="btn btn-sm text-danger"><i class="fas fa-times"></i></button>
                                                </form>
                                                @endif
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            @else
                                <div class="alert alert-light border text-center small">No hay archivos compartidos por el equipo.</div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>

            <!-- Modal Upload Final Report -->
            <div class="modal fade" id="uploadFinalReportModal" tabindex="-1">
                <div class="modal-dialog">
                    <form action="{{ route('projects.uploadReport', $project) }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        <div class="modal-content">
                            <div class="modal-header bg-info text-white">
                                <h5 class="modal-title">Subir Informe Final de Resultados</h5>
                                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                            </div>
                            <div class="modal-body">
                                <p class="text-muted small">Sube el documento PDF que detalla los hallazgos, impacto y evidencias del proyecto.</p>
                                <div class="mb-3">
                                    <label class="form-label">Archivo PDF *</label>
                                    <input type="file" name="file" class="form-control @error('file') is-invalid @enderror" accept=".pdf">
                                    @error('file')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            <div class="modal-footer">
                                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                                <button type="submit" class="btn btn-info text-white">
                                    <i class="fas fa-upload"></i> Subir Informe
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>

            <!-- Comentarios y Colaboración -->
            <div class="card shadow mt-4">
                <div class="card-header bg-info text-white">
                    <h5 class="mb-0"><i class="fas fa-comments"></i> Comentarios y Discusión</h5>
                </div>
                <div class="card-body">
                    <!-- Formulario de Nuevo Comentario -->
                    <form action="{{ route('comments.store', $project) }}" method="POST" class="mb-4">
                        @csrf
                        <div class="mb-2">
                            <textarea class="form-control @error('content') is-invalid @enderror" 
                                      name="content" rows="3" 
                                      placeholder="Escribe un comentario...">{{ old('content') }}</textarea>
                            @error('content')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <button type="submit" class="btn btn-info btn-sm">
                            <i class="fas fa-paper-plane"></i> Publicar Comentario
                        </button>
                    </form>

                    <!-- Lista de Comentarios -->
                    @php
                        $comments = $project->comments()->whereNull('parent_id')->with(['profile.user', 'replies.profile.user'])->get();
                    @endphp

                    @if($comments->count() > 0)
                        <div class="comments-list">
                            @foreach($comments as $comment)
                            <div class="card mb-3">
                                <div class="card-body">
                                    <div class="d-flex justify-content-between align-items-start mb-2">
                                        <div>
                                            <strong>{{ $comment->profile->user->name }}</strong>
                                            <small class="text-muted ms-2">{{ $comment->created_at->diffForHumans() }}</small>
                                        </div>
                                        @if(Auth::user()->profile->id == $comment->profile_id || Auth::user()->hasRole('admin'))
                                        <form action="{{ route('comments.destroy', $comment) }}" method="POST" class="delete-comment-form">
                                            @csrf
                                            @method('DELETE')
                                            <button type="button" class="btn btn-sm btn-link text-danger btn-delete-comment">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        </form>
                                        @endif
                                    </div>
                                    <p class="mb-0">{{ $comment->content }}</p>

                                    <!-- Respuestas -->
                                    @if($comment->replies->count() > 0)
                                    <div class="ms-4 mt-3 border-start border-3 border-info ps-3">
                                        @foreach($comment->replies as $reply)
                                        <div class="mb-2">
                                            <div class="d-flex justify-content-between align-items-start">
                                                <div>
                                                    <strong class="small">{{ $reply->profile->user->name }}</strong>
                                                    <small class="text-muted ms-1">{{ $reply->created_at->diffForHumans() }}</small>
                                                </div>
                                                @if(Auth::user()->profile->id == $reply->profile_id || Auth::user()->hasRole('admin'))
                                                <form action="{{ route('comments.destroy', $reply) }}" method="POST" class="delete-comment-form">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="button" class="btn btn-sm btn-link text-danger p-0 btn-delete-comment">
                                                        <i class="fas fa-trash"></i>
                                                    </button>
                                                </form>
                                                @endif
                                            </div>
                                            <p class="mb-0 small">{{ $reply->content }}</p>
                                        </div>
                                        @endforeach
                                    </div>
                                    @endif

                                    <!-- Formulario de Respuesta -->
                                    <div class="mt-2">
                                        <button class="btn btn-sm btn-outline-info" type="button" 
                                                data-bs-toggle="collapse" data-bs-target="#reply-{{ $comment->id }}">
                                            <i class="fas fa-reply"></i> Responder
                                        </button>
                                        <div class="collapse mt-2" id="reply-{{ $comment->id }}">
                                            <form action="{{ route('comments.store', $project) }}" method="POST">
                                                @csrf
                                                <input type="hidden" name="parent_id" value="{{ $comment->id }}">
                                                <div class="input-group input-group-sm">
                                                    <textarea class="form-control" name="content" rows="2" 
                                                              placeholder="Escribe una respuesta..."></textarea>
                                                    <button type="submit" class="btn btn-info">
                                                        <i class="fas fa-paper-plane"></i>
                                                    </button>
                                                </div>
                                            </form>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            @endforeach
                        </div>
                    @else
                        <div class="text-center py-3">
                            <i class="fas fa-comments fa-2x text-muted mb-2"></i>
                            <p class="text-muted mb-0">No hay comentarios aún. ¡Sé el primero en comentar!</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>

        <!-- Barra Lateral con Detalles -->
        <div class="col-md-4">
            <!-- Estado y Avance -->
            <div class="card shadow mb-4">
                <div class="card-body">
                    <h6 class="text-muted mb-3">Estado del Proyecto</h6>
                    <div class="mb-3">
                        <span class="badge bg-{{ $project->status_color }} fs-6">
                            {{ ucfirst(str_replace('_', ' ', $project->status)) }}
                        </span>
                    </div>

                    <h6 class="text-muted mb-2">Avance</h6>
                    <div class="progress mb-2" style="height: 25px;">
                        <div class="progress-bar" role="progressbar" 
                             style="width: {{ $project->completion_percentage ?? 0 }}%; min-width: 2em;">
                            {{ $project->completion_percentage ?? 0 }}%
                        </div>
                    </div>
                </div>
            </div>

            <!-- Detalles -->
            <div class="card shadow mb-4">
                <div class="card-body">
                    <h6 class="text-muted mb-3">Detalles</h6>
                    
                    <div class="mb-3">
                        <small class="text-muted d-block">Categoría</small>
                        <span class="badge" style="background-color: {{ $project->category->color }}">
                            <i class="fas {{ $project->category->icon }}"></i> {{ $project->category->name }}
                        </span>
                    </div>

                    <div class="mb-3">
                        <small class="text-muted d-block">Responsable</small>
                        <x-user-link :user="$project->profile->user" :showAvatar="true" />
                        <br>
                        <small>{{ $project->profile->department }}</small>
                    </div>

                    <div class="mb-3">
                        <small class="text-muted d-block">Fechas</small>
                        <i class="fas fa-calendar-start"></i> {{ $project->start_date->format('d/m/Y') }}<br>
                        <i class="fas fa-calendar-check"></i> {{ $project->end_date->format('d/m/Y') }}
                    </div>

                    @if($project->budget)
                    <div class="mb-3">
                        <small class="text-muted d-block">Presupuesto</small>
                        <strong>${{ number_format($project->budget, 2) }}</strong>
                    </div>
                    @endif

                    <div>
                        <small class="text-muted d-block">Creado</small>
                        {{ $project->created_at->format('d/m/Y H:i') }}
                    </div>
                </div>
            </div>
        </div>
    </div> <!-- End row -->
    </div> <!-- End container-fluid -->
</div> <!-- End content-fix-sidebar -->
@endsection

@section('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Handle evaluation deletion
        document.querySelectorAll('.btn-delete-evaluation').forEach(button => {
            button.addEventListener('click', function() {
                const form = this.closest('form');
                Swal.fire({
                    title: '¿Eliminar esta evaluación?',
                    text: "Esta acción no se puede deshacer.",
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#e74a3b',
                    cancelButtonColor: '#858796',
                    confirmButtonText: 'Sí, eliminar',
                    cancelButtonText: 'Cancelar'
                }).then((result) => {
                    if (result.isConfirmed) {
                        form.submit();
                    }
                });
            });
        });

        // Handle comment deletion
        document.querySelectorAll('.btn-delete-comment').forEach(button => {
            button.addEventListener('click', function() {
                const form = this.closest('form');
                Swal.fire({
                    title: '¿Eliminar comentario?',
                    text: "Esta acción no se puede deshacer.",
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#e74a3b',
                    cancelButtonColor: '#858796',
                    confirmButtonText: 'Sí, eliminar',
                    cancelButtonText: 'Cancelar'
                }).then((result) => {
                    if (result.isConfirmed) {
                        form.submit();
                    }
                });
            });
        });
    });
</script>
@endsection

