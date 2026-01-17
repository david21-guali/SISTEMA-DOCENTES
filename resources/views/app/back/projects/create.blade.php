@extends('layouts.admin')

@section('title', 'Crear Proyecto')

@section('contenido')
<div class="container-fluid">
    <div class="row justify-content-center">
        <div class="col-md-10">
            <div class="card shadow">
                <div class="card-header bg-primary text-white">
                    <h4 class="mb-0"><i class="fas fa-plus-circle"></i> Crear Nuevo Proyecto</h4>
                </div>
                <div class="card-body">
                    <form action="{{ route('projects.store') }}" method="POST" enctype="multipart/form-data" novalidate id="projectForm">
                        @csrf

                        <div class="row">
                            <!-- Título -->
                            <div class="col-md-8 mb-3">
                                <label for="title" class="form-label">Título del Proyecto *</label>
                                <input type="text" class="form-control @error('title') is-invalid @enderror" 
                                       id="title" name="title" value="{{ old('title') }}">
                                @error('title')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <!-- Categoría -->
                            <div class="col-md-4 mb-3">
                                <label for="category_id" class="form-label">Categoría *</label>
                                <div class="input-group">
                                    <select class="form-select @error('category_id') is-invalid @enderror" 
                                            id="category_id" name="category_id">
                                        <option value="">Seleccionar...</option>
                                        @foreach($categories as $category)
                                            <option value="{{ $category->id }}" {{ old('category_id') == $category->id ? 'selected' : '' }}>
                                                {{ $category->name }}
                                            </option>
                                        @endforeach
                                    </select>
                                    <button class="btn btn-outline-secondary" type="button" data-bs-toggle="modal" data-bs-target="#createCategoryModal" title="Nueva Categoría">
                                        <i class="fas fa-plus"></i>
                                    </button>
                                </div>
                                @error('category_id')
                                    <div class="invalid-feedback d-block">{{ $message }}</div>
                                @enderror
                                
                                @if(auth()->user()->hasRole('admin'))
                                <div class="mt-2">
                                    <button class="btn btn-sm btn-link text-muted p-0" type="button" data-bs-toggle="collapse" data-bs-target="#manageCategoriesSection">
                                        <i class="fas fa-cog me-1"></i> Gestionar categorías
                                    </button>
                                    <div class="collapse mt-2" id="manageCategoriesSection">
                                        <div class="border rounded p-2 bg-light">
                                            <p class="small text-muted mb-2"><i class="fas fa-info-circle"></i> Solo puedes eliminar categorías sin proyectos asociados.</p>
                                            <div class="list-group list-group-flush">
                                                @foreach($categories as $cat)
                                                <div class="list-group-item d-flex justify-content-between align-items-center px-2 py-1 bg-transparent border-0">
                                                    <span class="small">{{ $cat->name }} <span class="badge bg-secondary">{{ $cat->projects->count() }}</span></span>
                                                    <button type="button" class="btn btn-sm btn-link text-danger p-0" 
                                                            onclick="deleteCategory({{ $cat->id }}, '{{ $cat->name }}', {{ $cat->projects->count() }})"
                                                            title="Eliminar categoría">
                                                        <i class="fas fa-trash"></i>
                                                    </button>
                                                </div>
                                                @endforeach
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                @endif
                            </div>

                        </div>

                        <!-- Equipo de Trabajo -->
                        <div class="mb-3">
                            <label class="form-label">Equipo de Trabajo </label>
                            
                            <div class="card p-2 bg-light border">
                                <!-- Search Input -->
                                <div class="input-group mb-2">
                                    <span class="input-group-text bg-white"><i class="fas fa-search"></i></span>
                                    <input type="text" id="user_search" class="form-control" placeholder="Buscar persona..." onkeyup="filterUsers()">
                                </div>

                                <!-- Checklist Container -->
                                <div id="user_list" style="max-height: 200px; overflow-y: auto;" class="bg-white border rounded p-2">
                                    @foreach($users as $user)
                                        <div class="form-check user-item">
                                            <input class="form-check-input" type="checkbox" name="team_members[]" value="{{ $user->id }}" 
                                                   id="user_{{ $user->id }}"
                                                   {{ (collect(old('team_members'))->contains($user->id)) ? 'checked' : '' }}>
                                            <label class="form-check-label w-100" for="user_{{ $user->id }}">
                                                {{ $user->name }} <span class="text-muted small">({{ $user->email }})</span>
                                            </label>
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                            <small class="text-muted">Selecciona los participantes del equipo.</small>
                            @error('team_members')
                                <div class="invalid-feedback d-block">{{ $message }}</div>
                            @enderror
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

                        <!-- Objetivos -->
                        <div class="mb-3">
                            <label for="objectives" class="form-label">Objetivos *</label>
                            <textarea class="form-control @error('objectives') is-invalid @enderror" 
                                      id="objectives" name="objectives" rows="3">{{ old('objectives') }}</textarea>
                            @error('objectives')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="row">
                            <!-- Fecha Inicio -->
                            <div class="col-md-6 mb-3">
                                <label for="start_date" class="form-label">Fecha de Inicio *</label>
                                <input type="date" class="form-control @error('start_date') is-invalid @enderror" 
                                       id="start_date" name="start_date" value="{{ old('start_date') }}">
                                @error('start_date')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <!-- Fecha Fin -->
                            <div class="col-md-6 mb-3">
                                <label for="end_date" class="form-label">Fecha de Fin *</label>
                                <input type="date" class="form-control @error('end_date') is-invalid @enderror" 
                                       id="end_date" name="end_date" value="{{ old('end_date') }}">
                                @error('end_date')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <!-- Opciones Financieras -->
                        <div class="mb-3 border p-3 rounded bg-light">
                            <div class="form-check form-switch">
                                <input class="form-check-input" type="checkbox" id="project_needs_budget" 
                                        {{ old('budget') ? 'checked' : '' }}
                                        onchange="toggleBudget()">
                                <label class="form-check-label fw-bold" for="project_needs_budget">
                                    <i class="fas fa-coins"></i> ¿Este proyecto requiere adquisición de bienes o presupuesto?
                                </label>
                            </div>
                            
                            <!-- Presupuesto (Oculto por defecto) -->
                            <div class="mt-3" id="budget_container" style="display: {{ old('budget') ? 'block' : 'none' }};">
                                <label for="budget" class="form-label">Monto del Presupuesto ($)</label>
                                <div class="input-group">
                                    <span class="input-group-text">$</span>
                                    <input type="number" step="0.01" min="0" max="9999999.99"
                                            class="form-control @error('budget') is-invalid @enderror" 
                                            id="budget" name="budget" value="{{ old('budget') }}"
                                            placeholder="0.00" oninput="if(this.value.length > 10) this.value = this.value.slice(0, 10);">
                                </div>
                                <small class="text-muted">Ingrese el valor total (Máx 7 dígitos).</small>
                                @error('budget')
                                    <div class="invalid-feedback d-block">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>


                        <!-- Descripción de Impacto -->
                        <div class="mb-3">
                            <label for="impact_description" class="form-label text-muted small fw-bold text-uppercase">Descripción del Impacto</label>
                            <textarea class="form-control @error('impact_description') is-invalid @enderror" 
                                      id="impact_description" name="impact_description" rows="2">{{ old('impact_description') }}</textarea>
                            @error('impact_description')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Archivos Adjuntos -->
                        <div class="mb-4">
                            <label class="form-label"><i class="fas fa-paperclip"></i> Archivos Adjuntos (Opcional)</label>
                            <div class="border border-2 border-dashed rounded p-4 text-center bg-light" id="createDropZone" style="cursor: pointer;">
                                <i class="fas fa-cloud-upload-alt fa-2x text-muted mb-2"></i>
                                <p class="mb-1 text-muted small">Arrastra archivos aquí o <span class="text-primary fw-bold">haz clic para seleccionar</span></p>
                                <small class="text-muted">Máximo 10MB por archivo • PDF, Imágenes, Word, Excel</small>
                                <input type="file" id="createFileInput" class="d-none" multiple 
                                       accept=".pdf,.doc,.docx,.xls,.xlsx,.png,.jpg,.jpeg,.gif">
                            </div>
                            <div id="tempFileInputs">
                                @if(old('temp_attachments'))
                                    @foreach(old('temp_attachments') as $temp)
                                        <input type="hidden" name="temp_attachments[]" value="{{ $temp }}">
                                    @endforeach
                                @endif
                            </div>
                            <div id="createFileList" class="mt-2 text-start"></div>
                        </div>

                        <!-- Botones -->
                        <div class="d-flex justify-content-end gap-2">
                            <a href="{{ route('projects.index') }}" class="btn btn-secondary">
                                <i class="fas fa-times"></i> Cancelar
                            </a>
                            <button type="submit" class="btn btn-primary" id="submitBtn">
                                <i class="fas fa-save"></i> <span class="btn-text">Guardar Proyecto</span>
                                <span class="spinner-border spinner-border-sm d-none" role="status" aria-hidden="true"></span>
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal Nueva Categoría -->
<div class="modal fade" id="createCategoryModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Nueva Categoría</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="mb-3">
                    <label class="form-label">Nombre</label>
                    <input type="text" id="new_cat_name" class="form-control">
                </div>
                <div class="mb-3">
                    <label class="form-label">Color (Opcional)</label>
                    <input type="color" id="new_cat_color" class="form-control form-control-color" value="#6c757d" title="Elige un color">
                </div>
                <div class="mb-3">
                    <label class="form-label">Descripción (Opcional)</label>
                    <textarea id="new_cat_desc" class="form-control" rows="2"></textarea>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                <button type="button" class="btn btn-primary" onclick="saveCategory()">Guardar</button>
            </div>
        </div>
    </div>
</div>

<!-- Modal de Vista Previa Genérico -->
<div class="modal fade" id="globalPreviewModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-xl modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="previewTitle">Vista Previa</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body p-0 bg-dark d-flex align-items-center justify-content-center" style="min-height: 500px;">
                <div id="previewContent" class="w-100 text-center">
                    <!-- Contenido dinámico -->
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
@php
    $oldFiles = [];
    if(old('temp_attachments')) {
        foreach(old('temp_attachments') as $value) {
            $data = json_decode($value, true);
            if ($data) {
                $oldFiles[] = [
                    'id' => $data['id'] ?? basename($data['path']),
                    'name' => $data['name'], 
                    'path' => $data['path'],
                    'type' => $data['type'] ?? 'other',
                    'size' => $data['size']
                ];
            }
        }
    }
@endphp

<script>
    window.AppConfig = {
        csrfToken: '{{ csrf_token() }}',
        formId: 'projectForm',
        routes: {
            tempUpload: '{{ route("temp.upload") }}',
            tempDelete: '{{ route("temp.delete") }}',
            categoriesStore: '{{ route("categories.store") }}'
        },
        urls: {
            storagePreview: '{{ url("storage-preview") }}'
        }
    };

    window.FileUploadConfig = {
        initialFiles: {!! json_encode($oldFiles) !!}
    };
</script>
@vite(['resources/js/pages/projects-form.js'])
@endsection
