@extends('layouts.admin')

@section('title', 'Editar Innovación')

@section('contenido')
<div class="container-fluid">
    <div class="row justify-content-center">
        <div class="col-md-10">
            <div class="card shadow">
                <div class="card-header bg-warning text-dark">
                    <h4 class="mb-0"><i class="fas fa-edit"></i> Editar Innovación</h4>
                </div>
                <div class="card-body">
                    <form action="{{ route('innovations.update', $innovation) }}" method="POST" enctype="multipart/form-data" novalidate>
                        @csrf
                        @method('PUT')

                        <div class="row">
                            <!-- Título -->
                            <div class="col-md-8 mb-3">
                                <label for="title" class="form-label">Título de la Innovación *</label>
                                <input type="text" class="form-control @error('title') is-invalid @enderror" 
                                       id="title" name="title" value="{{ old('title', $innovation->title) }}">
                                @error('title')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <!-- Tipo de Innovación -->
                            <div class="col-md-4 mb-3">
                                <label for="innovation_type_id" class="form-label">Tipo de Innovación *</label>
                                <select class="form-select @error('innovation_type_id') is-invalid @enderror" 
                                        id="innovation_type_id" name="innovation_type_id">
                                    @foreach($innovationTypes as $type)
                                        <option value="{{ $type->id }}" 
                                            {{ old('innovation_type_id', $innovation->innovation_type_id) == $type->id ? 'selected' : '' }}>
                                            {{ $type->name }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('innovation_type_id')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <!-- Descripción -->
                        <div class="mb-3">
                            <label for="description" class="form-label">Descripción *</label>
                            <textarea class="form-control @error('description') is-invalid @enderror" 
                                      id="description" name="description" rows="3">{{ old('description', $innovation->description) }}</textarea>
                            @error('description')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Estado -->
                        <div class="mb-3">
                            <label for="status" class="form-label">Estado *</label>
                            <select class="form-select @error('status') is-invalid @enderror" 
                                    id="status" name="status">
                                <option value="propuesta" {{ old('status', $innovation->status) == 'propuesta' ? 'selected' : '' }}>Propuesta</option>
                                <option value="en_implementacion" {{ old('status', $innovation->status) == 'en_implementacion' ? 'selected' : '' }}>En Implementación</option>
                                <option value="completada" {{ old('status', $innovation->status) == 'completada' ? 'selected' : '' }}>Completada</option>
                            </select>
                            @error('status')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Metodología -->
                        <div class="mb-3">
                            <label for="methodology" class="form-label">Metodología Aplicada *</label>
                            <textarea class="form-control @error('methodology') is-invalid @enderror" 
                                      id="methodology" name="methodology" rows="3">{{ old('methodology', $innovation->methodology) }}</textarea>
                            @error('methodology')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="row">
                            <!-- Resultados Esperados -->
                            <div class="col-md-6 mb-3">
                                <label for="expected_results" class="form-label">Resultados Esperados *</label>
                                <textarea class="form-control @error('expected_results') is-invalid @enderror" 
                                          id="expected_results" name="expected_results" rows="3">{{ old('expected_results', $innovation->expected_results) }}</textarea>
                                @error('expected_results')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <!-- Resultados Reales -->
                            <div class="col-md-6 mb-3">
                                <label for="actual_results" class="form-label">Resultados Obtenidos *</label>
                                <textarea class="form-control @error('actual_results') is-invalid @enderror" 
                                          id="actual_results" name="actual_results" rows="3">{{ old('actual_results', $innovation->actual_results) }}</textarea>
                                @error('actual_results')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <!-- Impacto -->
                        <div class="mb-3">
                            <label for="impact_score" class="form-label">Puntuación de Impacto (1-10) *</label>
                            <input type="number" min="1" max="10" class="form-control @error('impact_score') is-invalid @enderror" 
                                   id="impact_score" name="impact_score" value="{{ old('impact_score', $innovation->impact_score) }}">
                            @error('impact_score')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Archivos Existentes -->
                        @if($innovation->attachments->count() > 0)
                        <div class="mb-3">
                            <label class="form-label">Archivos de Evidencia Actuales</label>
                            <div class="list-group">
                                @foreach($innovation->attachments as $attachment)
                                <div class="list-group-item d-flex justify-content-between align-items-center">
                                    <div class="d-flex align-items-center">
                                        <i class="{{ $attachment->icon }} me-2"></i>
                                        <div>
                                            <!-- Logic for preview vs specific link -->
                                            @php
                                                $canPreview = $attachment->isImage() || $attachment->isPdf();
                                                $previewType = $attachment->isImage() ? 'image' : ($attachment->isPdf() ? 'pdf' : 'other');
                                            @endphp

                                            @if($canPreview)
                                                <a href="#" class="text-decoration-none fw-bold text-dark"
                                                   data-bs-toggle="modal" 
                                                   data-bs-target="#previewModal"
                                                   data-url="{{ $attachment->url }}"
                                                   data-type="{{ $previewType }}"
                                                   data-name="{{ $attachment->original_name }}">
                                                    {{ $attachment->original_name }}
                                                </a>
                                            @else
                                                <a href="{{ $attachment->url }}" target="_blank" class="text-decoration-none fw-bold text-dark">
                                                    {{ $attachment->original_name }}
                                                </a>
                                            @endif

                                            <span class="text-muted small">({{ $attachment->human_size }})</span>
                                        </div>
                                    </div>
                                    <div class="d-flex gap-2">
                                        @if($canPreview)
                                        <button type="button" class="btn btn-sm btn-outline-primary"
                                                data-bs-toggle="modal" 
                                                data-bs-target="#previewModal"
                                                data-url="{{ $attachment->url }}"
                                                data-type="{{ $previewType }}"
                                                data-name="{{ $attachment->original_name }}"
                                                title="Ver Archivo">
                                            <i class="fas fa-eye"></i>
                                        </button>
                                        @endif

                                        <button type="button" class="btn btn-sm btn-danger" 
                                                onclick="confirmDeleteAttachment('{{ route('innovations.attachments.delete', [$innovation, $attachment]) }}')">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </div>
                                </div>
                                @endforeach
                            </div>
                        </div>
                        @endif

                        <!-- Nuevos Archivos -->
                        <div class="mb-4">
                            <label for="evidence_files" class="form-label">Agregar Más Archivos de Evidencia</label>
                            <input type="file" class="form-control @error('evidence_files.*') is-invalid @enderror" 
                                   id="evidence_files" name="evidence_files[]" multiple>
                            @error('evidence_files.*')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Botones -->
                        <div class="d-flex justify-content-end gap-2">
                            <a href="{{ route('innovations.index') }}" class="btn btn-secondary">
                                <i class="fas fa-times"></i> Cancelar
                            </a>
                            <button type="submit" class="btn btn-warning">
                                <i class="fas fa-save"></i> Actualizar Innovación
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Formulario oculto para eliminación de adjuntos -->
<form id="deleteAttachmentForm" action="" method="POST" style="display: none;">
    @csrf
    @method('DELETE')
</form>

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
    function confirmDeleteAttachment(url) {
        if (confirm('¿Eliminar este archivo? NO podrás deshacer esta acción.')) {
            const form = document.getElementById('deleteAttachmentForm');
            form.action = url;
            form.submit();
        }
    }

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
