@extends('layouts.admin')

@section('title', 'Evaluar Proyecto')

@section('contenido')
<div class="container-fluid">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card shadow">
                <div class="card-header bg-success text-white">
                    <h4 class="mb-0"><i class="fas fa-star"></i> Evaluar Proyecto: {{ $project->title }}</h4>
                </div>
                <div class="card-body">
                    <form action="{{ route('evaluations.store', $project) }}" method="POST" enctype="multipart/form-data" novalidate>
                        @csrf

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
                            <strong>Proyecto:</strong> {{ $project->title }}<br>
                            <strong>Responsable:</strong> {{ $project->profile->user->name }}<br>
                            <strong>Categoría:</strong> {{ $project->category->name }}
                        </div>

                        <div class="row gx-3">
                            <!-- Columna Izquierda: Rúbrica -->
                            <div class="col-md-5 border-end">
                                <h6 class="fw-bold mb-2 text-primary small">Rúbrica de Evaluación</h6>
                                <div class="row row-cols-2 g-2">
                                    <div class="col">
                                        <label class="form-label mb-0 small x-small-text">Innovación</label>
                                        <select class="form-select form-select-sm @error('innovation_score') is-invalid @enderror" name="innovation_score">
                                            <option value="" disabled selected>--</option>
                                            @for($i = 1; $i <= 5; $i++)
                                                <option value="{{ $i }}" {{ old('innovation_score') == $i ? 'selected' : '' }}>{{ $i }}</option>
                                            @endfor
                                        </select>
                                        @error('innovation_score')<div class="invalid-feedback x-small-text">{{ $message }}</div>@enderror
                                    </div>
                                    <div class="col">
                                        <label class="form-label mb-0 small x-small-text">Pertinencia</label>
                                        <select class="form-select form-select-sm @error('relevance_score') is-invalid @enderror" name="relevance_score">
                                            <option value="" disabled selected>--</option>
                                            @for($i = 1; $i <= 5; $i++)
                                                <option value="{{ $i }}" {{ old('relevance_score') == $i ? 'selected' : '' }}>{{ $i }}</option>
                                            @endfor
                                        </select>
                                        @error('relevance_score')<div class="invalid-feedback x-small-text">{{ $message }}</div>@enderror
                                    </div>
                                    <div class="col">
                                        <label class="form-label mb-0 small x-small-text">Resultados</label>
                                        <select class="form-select form-select-sm @error('results_score') is-invalid @enderror" name="results_score">
                                            <option value="" disabled selected>--</option>
                                            @for($i = 1; $i <= 5; $i++)
                                                <option value="{{ $i }}" {{ old('results_score') == $i ? 'selected' : '' }}>{{ $i }}</option>
                                            @endfor
                                        </select>
                                        @error('results_score')<div class="invalid-feedback x-small-text">{{ $message }}</div>@enderror
                                    </div>
                                    <div class="col">
                                        <label class="form-label mb-0 small x-small-text">Impacto</label>
                                        <select class="form-select form-select-sm @error('impact_score') is-invalid @enderror" name="impact_score">
                                            <option value="" disabled selected>--</option>
                                            @for($i = 1; $i <= 5; $i++)
                                                <option value="{{ $i }}" {{ old('impact_score') == $i ? 'selected' : '' }}>{{ $i }}</option>
                                            @endfor
                                        </select>
                                        @error('impact_score')<div class="invalid-feedback x-small-text">{{ $message }}</div>@enderror
                                    </div>
                                    <div class="col">
                                        <label class="form-label mb-0 small x-small-text">Metodología</label>
                                        <select class="form-select form-select-sm @error('methodology_score') is-invalid @enderror" name="methodology_score">
                                            <option value="" disabled selected>--</option>
                                            @for($i = 1; $i <= 5; $i++)
                                                <option value="{{ $i }}" {{ old('methodology_score') == $i ? 'selected' : '' }}>{{ $i }}</option>
                                            @endfor
                                        </select>
                                        @error('methodology_score')<div class="invalid-feedback x-small-text">{{ $message }}</div>@enderror
                                    </div>
                                    <div class="col">
                                        <label class="form-label mb-0 small x-small-text fw-bold">Calif. Final</label>
                                        <div class="input-group input-group-sm @error('final_score') is-invalid @enderror">
                                            <input type="number" step="0.1" min="1" max="10" name="final_score" class="form-control form-control-sm @error('final_score') is-invalid @enderror" value="{{ old('final_score') }}">
                                        </div>
                                        @error('final_score')<div class="invalid-feedback x-small-text">{{ $message }}</div>@enderror
                                    </div>
                                </div>
                                
@push('styles')
    <link rel="stylesheet" href="{{ asset('assets/back/css/evaluations.css') }}">
@endpush

                                <hr class="my-2">
                                
                                <div class="mb-2">
                                    <label class="form-label small fw-bold mb-1">Estado y Reporte</label>
                                    <select class="form-select form-select-sm mb-2 @error('status') is-invalid @enderror" name="status">
                                        <option value="borrador" {{ old('status') == 'borrador' ? 'selected' : '' }}>Borrador</option>
                                        <option value="finalizada" {{ old('status') == 'finalizada' ? 'selected' : '' }}>Finalizada</option>
                                    </select>
                                    @error('status')<div class="invalid-feedback x-small-text">{{ $message }}</div>@enderror
                                    
                                    <input type="file" class="form-control form-control-sm @error('report_file') is-invalid @enderror" name="report_file" accept=".pdf">
                                    @error('report_file')<div class="invalid-feedback x-small-text">{{ $message }}</div>@enderror
                                    <div class="small text-muted mt-1" style="font-size: 0.7rem;">Opcional: Informe en PDF (vía archivo).</div>
                                </div>
                            </div>

                            <!-- Columna Derecha: Retroalimentación -->
                            <div class="col-md-7">
                                <h6 class="fw-bold mb-2 text-primary small">Retroalimentación Detallada</h6>
                                <div class="row g-2">
                                    <div class="col-6">
                                        <label class="form-label mb-0 small x-small-text fw-bold">Fortalezas</label>
                                        <textarea class="form-control form-control-sm" name="strengths" rows="3" placeholder="... ">{{ old('strengths') }}</textarea>
                                    </div>
                                    <div class="col-6">
                                        <label class="form-label mb-0 small x-small-text fw-bold">Debilidades</label>
                                        <textarea class="form-control form-control-sm" name="weaknesses" rows="3" placeholder="... ">{{ old('weaknesses') }}</textarea>
                                    </div>
                                    <div class="col-6">
                                        <label class="form-label mb-0 small x-small-text fw-bold">Recomendaciones</label>
                                        <textarea class="form-control form-control-sm" name="recommendations" rows="3" placeholder="... ">{{ old('recommendations') }}</textarea>
                                    </div>
                                    <div class="col-6">
                                        <label class="form-label mb-0 small x-small-text fw-bold">Comentarios Generales</label>
                                        <textarea class="form-control form-control-sm" name="general_comments" rows="3" placeholder="... ">{{ old('general_comments') }}</textarea>
                                    </div>
                                </div>
                            </div>
                        </div>


                        <!-- Botones -->
                        <div class="d-flex justify-content-end gap-2">
                            <a href="{{ route('projects.show', $project) }}" class="btn btn-secondary">
                                <i class="fas fa-times"></i> Cancelar
                            </a>
                            <button type="submit" class="btn btn-success">
                                <i class="fas fa-save"></i> Guardar Evaluación
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
