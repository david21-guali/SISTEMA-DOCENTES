@extends('layouts.admin')

@section('title', 'Crear Innovación')

@push('styles')
    <link rel="stylesheet" href="{{ asset('assets/back/css/innovations.css') }}">
@endpush
@section('contenido')
<div class="container-fluid">
    <div class="row justify-content-center">
        <div class="col-md-10">
            <div class="card shadow">
                <div class="card-header bg-primary text-white">
                    <h4 class="mb-0"><i class="fas fa-plus-circle"></i> Crear Nueva Innovación Pedagógica</h4>
                </div>
                <div class="card-body">
                    <form action="{{ route('innovations.store') }}" method="POST" enctype="multipart/form-data" novalidate id="innovationForm">
                        @csrf

                        <div class="row">
                            <!-- Título -->
                            <div class="col-md-8 mb-3">
                                <label for="title" class="form-label">Título de la Innovación *</label>
                                <input type="text" class="form-control @error('title') is-invalid @enderror" 
                                       id="title" name="title" value="{{ old('title') }}">
                                @error('title')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <!-- Tipo de Innovación -->
                            <div class="col-md-4 mb-3">
                                <label for="innovation_type_id" class="form-label">Tipo de Innovación *</label>
                                <select class="form-select @error('innovation_type_id') is-invalid @enderror" 
                                        id="innovation_type_id" name="innovation_type_id">
                                    <option value="">Seleccionar...</option>
                                    @foreach($innovationTypes as $type)
                                        <option value="{{ $type->id }}" {{ old('innovation_type_id') == $type->id ? 'selected' : '' }}>
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
                                      id="description" name="description" rows="3">{{ old('description') }}</textarea>
                            @error('description')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Metodología -->
                        <div class="mb-3">
                            <label for="methodology" class="form-label">Metodología Aplicada *</label>
                            <textarea class="form-control @error('methodology') is-invalid @enderror" 
                                      id="methodology" name="methodology" rows="3">{{ old('methodology') }}</textarea>
                            <small class="text-muted">Describe cómo se implementará o implementó esta innovación</small>
                            @error('methodology')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="row">
                            <!-- Resultados Esperados -->
                            <div class="col-md-6 mb-3">
                                <label for="expected_results" class="form-label">Resultados Esperados *</label>
                                <textarea class="form-control @error('expected_results') is-invalid @enderror" 
                                          id="expected_results" name="expected_results" rows="3">{{ old('expected_results') }}</textarea>
                                @error('expected_results')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <!-- Resultados Reales -->
                            <div class="col-md-6 mb-3">
                                <label for="actual_results" class="form-label">Resultados Obtenidos *</label>
                                <textarea class="form-control @error('actual_results') is-invalid @enderror" 
                                          id="actual_results" name="actual_results" rows="3">{{ old('actual_results') }}</textarea>
                                @error('actual_results')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <!-- Archivos de Evidencia -->
                        <div class="mb-4">
                            <label for="evidence_files" class="form-label fw-bold">Archivos de Evidencia</label>
                            <input type="file" class="form-control @error('evidence_files.*') is-invalid @enderror" 
                                   id="evidence_files" name="evidence_files[]" multiple
                                   onchange="previewFiles()">
                            <small class="text-muted">Puedes subir múltiples archivos (máx. 10MB cada uno)</small>
                            @error('evidence_files.*')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror

                            <!-- Contenedor para previsualización de archivos seleccionados -->
                            <div id="file-preview-container" class="mt-3" style="display: none;">
                                <h6 class="small fw-bold text-muted mb-2"><i class="fas fa-list me-1"></i> Archivos seleccionados para subir:</h6>
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
                            <button type="submit" class="btn btn-primary" id="submitBtn">
                                <i class="fas fa-save"></i> <span class="btn-text">Guardar Innovación</span>
                                <span class="spinner-border spinner-border-sm d-none" role="status" aria-hidden="true"></span>
                            </button>
                        </div>
                    </form>
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
@vite(['resources/js/pages/innovations-form.js'])

@endpush
