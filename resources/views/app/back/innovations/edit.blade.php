@extends('layouts.admin')

@section('title', 'Editar Innovación')

@push('styles')
    <link rel="stylesheet" href="{{ asset('assets/back/css/innovations.css') }}">
@endpush
@section('contenido')
<div class="container-fluid">
    <div class="row justify-content-center">
        <div class="col-md-10">
            <div class="card shadow">
                <div class="card-header bg-warning text-dark">
                    <h4 class="mb-0"><i class="fas fa-edit"></i> Editar Innovación</h4>
                </div>
                <div class="card-body">
                    <form action="{{ route('innovations.update', $innovation) }}" method="POST" enctype="multipart/form-data" novalidate id="innovationForm">
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

                        <!-- Archivos Existentes -->
                        @if($innovation->attachments->count() > 0)
                        <div class="mb-4">
                            <label class="form-label fw-bold">Archivos de Evidencia Actuales</label>
                            <div class="list-group list-group-flush border-top border-bottom">
                                @foreach($innovation->attachments as $attachment)
                                <div class="list-group-item d-flex justify-content-between align-items-center px-0">
                                    <div class="d-flex align-items-center overflow-hidden">
                                        @php
                                            $canPreview = $attachment->isImage() || $attachment->isPdf();
                                            $previewType = $attachment->isImage() ? 'image' : ($attachment->isPdf() ? 'pdf' : 'other');
                                        @endphp
                                        <div class="me-2 clickable-thumbnail" style="cursor: pointer;"
                                             @if($canPreview) onclick="openGlobalPreview('{{ route('storage.preview', $attachment->path) }}', '{{ $attachment->original_name }}', '{{ $previewType }}')" @endif>
                                            <i class="{{ $attachment->icon }} fa-lg"></i>
                                        </div>
                                        <div class="text-truncate">
                                            <span class="fw-bold text-dark d-block text-truncate small" title="{{ $attachment->original_name }}">
                                                {{ $attachment->original_name }}
                                            </span>
                                            <div class="small text-muted">{{ $attachment->human_size }}</div>
                                        </div>
                                    </div>
                                    <div class="d-flex align-items-center gap-1">
                                        @if($canPreview)
                                        <button type="button" class="btn btn-sm btn-outline-primary"
                                                onclick="openGlobalPreview('{{ route('storage.preview', $attachment->path) }}', '{{ $attachment->original_name }}', '{{ $previewType }}')"
                                                title="Ver Archivo">
                                            <i class="fas fa-eye"></i>
                                        </button>
                                        @endif

                                        <a href="{{ route('attachments.download', $attachment) }}" class="btn btn-sm btn-outline-secondary" title="Descargar">
                                            <i class="fas fa-download"></i>
                                        </a>

                                        <button type="button" class="btn btn-sm text-danger" 
                                                onclick="confirmDeleteAttachment('{{ route('attachments.destroy', $attachment) }}')"
                                                title="Eliminar">
                                            <i class="fas fa-times"></i>
                                        </button>
                                    </div>
                                </div>
                                @endforeach
                            </div>
                        </div>
                        @endif

                        <!-- Nuevos Archivos -->
                        <div class="mb-4">
                            <label for="evidence_files" class="form-label fw-bold">Agregar Más Archivos de Evidencia</label>
                            <input type="file" class="form-control @error('evidence_files.*') is-invalid @enderror" 
                                   id="evidence_files" name="evidence_files[]" multiple
                                   onchange="previewFiles()">
                            <small class="text-muted">Los nuevos archivos se añadirán a la lista actual al guardar.</small>
                            @error('evidence_files.*')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror

                            <!-- Contenedor para previsualización de nuevos archivos -->
                            <div id="file-preview-container" class="mt-3" style="display: none;">
                                <h6 class="small fw-bold text-muted mb-2"><i class="fas fa-plus-circle me-1"></i> Nuevos archivos para subir:</h6>
                                <div id="file-list" class="list-group list-group-flush border-top border-bottom">
                                    <!-- Se llenará con JS -->
                                </div>
                            </div>
                        </div>

                        <!-- Botones -->
                        <div class="d-flex justify-content-end gap-2">
                            <a href="{{ route('innovations.index') }}" class="btn btn-secondary">
                                <i class="fas fa-times"></i> Cancelar
                            </a>
                            <button type="submit" class="btn btn-warning" id="submitBtn">
                                <i class="fas fa-save"></i> <span class="btn-text">Actualizar Innovación</span>
                                <span class="spinner-border spinner-border-sm d-none" role="status" aria-hidden="true"></span>
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
@vite(['resources/js/pages/innovations-form.js'])

@endpush
