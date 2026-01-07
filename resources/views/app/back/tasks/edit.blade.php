@extends('layouts.admin')

@section('title', 'Editar Tarea')

@section('contenido')
<div class="container-fluid">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card shadow">
                <div class="card-header bg-warning text-dark">
                    <h4 class="mb-0"><i class="fas fa-edit"></i> Editar Tarea</h4>
                </div>
                <div class="card-body">
                    <form action="{{ route('tasks.update', $task) }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        @method('PUT')

                        <!-- Proyecto -->
                        <div class="mb-3">
                            <label for="project_id" class="form-label">Proyecto *</label>
                            <select class="form-select @error('project_id') is-invalid @enderror" 
                                    id="project_id" name="project_id">
                                @foreach($projects as $project)
                                    <option value="{{ $project->id }}" 
                                        {{ old('project_id', $task->project_id) == $project->id ? 'selected' : '' }}>
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
                                   id="title" name="title" value="{{ old('title', $task->title) }}">
                            @error('title')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Descripción -->
                        <div class="mb-3">
                            <label for="description" class="form-label">Descripción *</label>
                            <textarea class="form-control @error('description') is-invalid @enderror" 
                                      id="description" name="description" rows="3">{{ old('description', $task->description) }}</textarea>
                            @error('description')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="row">
                            <!-- Asignar a -->
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Asignar a (Checklist y Búsqueda)</label>
                                
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
                                                       {{ (collect(old('assignees', $task->assignees->pluck('id')))->contains($user->id)) ? 'checked' : '' }}>
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

                            <script>
                            function filterAssignees() {
                                const input = document.getElementById('assignee_search');
                                const filter = input.value.toLowerCase();
                                const list = document.getElementById('assignee_list');
                                const items = list.getElementsByClassName('assignee-item');

                                for (let i = 0; i < items.length; i++) {
                                    const label = items[i].getElementsByTagName('label')[0];
                                    const txtValue = label.textContent || label.innerText;
                                    if (txtValue.toLowerCase().indexOf(filter) > -1) {
                                        items[i].style.display = "";
                                    } else {
                                        items[i].style.display = "none";
                                    }
                                }
                            }
                            </script>

                            <!-- Estado -->
                            <div class="col-md-6 mb-3">
                                <label for="status" class="form-label">Estado *</label>
                                <select class="form-select @error('status') is-invalid @enderror" 
                                        id="status" name="status">
                                    <option value="pendiente" {{ old('status', $task->status) == 'pendiente' ? 'selected' : '' }}>Pendiente</option>
                                    <option value="en_progreso" {{ old('status', $task->status) == 'en_progreso' ? 'selected' : '' }}>En Progreso</option>
                                    <option value="completada" {{ old('status', $task->status) == 'completada' ? 'selected' : '' }}>Completada</option>
                                    <option value="atrasada" {{ old('status', $task->status) == 'atrasada' ? 'selected' : '' }}>Atrasada</option>
                                </select>
                                @error('status')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="row">
                            <!-- Prioridad -->
                            <div class="col-md-6 mb-3">
                                <label for="priority" class="form-label">Prioridad *</label>
                                <select class="form-select @error('priority') is-invalid @enderror" 
                                        id="priority" name="priority">
                                    <option value="">Seleccione una prioridad...</option>
                                    <option value="baja" {{ old('priority', $task->priority) == 'baja' ? 'selected' : '' }}>Baja</option>
                                    <option value="media" {{ old('priority', $task->priority) == 'media' ? 'selected' : '' }}>Media</option>
                                    <option value="alta" {{ old('priority', $task->priority) == 'alta' ? 'selected' : '' }}>Alta</option>
                                </select>
                                @error('priority')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <!-- Fecha Límite -->
                            <div class="col-md-6 mb-3">
                                <label for="due_date" class="form-label">Fecha Límite *</label>
                                <input type="date" class="form-control @error('due_date') is-invalid @enderror" 
                                       id="due_date" name="due_date" value="{{ old('due_date', $task->due_date->format('Y-m-d')) }}">
                                @error('due_date')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <!-- Gestión de Archivos Adjuntos -->
                        <div class="mb-4">
                            <label class="form-label fw-bold"><i class="fas fa-paperclip"></i> Archivos Adjuntos</label>
                            
                            <!-- Archivos Actuales -->
                            @if($task->attachments->count() > 0)
                                <div class="mb-3">
                                    <p class="small text-muted mb-1">Archivos actuales:</p>
                                    <div class="row g-2" id="currentAttachments">
                                        @foreach($task->attachments as $attachment)
                                            <div class="col-md-6 col-lg-4 attachment-item" id="attachment-{{ $attachment->id }}">
                                                <div class="card h-100 border shadow-sm">
                                                    <div class="card-body p-2 d-flex align-items-center">
                                                        <div class="me-2">
                                                            @if(Str::startsWith($attachment->mime_type, 'image/'))
                                                                <i class="fas fa-file-image text-success fa-lg"></i>
                                                            @elseif($attachment->mime_type === 'application/pdf')
                                                                <i class="fas fa-file-pdf text-danger fa-lg"></i>
                                                            @else
                                                                <i class="fas fa-file-alt text-primary fa-lg"></i>
                                                            @endif
                                                        </div>
                                                        <div class="flex-grow-1 overflow-hidden">
                                                            <p class="small mb-0 text-truncate" title="{{ $attachment->original_name }}">
                                                                {{ $attachment->original_name }}
                                                            </p>
                                                            <small class="text-muted">{{ number_format($attachment->size / 1024 / 1024, 2) }} MB</small>
                                                        </div>
                                                        <button type="button" class="btn btn-sm btn-link text-danger delete-attachment" 
                                                                data-id="{{ $attachment->id }}" title="Eliminar">
                                                            <i class="fas fa-trash"></i>
                                                        </button>
                                                    </div>
                                                </div>
                                            </div>
                                        @endforeach
                                    </div>
                                </div>
                            @endif

                            <!-- Carga de Nuevos Archivos -->
                            <div class="border border-2 border-dashed rounded p-4 text-center bg-light" id="editDropZone" style="cursor: pointer;">
                                <i class="fas fa-cloud-upload-alt fa-2x text-muted mb-2"></i>
                                <p class="mb-1 text-muted small">Subir nuevos archivos: Arrastra aquí o <span class="text-primary fw-bold">clic para seleccionar</span></p>
                                <small class="text-muted">Máximo 10MB por archivo</small>
                                <input type="file" id="editFileInput" class="d-none" multiple>
                            </div>
                            <div id="tempFileInputs">
                                @if(old('temp_attachments'))
                                    @foreach(old('temp_attachments') as $temp)
                                        <input type="hidden" name="temp_attachments[]" value="{{ $temp }}">
                                    @endforeach
                                @endif
                            </div>
                            <div id="editFileList" class="mt-2 text-start"></div>
                        </div>

                        <!-- Botones -->
                        <div class="d-flex justify-content-end gap-2">
                            <a href="{{ route('tasks.index') }}" class="btn btn-secondary">
                                <i class="fas fa-times"></i> Cancelar
                            </a>
                            <button type="submit" class="btn btn-warning">
                                <i class="fas fa-save"></i> Actualizar Tarea
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    // 1. Manejo de Borrado de Adjuntos Existentes
    const deleteButtons = document.querySelectorAll('.delete-attachment');
    deleteButtons.forEach(btn => {
        btn.addEventListener('click', function() {
            const id = this.dataset.id;
            Swal.fire({
                title: '¿Eliminar archivo permanentemente?',
                text: "Esta acción no se puede deshacer.",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#d33',
                cancelButtonColor: '#858796',
                confirmButtonText: 'Sí, eliminar',
                cancelButtonText: 'Cancelar'
            }).then((result) => {
                if (result.isConfirmed) {
                    fetch(`/attachments/${id}`, {
                        method: 'DELETE',
                        headers: {
                            'X-CSRF-TOKEN': '{{ csrf_token() }}',
                            'Accept': 'application/json'
                        }
                    })
                    .then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            document.getElementById(`attachment-${id}`).remove();
                            Swal.fire('Eliminado', data.message, 'success');
                        } else {
                            Swal.fire('Error', data.error || 'No se pudo eliminar', 'error');
                        }
                    });
                }
            });
        });
    });

    // 2. Manejo de Nuevos Archivos (AJAX similar a create)
    const dropZone = document.getElementById('editDropZone');
    const fileInput = document.getElementById('editFileInput');
    const fileListContainer = document.getElementById('editFileList');
    const tempInputsContainer = document.getElementById('tempFileInputs');
    let selectedFiles = [];


    if (dropZone && fileInput) {
        dropZone.addEventListener('click', () => fileInput.click());
        dropZone.addEventListener('dragover', (e) => { e.preventDefault(); dropZone.classList.add('bg-primary', 'bg-opacity-10'); });
        dropZone.addEventListener('dragleave', () => { dropZone.classList.remove('bg-primary', 'bg-opacity-10'); });
        dropZone.addEventListener('drop', (e) => {
            e.preventDefault();
            dropZone.classList.remove('bg-primary', 'bg-opacity-10');
            handleFiles(e.dataTransfer.files);
        });
        fileInput.addEventListener('change', () => handleFiles(fileInput.files));

        function handleFiles(files) {
            for (let i = 0; i < files.length; i++) uploadFile(files[i]);
        }

        function uploadFile(file) {
            const formData = new FormData();
            formData.append('file', file);
            formData.append('_token', '{{ csrf_token() }}');

            const tempId = Math.random().toString(36).substring(7);
            addLoadingPlaceholder(tempId, file.name);

            fetch('{{ route("temp.upload") }}', {
                method: 'POST',
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                removeLoadingPlaceholder(tempId);
                if (data.success) {
                    selectedFiles.push({
                        id: data.id,
                        name: data.name,
                        path: data.path,
                        size: (file.size / 1024 / 1024).toFixed(2)
                    });
                    updateFileList();
                    updateHiddenInputs();
                }
            })
            .catch(() => removeLoadingPlaceholder(tempId));
        }

        function addLoadingPlaceholder(id, name) {
            const div = document.createElement('div');
            div.id = 'loading-' + id;
            div.className = 'alert alert-info py-1 px-2 mb-1 small';
            div.innerHTML = `<i class="fas fa-spinner fa-spin me-2"></i> Subiendo ${name}...`;
            fileListContainer.appendChild(div);
        }

        function removeLoadingPlaceholder(id) {
            const el = document.getElementById('loading-' + id);
            if (el) el.remove();
        }

        function updateHiddenInputs() {
            tempInputsContainer.innerHTML = '';
            selectedFiles.forEach(file => {
                const input = document.createElement('input');
                input.type = 'hidden';
                input.name = 'temp_attachments[]';
                input.value = JSON.stringify({
                    path: file.path,
                    name: file.name,
                    size: file.size,
                    type: file.type
                });
                tempInputsContainer.appendChild(input);
            });
        }

        function updateFileList() {
            fileListContainer.innerHTML = '';
            if (selectedFiles.length === 0) return;

            const row = document.createElement('div');
            row.className = 'row g-2';

            selectedFiles.forEach((file, index) => {
                const col = document.createElement('div');
                col.className = 'col-6 col-md-4';
                col.innerHTML = `
                    <div class="card h-100 border shadow-sm">
                        <div class="card-body p-2 overflow-hidden">
                            <p class="mb-0 small text-truncate" title="${file.name}">${file.name}</p>
                            <small class="text-muted">${file.size} MB</small>
                            <button type="button" class="btn btn-sm btn-outline-danger mt-1 w-100" onclick="removeTemp(${index})">Quitar</button>
                        </div>
                    </div>
                `;
                row.appendChild(col);
            });
            fileListContainer.appendChild(row);
        }

        window.removeTemp = function(index) {
            selectedFiles.splice(index, 1);
            updateFileList();
            updateHiddenInputs();
        };

        // Recover old files if any (placed after function definitions)
        @if(old('temp_attachments'))
            @php
                $oldFiles = [];
                foreach(old('temp_attachments') as $value) {
                    $basePath = is_string($value) ? $value : '';
                    $data = json_decode($basePath, true);
                    
                    if ($data) {
                        $oldFiles[] = [
                            'id' => $data['id'] ?? basename($data['path']),
                            'name' => $data['name'], 
                            'path' => $data['path'],
                            'type' => $data['type'] ?? 'other',
                            'size' => $data['size']
                        ];
                    } else {
                        $oldFiles[] = [
                            'id' => basename($basePath),
                            'name' => basename($basePath), 
                            'path' => $basePath,
                            'type' => \Illuminate\Support\Str::endsWith($basePath, ['.jpg', '.jpeg', '.png', '.gif']) ? 'image' : (\Illuminate\Support\Str::endsWith($basePath, '.pdf') ? 'pdf' : 'other'),
                            'size' => '?'
                        ];
                    }
                }
            @endphp
            selectedFiles = {!! json_encode($oldFiles) !!};
            updateFileList();
            updateHiddenInputs();
        @endif
    }
});
</script>
@endsection
