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
                        <form action="{{ route('innovations.request-review', $innovation) }}" method="POST" class="d-inline">
                            @csrf
                            <button type="submit" class="btn btn-info text-white me-2">
                                <i class="fas fa-paper-plane"></i> Solicitar Revisión
                            </button>
                        </form>
                    @endif

                    @can('edit-innovations')
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
                    <div class="card shadow">
                        <div class="card-header bg-info text-white">
                            <h5 class="mb-0">
                                <i class="fas fa-paperclip"></i> Archivos de Evidencia
                                <span class="badge bg-light text-dark ms-2">{{ $innovation->attachments->count() }}</span>
                            </h5>
                        </div>
                        <div class="card-body">
                            <div class="list-group">
                                @foreach($innovation->attachments as $file)
                                <div class="list-group-item d-flex justify-content-between align-items-center">
                                    <div class="d-flex align-items-center">
                                        <i class="{{ $file->icon }} fa-2x me-3"></i>
                                        <div>
                                            <!-- Logic for preview vs specific link -->
                                            @php
                                                $canPreview = $file->isImage() || $file->isPdf();
                                                $previewType = $file->isImage() ? 'image' : ($file->isPdf() ? 'pdf' : 'other');
                                            @endphp

                                            @if($canPreview)
                                                <a href="#" class="text-decoration-none fw-bold text-dark"
                                                   data-bs-toggle="modal" 
                                                   data-bs-target="#previewModal"
                                                   data-url="{{ $file->url }}"
                                                   data-type="{{ $previewType }}"
                                                   data-name="{{ $file->original_name }}">
                                                    {{ $file->original_name }}
                                                </a>
                                            @else
                                                <a href="{{ $file->url }}" target="_blank" class="text-decoration-none fw-bold text-dark">
                                                    {{ $file->original_name }}
                                                </a>
                                            @endif

                                            <div class="small text-muted">
                                                {{ $file->human_size }} • {{ $file->created_at->format('d/m/Y') }}
                                            </div>
                                        </div>
                                    </div>
                                    
                                    <div class="d-flex gap-2">
                                        @if($canPreview)
                                        <button type="button" class="btn btn-sm btn-outline-primary"
                                                data-bs-toggle="modal" 
                                                data-bs-target="#previewModal"
                                                data-url="{{ $file->url }}"
                                                data-type="{{ $previewType }}"
                                                data-name="{{ $file->original_name }}"
                                                title="Ver Archivo">
                                            <i class="fas fa-eye"></i>
                                        </button>
                                        @endif
                                        
                                        <a href="{{ route('attachments.download', $file) }}" class="btn btn-sm btn-outline-secondary" title="Descargar">
                                            <i class="fas fa-download"></i>
                                        </a>
                                    </div>
                                </div>
                                @endforeach
                            </div>
                        </div>
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

<!-- Modal de Previsualización -->
<div class="modal fade" id="previewModal" tabindex="-1" aria-labelledby="previewModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-xl modal-dialog-centered">
        <div class="modal-content" style="height: 90vh;">
            <div class="modal-header">
                <h5 class="modal-title" id="previewModalLabel">Previsualización de Archivo</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body p-0 d-flex justify-content-center align-items-center bg-light" style="overflow: hidden;">
                <div id="loadingSpinner" class="spinner-border text-primary" role="status" style="display: none;">
                    <span class="visually-hidden">Cargando...</span>
                </div>
                <img id="previewImage" src="" class="img-fluid" style="display: none; max-height: 100%; max-width: 100%; object-fit: contain;">
                <iframe id="previewFrame" src="" style="display: none; width: 100%; height: 100%; border: none;"></iframe>
                <div id="previewError" class="text-center p-5" style="display: none;">
                    <i class="fas fa-exclamation-triangle fa-3x text-warning mb-3"></i>
                    <p>No se puede previsualizar este archivo.</p>
                    <a id="downloadLink" href="#" class="btn btn-primary" download>
                        <i class="fas fa-download"></i> Descargar Archivo
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>

@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const previewModal = document.getElementById('previewModal');
    const previewImage = document.getElementById('previewImage');
    const previewFrame = document.getElementById('previewFrame');
    const previewError = document.getElementById('previewError');
    const downloadLink = document.getElementById('downloadLink');
    const loadingSpinner = document.getElementById('loadingSpinner');
    const modalTitle = document.getElementById('previewModalLabel');

    if (previewModal) {
        previewModal.addEventListener('show.bs.modal', function(event) {
            // Button that triggered the modal
            const button = event.relatedTarget;
            const fileUrl = button.getAttribute('data-url');
            const fileType = button.getAttribute('data-type'); // 'image' or 'pdf'
            const fileName = button.getAttribute('data-name');

            modalTitle.textContent = fileName || 'Previsualización';

            // Reset displays
            previewImage.style.display = 'none';
            previewImage.src = '';
            previewFrame.style.display = 'none';
            previewFrame.src = '';
            previewError.style.display = 'none';
            loadingSpinner.style.display = 'block';

            if (fileType === 'pdf') {
                previewFrame.src = fileUrl;
                previewFrame.onload = function() {
                    loadingSpinner.style.display = 'none';
                    previewFrame.style.display = 'block';
                };
            } else if (fileType === 'image') {
                previewImage.src = fileUrl;
                previewImage.onload = function() {
                    loadingSpinner.style.display = 'none';
                    previewImage.style.display = 'block';
                };
                previewImage.onerror = function() {
                    loadingSpinner.style.display = 'none';
                    previewError.style.display = 'block';
                    downloadLink.href = fileUrl;
                };
            } else {
                // Fallback
                loadingSpinner.style.display = 'none';
                previewError.style.display = 'block';
                downloadLink.href = fileUrl;
            }
        });

        previewModal.addEventListener('hidden.bs.modal', function () {
            previewFrame.src = '';
            previewImage.src = '';
        });
    }
});
</script>
@endpush
