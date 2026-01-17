@extends('layouts.admin')

@section('title', 'Crear Tarea')

@section('contenido')
<div class="container-fluid">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card shadow">
                <div class="card-header bg-primary text-white">
                    <h4 class="mb-0"><i class="fas fa-plus-circle"></i> Crear Nueva Tarea</h4>
                </div>
                <div class="card-body">
                    <form action="{{ route('tasks.store') }}" method="POST" enctype="multipart/form-data" id="taskForm">
                        @csrf

                        <!-- Proyecto -->
                        <div class="mb-3">
                            <label for="project_id" class="form-label">Proyecto *</label>
                            <select class="form-select @error('project_id') is-invalid @enderror" 
                                    id="project_id" name="project_id">
                                <option value="">Seleccionar proyecto...</option>
                                @foreach($projects as $project)
                                    <option value="{{ $project->id }}" {{ old('project_id') == $project->id ? 'selected' : '' }}>
                                        {{ $project->title }}
                                    </option>
                                @endforeach
                            </select>
                            @error('project_id')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Título -->
                        <div class="mb-3">
                            <label for="title" class="form-label">Título de la Tarea *</label>
                            <input type="text" class="form-control @error('title') is-invalid @enderror" 
                                   id="title" name="title" value="{{ old('title') }}">
                            @error('title')
                                <div class="invalid-feedback">{{ $message }}</div>
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

                        <div class="row">
                            <!-- Asignar a -->
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Asignar a:</label>
                                
                                <div class="card p-2 bg-light border">
                                    <div class="input-group mb-2">
                                        <span class="input-group-text bg-white p-1"><i class="fas fa-search small"></i></span>
                                        <input type="text" id="assignee_search" class="form-control form-control-sm" placeholder="Buscar..." onkeyup="filterAssignees()">
                                    </div>

                                    <div id="assignee_list" style="max-height: 150px; overflow-y: auto;" class="bg-white border rounded p-2">
                                        @foreach($users as $user)
                                            <div class="form-check assignee-item">
                                                <input class="form-check-input" type="checkbox" name="assignees[]" value="{{ $user->id }}" 
                                                       id="assignee_{{ $user->id }}"
                                                       {{ (collect(old('assignees'))->contains($user->id)) ? 'checked' : '' }}>
                                                <label class="form-check-label w-100 small" for="assignee_{{ $user->id }}">
                                                    {{ $user->name }}
                                                </label>
                                            </div>
                                        @endforeach
                                    </div>
                                </div>
                                @error('assignees')
                                    <div class="invalid-feedback d-block">{{ $message }}</div>
                                @enderror
                            </div>


                            <!-- Prioridad -->
                            <div class="col-md-6 mb-3">
                                <label for="priority" class="form-label">Prioridad *</label>
                                <select class="form-select @error('priority') is-invalid @enderror" 
                                        id="priority" name="priority">
                                    <option value="">Seleccione una prioridad...</option>
                                    <option value="baja" {{ old('priority') == 'baja' ? 'selected' : '' }}>Baja</option>
                                    <option value="media" {{ old('priority') == 'media' ? 'selected' : '' }}>Media</option>
                                    <option value="alta" {{ old('priority') == 'alta' ? 'selected' : '' }}>Alta</option>
                                </select>
                                @error('priority')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <!-- Fecha Límite -->
                        <div class="mb-3">
                            <label for="due_date" class="form-label">Fecha Límite *</label>
                            <input type="date" class="form-control @error('due_date') is-invalid @enderror" 
                                   id="due_date" name="due_date" value="{{ old('due_date') }}">
                            @error('due_date')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Archivos Adjuntos -->
                        <div class="mb-4">
                            <label class="form-label"><i class="fas fa-paperclip"></i> Archivos Adjuntos (Opcional)</label>
                            <div class="border border-2 border-dashed rounded p-4 text-center bg-light" id="taskDropZone" style="cursor: pointer;">
                                <i class="fas fa-cloud-upload-alt fa-2x text-muted mb-2"></i>
                                <p class="mb-1 text-muted small">Arrastra archivos aquí o <span class="text-primary fw-bold">haz clic para seleccionar</span></p>
                                <small class="text-muted">Máximo 10MB por archivo • PDF, Imágenes, Word, Excel</small>
                                <input type="file" id="taskFileInput" class="d-none" multiple 
                                       accept=".pdf,.doc,.docx,.xls,.xlsx,.png,.jpg,.jpeg,.gif">
                            </div>
                            <div id="tempFileInputs">
                                @if(old('temp_attachments'))
                                    @foreach(old('temp_attachments') as $temp)
                                        <input type="hidden" name="temp_attachments[]" value="{{ $temp }}">
                                    @endforeach
                                @endif
                            </div>
                            <div id="taskFileList" class="mt-2 text-start"></div>
                        </div>

                        <!-- Botones -->
                        <div class="d-flex justify-content-end gap-2">
                            <a href="{{ route('tasks.index') }}" class="btn btn-secondary">
                                <i class="fas fa-times"></i> Cancelar
                            </a>
                            <button type="submit" class="btn btn-primary" id="submitBtn">
                                <i class="fas fa-save"></i> <span class="btn-text">Guardar Tarea</span>
                                <span class="spinner-border spinner-border-sm d-none" role="status" aria-hidden="true"></span>
                            </button>
                        </div>
                    </form>
                </div>
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
        formId: 'taskForm',
        routes: {
            tempUpload: '{{ route("temp.upload") }}',
            tempDelete: '{{ route("temp.delete") }}'
        },
        urls: {
            storagePreview: '{{ url("storage-preview") }}'
        }
    };

    window.FileUploadConfig = {
        initialFiles: {!! json_encode($oldFiles) !!}
    };
</script>
@vite(['resources/js/pages/tasks-form.js'])
@endsection
