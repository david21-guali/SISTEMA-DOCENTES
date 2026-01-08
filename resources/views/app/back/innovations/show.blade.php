@extends('layouts.admin')

@section('title', 'Detalle de Innovación')

@section('contenido')
<div class="container-fluid">
    <div class="row justify-content-center">
        <div class="col-lg-10">
            <!-- Header -->
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h2><i class="fas fa-lightbulb"></i> {{ $innovation->title }}</h2>
                <div>
                    @if($innovation->status !== 'aprobada' && $innovation->status !== 'en_revision')
                        {{-- Solo el autor puede solicitar revisión --}}
                        @if(auth()->user()->profile->id === $innovation->profile_id)
                            <form action="{{ route('innovations.request-review', $innovation) }}" method="POST" class="d-inline">
                                @csrf
                                <button type="submit" class="btn btn-info text-white me-2">
                                    <i class="fas fa-paper-plane"></i> Solicitar Revisión
                                </button>
                            </form>
                        @endif
                    @endif

                    @can('update', $innovation)
                    <a href="{{ route('innovations.edit', $innovation) }}" class="btn btn-warning">
                        <i class="fas fa-edit"></i> Editar
                    </a>
                    @endcan
                    <a href="{{ route('innovations.index') }}" class="btn btn-secondary">
                        <i class="fas fa-arrow-left"></i> Volver
                    </a>
                </div>
            </div>

            <div class="row">
                <!-- Información Principal -->
                <div class="col-md-8">
                    <div class="card shadow mb-4">
                        <div class="card-header bg-{{ $innovation->status_color }} text-white">
                            <div class="d-flex justify-content-between align-items-center">
                                <h5 class="mb-0">Información General</h5>
                                <span class="badge bg-white text-dark">
                                    {{ ucfirst(str_replace('_', ' ', $innovation->status)) }}
                                </span>
                            </div>
                        </div>
                        <div class="card-body">
                            <div class="mb-4">
                                <h6 class="text-muted">Descripción</h6>
                                <p>{{ $innovation->description }}</p>
                            </div>

                            @if($innovation->methodology)
                            <div class="mb-4">
                                <h6 class="text-muted">Metodología</h6>
                                <p>{{ $innovation->methodology }}</p>
                            </div>
                            @endif

                            <div class="row">
                                @if($innovation->expected_results)
                                <div class="col-md-6 mb-3">
                                    <h6 class="text-muted">Resultados Esperados</h6>
                                    <p>{{ $innovation->expected_results }}</p>
                                </div>
                                @endif

                                @if($innovation->actual_results)
                                <div class="col-md-6 mb-3">
                                    <h6 class="text-muted">Resultados Obtenidos</h6>
                                    <p>{{ $innovation->actual_results }}</p>
                                </div>
                                @endif
                            </div>
                        </div>
                    </div>

                    <!-- Archivos de Evidencia -->
                    <!-- Archivos de Evidencia -->
                    @if($innovation->attachments->count() > 0)
                    <div class="card shadow mb-4">
                        <div class="card-header bg-primary text-white d-flex justify-content-between align-items-center">
                            <h5 class="mb-0">
                                <i class="fas fa-file-contract"></i> Archivos de Evidencia
                                <span class="badge bg-light text-dark ms-2">{{ $innovation->attachments->count() }}</span>
                            </h5>
                        </div>
                        <div class="card-body">
                            <div class="list-group list-group-flush mt-2">
                                @foreach($innovation->attachments as $file)
                                <div class="list-group-item d-flex justify-content-between align-items-center px-0">
                                    <div class="d-flex align-items-center overflow-hidden">
                                        @php
                                            $canPreview = $file->isImage() || $file->isPdf();
                                            $previewType = $file->isImage() ? 'image' : ($file->isPdf() ? 'pdf' : 'other');
                                        @endphp
                                        <div class="me-2 clickable-thumbnail" style="cursor: pointer;"
                                             @if($canPreview) onclick="openGlobalPreview('{{ route('storage.preview', $file->path) }}', '{{ $file->original_name }}', '{{ $previewType }}')" @endif>
                                            <i class="{{ $file->icon }} fa-lg"></i>
                                        </div>
                                        <div class="text-truncate">
                                            <span class="fw-bold text-dark d-block text-truncate" title="{{ $file->original_name }}">
                                                {{ $file->original_name }}
                                            </span>
                                            <div class="small text-muted">{{ $file->human_size }} • {{ $file->created_at->format('d/m/Y') }}</div>
                                        </div>
                                    </div>
                                    
                                    <div class="d-flex align-items-center gap-1">
                                        @if($canPreview)
                                        <button type="button" class="btn btn-sm btn-outline-primary" 
                                                onclick="openGlobalPreview('{{ route('storage.preview', $file->path) }}', '{{ $file->original_name }}', '{{ $previewType }}')"
                                                title="Vista Previa">
                                            <i class="fas fa-eye"></i>
                                        </button>
                                        @endif
                                        
                                        <a href="{{ route('attachments.download', $file) }}" class="btn btn-sm btn-outline-secondary" title="Descargar">
                                            <i class="fas fa-download"></i>
                                        </a>

                                        @if(Auth::user()->profile->id == $innovation->profile_id || Auth::user()->hasRole('admin'))
                                        <form action="{{ route('attachments.destroy', $file) }}" method="POST" class="ms-1 form-delete">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-sm text-danger" title="Eliminar">
                                                <i class="fas fa-times"></i>
                                            </button>
                                        </form>
                                        @endif
                                    </div>
                                </div>
                                @endforeach
                            </div>
                        </div>
                    </div>
                    @endif

                    <!-- Comentarios y Colaboración -->
                    @if($innovation->status === 'aprobada' || $innovation->status === 'en_implementacion' || auth()->user()->hasRole('admin'))
                    <div class="card shadow mt-4">
                        <div class="card-header bg-info text-white">
                            <h5 class="mb-0"><i class="fas fa-comments"></i> Intercambio de Experiencias</h5>
                        </div>
                        <div class="card-body">
                            <!-- Formulario de Nuevo Comentario -->
                            <form action="{{ route('comments.store') }}" method="POST" class="mb-4">
                                @csrf
                                <input type="hidden" name="commentable_id" value="{{ $innovation->id }}">
                                <input type="hidden" name="commentable_type" value="innovation">
                                <div class="mb-2">
                                    <textarea class="form-control @error('content') is-invalid @enderror" 
                                              name="content" rows="3" 
                                              placeholder="Escribe un comentario o pregunta sobre esta innovación...">{{ old('content') }}</textarea>
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
                                $comments = $innovation->comments()->whereNull('parent_id')->with(['profile.user', 'replies.profile.user'])->get();
                            @endphp

                            @if($comments->count() > 0)
                                <div class="comments-list">
                                    @foreach($comments as $comment)
                                    <div class="card mb-3 border-light shadow-sm">
                                        <div class="card-body">
                                            <div class="d-flex justify-content-between align-items-start mb-2">
                                                <div>
                                                    <x-user-link :user="$comment->profile->user" :showAvatar="true" />
                                                    <small class="text-muted ms-2">{{ $comment->created_at->diffForHumans() }}</small>
                                                </div>
                                                @if(Auth::user()->profile->id == $comment->profile_id || Auth::user()->hasRole('admin'))
                                                <form action="{{ route('comments.destroy', $comment) }}" method="POST" class="delete-comment-form">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="btn btn-sm btn-link text-danger">
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
                                                            <button type="submit" class="btn btn-sm btn-link text-danger p-0">
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
                                                    <form action="{{ route('comments.store') }}" method="POST">
                                                        @csrf
                                                        <input type="hidden" name="commentable_id" value="{{ $innovation->id }}">
                                                        <input type="hidden" name="commentable_type" value="innovation">
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
                                    <p class="text-muted mb-0">No hay comentarios aún. ¡Inicia la conversación!</p>
                                </div>
                            @endif
                        </div>
                    </div>
                    @else
                    <div class="alert alert-light border mt-4">
                        <i class="fas fa-lock me-2 text-muted"></i>
                        <small class="text-muted">El espacio de colaboración se activará cuando la innovación sea aprobada o esté en implementación.</small>
                    </div>
                    @endif
                </div>

                <!-- Sidebar de Detalles -->
                <div class="col-md-4">
                    <!-- Tipo y Estado -->
                    <div class="card shadow mb-4">
                        <div class="card-body">
                            <h6 class="text-muted mb-3">Tipo de Innovación</h6>
                            <div class="mb-4">
                                <span class="badge bg-primary fs-6">{{ $innovation->innovationType->name }}</span>
                            </div>

                            @if($innovation->impact_score)
                            <h6 class="text-muted mb-2">Puntuación de Impacto</h6>
                            <div class="progress mb-2" style="height: 30px;">
                                <div class="progress-bar bg-success" role="progressbar" 
                                     style="width: {{ $innovation->impact_score * 10 }}%">
                                    {{ $innovation->impact_score }}/10
                                </div>
                            </div>
                            @endif
                        </div>
                    </div>

                    <!-- Panel de Aprobación (Solo Admins en estado en_revision o completada) -->
                    @if(auth()->user()->hasRole('admin') && ($innovation->status === 'en_revision' || $innovation->status === 'completada'))
                    <div class="card shadow mb-4 border-warning">
                        <div class="card-header bg-warning text-white">
                            <h6 class="mb-0"><i class="fas fa-gavel me-2"></i>Revisión Pendiente</h6>
                        </div>
                        <div class="card-body">
                            <p class="small text-muted mb-3">
                                <i class="fas fa-info-circle me-1"></i>
                                Esta innovación está esperando tu revisión para ser aprobada como Mejor Práctica.
                            </p>
                            
                            <form action="{{ route('innovations.approve', $innovation) }}" method="POST" class="mb-3">
                                @csrf
                                <textarea name="review_notes" class="form-control form-control-sm mb-2" rows="2" 
                                          placeholder="Notas de aprobación (opcional)"></textarea>
                                <button type="submit" class="btn btn-success btn-sm w-100">
                                    <i class="fas fa-check me-1"></i>Aprobar como Mejor Práctica
                                </button>
                            </form>
                            
                            <form action="{{ route('innovations.reject', $innovation) }}" method="POST">
                                @csrf
                                <textarea name="review_notes" class="form-control form-control-sm mb-2" rows="2" 
                                          placeholder="Razón del rechazo (requerido)" required></textarea>
                                <button type="submit" class="btn btn-danger btn-sm w-100">
                                    <i class="fas fa-times me-1"></i>Rechazar
                                </button>
                            </form>
                        </div>
                    </div>
                    @endif

                    <!-- Información de Revisión (Si ya fue revisada) -->
                    @if($innovation->reviewed_by && $innovation->reviewed_at)
                    <div class="card shadow mb-4 border-{{ $innovation->status === 'aprobada' ? 'success' : 'danger' }}">
                        <div class="card-header bg-{{ $innovation->status === 'aprobada' ? 'success' : 'danger' }} text-white">
                            <h6 class="mb-0">
                                <i class="fas fa-{{ $innovation->status === 'aprobada' ? 'check-circle' : 'times-circle' }} me-2"></i>
                                {{ $innovation->status === 'aprobada' ? 'Aprobada' : 'Rechazada' }}
                            </h6>
                        </div>
                        <div class="card-body">
                            <div class="mb-2">
                                <small class="text-muted d-block">Revisada por</small>
                                <strong>{{ $innovation->reviewer->name }}</strong>
                            </div>
                            <div class="mb-2">
                                <small class="text-muted d-block">Fecha de revisión</small>
                                <strong>{{ $innovation->reviewed_at->format('d/m/Y H:i') }}</strong>
                            </div>
                            @if($innovation->review_notes)
                            <div>
                                <small class="text-muted d-block">Notas de revisión</small>
                                <p class="mb-0 small">{{ $innovation->review_notes }}</p>
                            </div>
                            @endif
                        </div>
                    </div>
                    @endif

                    <!-- Información del Docente -->
                    <div class="card shadow mb-4">
                        <div class="card-body">
                            <h6 class="text-muted mb-3">Docente Responsable</h6>
                            <div>
                                <strong>{{ $innovation->profile->user->name }}</strong><br>
                                <small class="text-muted">{{ $innovation->profile->department }}</small><br>
                                <small class="text-muted">{{ $innovation->profile->specialty }}</small>
                            </div>
                        </div>
                    </div>

                    <!-- Fechas -->
                    <div class="card shadow">
                        <div class="card-body">
                            <h6 class="text-muted mb-3">Información Temporal</h6>
                            <div class="mb-2">
                                <small class="text-muted d-block">Creada</small>
                                <strong>{{ $innovation->created_at->format('d/m/Y H:i') }}</strong>
                            </div>
                            <div>
                                <small class="text-muted d-block">Última Actualización</small>
                                <strong>{{ $innovation->updated_at->format('d/m/Y H:i') }}</strong>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal de Vista Previa Global -->
<div class="modal fade" id="globalPreviewModal" tabindex="-1" aria-labelledby="previewTitle" aria-hidden="true">
    <div class="modal-dialog modal-xl modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="previewTitle">Vista Previa</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body p-0 text-center" id="previewContent" style="min-height: 400px; display: flex; align-items: center; justify-content: center; background: #f8f9fc;">
                <!-- El contenido se cargará dinámicamente -->
            </div>
        </div>
    </div>
</div>

@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Vista Previa Global
    const globalModal = new bootstrap.Modal(document.getElementById('globalPreviewModal'));
    const previewTitle = document.getElementById('previewTitle');
    const previewContent = document.getElementById('previewContent');

    window.openGlobalPreview = function(url, name, type) {
        previewTitle.textContent = name;
        previewContent.innerHTML = '<div class="spinner-border text-primary" role="status"></div>';

        if (type === 'image') {
            const img = document.createElement('img');
            img.src = url;
            img.className = 'img-fluid shadow-sm';
            img.style.maxHeight = '80vh';
            img.onload = () => { previewContent.innerHTML = ''; previewContent.appendChild(img); };
            img.onerror = () => { previewContent.innerHTML = '<div class="p-5">Error al cargar la imagen.</div>'; };
        } else if (type === 'pdf') {
            const iframe = document.createElement('iframe');
            iframe.src = url;
            iframe.style.width = '100%';
            iframe.style.height = '80vh';
            iframe.style.border = 'none';
            previewContent.innerHTML = '';
            previewContent.appendChild(iframe);
        } else {
            previewContent.innerHTML = '<div class="p-5">Este archivo no se puede previsualizar. Por favor, descárgalo.</div>';
        }

        globalModal.show();
    };
});
</script>
@endpush
