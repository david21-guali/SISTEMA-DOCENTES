@extends('layouts.admin')

@section('title', 'Editar Evaluación')

@section('contenido')
<div class="container-fluid">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card shadow">
                <div class="card-header bg-warning text-dark">
                    <h4 class="mb-0"><i class="fas fa-edit"></i> Editar Evaluación: {{ $evaluation->project->title }}</h4>
                </div>
                <div class="card-body">
                    <form action="{{ route('evaluations.update', $evaluation) }}" method="POST" enctype="multipart/form-data" novalidate>
                        @csrf
                        @method('PUT')

                        @if($errors->any())
                        <div class="alert alert-danger alert-dismissible fade show" role="alert">
                            <div class="d-flex align-items-center">
                                <i class="fas fa-exclamation-triangle me-2"></i>
                                <div>
                                    <strong>¡Atención!</strong> Debes completar todos los campos obligatorios de la rúbrica.
                                </div>
                            </div>
                            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                        </div>
                        @endif

                        <!-- Información del Proyecto -->
                        <div class="alert alert-info mb-4">
                            <strong>Proyecto:</strong> {{ $evaluation->project->title }}<br>
                            <strong>Responsable:</strong> {{ $evaluation->project->profile->user->name }}<br>
                            <strong>Evaluador:</strong> {{ $evaluation->evaluator->user->name }}
                        </div>

                        <div class="row gx-3">
                            <!-- Columna Izquierda: Rúbrica -->
                            <div class="col-md-5 border-end">
                                <h6 class="fw-bold mb-2 text-primary small">Rúbrica de Evaluación</h6>
                                <div class="row row-cols-2 g-2">
                                    <div class="col">
                                        <label class="form-label mb-0 small x-small-text">Innovación</label>
                                        <select class="form-select form-select-sm @error('innovation_score') is-invalid @enderror" name="innovation_score">
                                            @for($i = 1; $i <= 5; $i++)
                                                <option value="{{ $i }}" {{ old('innovation_score', $evaluation->innovation_score) == $i ? 'selected' : '' }}>{{ $i }}</option>
                                            @endfor
                                        </select>
                                        @error('innovation_score')<div class="invalid-feedback x-small-text">{{ $message }}</div>@enderror
                                    </div>
                                    <div class="col">
                                        <label class="form-label mb-0 small x-small-text">Pertinencia</label>
                                        <select class="form-select form-select-sm @error('relevance_score') is-invalid @enderror" name="relevance_score">
                                            @for($i = 1; $i <= 5; $i++)
                                                <option value="{{ $i }}" {{ old('relevance_score', $evaluation->relevance_score) == $i ? 'selected' : '' }}>{{ $i }}</option>
                                            @endfor
                                        </select>
                                        @error('relevance_score')<div class="invalid-feedback x-small-text">{{ $message }}</div>@enderror
                                    </div>
                                    <div class="col">
                                        <label class="form-label mb-0 small x-small-text">Resultados</label>
                                        <select class="form-select form-select-sm @error('results_score') is-invalid @enderror" name="results_score">
                                            @for($i = 1; $i <= 5; $i++)
                                                <option value="{{ $i }}" {{ old('results_score', $evaluation->results_score) == $i ? 'selected' : '' }}>{{ $i }}</option>
                                            @endfor
                                        </select>
                                        @error('results_score')<div class="invalid-feedback x-small-text">{{ $message }}</div>@enderror
                                    </div>
                                    <div class="col">
                                        <label class="form-label mb-0 small x-small-text">Impacto</label>
                                        <select class="form-select form-select-sm @error('impact_score') is-invalid @enderror" name="impact_score">
                                            @for($i = 1; $i <= 5; $i++)
                                                <option value="{{ $i }}" {{ old('impact_score', $evaluation->impact_score) == $i ? 'selected' : '' }}>{{ $i }}</option>
                                            @endfor
                                        </select>
                                        @error('impact_score')<div class="invalid-feedback x-small-text">{{ $message }}</div>@enderror
                                    </div>
                                    <div class="col">
                                        <label class="form-label mb-0 small x-small-text">Metodología</label>
                                        <select class="form-select form-select-sm @error('methodology_score') is-invalid @enderror" name="methodology_score">
                                            @for($i = 1; $i <= 5; $i++)
                                                <option value="{{ $i }}" {{ old('methodology_score', $evaluation->methodology_score) == $i ? 'selected' : '' }}>{{ $i }}</option>
                                            @endfor
                                        </select>
                                        @error('methodology_score')<div class="invalid-feedback x-small-text">{{ $message }}</div>@enderror
                                    </div>
                                    <div class="col">
                                        <label class="form-label mb-0 small x-small-text fw-bold">Calif. Final</label>
                                        <div class="input-group input-group-sm @error('final_score') is-invalid @enderror">
                                            <input type="number" step="0.1" min="1" max="10" name="final_score" class="form-control form-control-sm @error('final_score') is-invalid @enderror" value="{{ old('final_score', $evaluation->final_score) }}">
                                        </div>
                                        @error('final_score')<div class="invalid-feedback x-small-text">{{ $message }}</div>@enderror
                                    </div>
                                </div>
                                
                                <style>
                                    .x-small-text { font-size: 0.75rem; }
                                    .form-select-sm, .form-control-sm { padding-top: 2px; padding-bottom: 2px; }
                                </style>

                                <hr class="my-2">
                                
                                <div class="mb-2">
                                    <label class="form-label small fw-bold mb-1">Estado y Reporte</label>
                                    <select class="form-select form-select-sm mb-2" name="status">
                                        <option value="borrador" {{ old('status', $evaluation->status) == 'borrador' ? 'selected' : '' }}>Borrador</option>
                                        <option value="finalizada" {{ old('status', $evaluation->status) == 'finalizada' ? 'selected' : '' }}>Finalizada</option>
                                    </select>
                                    <input type="file" class="form-control form-control-sm @error('report_file') is-invalid @enderror" name="report_file" accept=".pdf">
                                    @error('report_file')<div class="invalid-feedback x-small-text">{{ $message }}</div>@enderror
                                    <div class="small text-muted mt-1" style="font-size: 0.7rem;">Opcional: Reemplazar archivo PDF manual.</div>
                                </div>
                            </div>

                            <!-- Columna Derecha: Retroalimentación -->
                            <div class="col-md-7">
                                <h6 class="fw-bold mb-2 text-primary small">Retroalimentación Detallada</h6>
                                <div class="row g-2">
                                    <div class="col-6">
                                        <label class="form-label mb-0 small x-small-text fw-bold">Fortalezas</label>
                                        <textarea class="form-control form-control-sm" name="strengths" rows="3">{{ old('strengths', $evaluation->strengths) }}</textarea>
                                    </div>
                                    <div class="col-6">
                                        <label class="form-label mb-0 small x-small-text fw-bold">Debilidades</label>
                                        <textarea class="form-control form-control-sm" name="weaknesses" rows="3">{{ old('weaknesses', $evaluation->weaknesses) }}</textarea>
                                    </div>
                                    <div class="col-6">
                                        <label class="form-label mb-0 small x-small-text fw-bold">Recomendaciones</label>
                                        <textarea class="form-control form-control-sm" name="recommendations" rows="3">{{ old('recommendations', $evaluation->recommendations) }}</textarea>
                                    </div>
                                    <div class="col-6">
                                        <label class="form-label mb-0 small x-small-text fw-bold">Comentarios Generales</label>
                                        <textarea class="form-control form-control-sm" name="general_comments" rows="3">{{ old('general_comments', $evaluation->general_comments) }}</textarea>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Botones -->
                        <div class="d-flex justify-content-end gap-2">
                            <a href="{{ route('projects.show', $evaluation->project) }}" class="btn btn-secondary">
                                <i class="fas fa-times"></i> Cancelar
                            </a>
                            <button type="submit" class="btn btn-warning">
                                <i class="fas fa-save"></i> Actualizar Evaluación
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
