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
                    <form action="{{ route('tasks.store') }}" method="POST" enctype="multipart/form-data">
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
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-save"></i> Guardar Tarea
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
// File Upload AJAX with Preview and Persistence
document.addEventListener('DOMContentLoaded', function() {
    const dropZone = document.getElementById('taskDropZone');
    const fileInput = document.getElementById('taskFileInput');
    const fileListContainer = document.getElementById('taskFileList');
    const tempInputsContainer = document.getElementById('tempFileInputs');
    let selectedFiles = []; // Format: { id, name, path, type, size }


    if (dropZone && fileInput) {
        dropZone.addEventListener('click', () => fileInput.click());

        dropZone.addEventListener('dragover', (e) => {
            e.preventDefault();
            dropZone.style.borderColor = '#4e73df';
            dropZone.style.backgroundColor = '#e3f2fd';
        });

        dropZone.addEventListener('dragleave', () => {
            dropZone.style.borderColor = '#dee2e6';
            dropZone.style.backgroundColor = '';
        });

        dropZone.addEventListener('drop', (e) => {
            e.preventDefault();
            dropZone.style.borderColor = '#dee2e6';
            dropZone.style.backgroundColor = '';
            handleFiles(e.dataTransfer.files);
        });

        fileInput.addEventListener('change', () => {
            handleFiles(fileInput.files);
        });

        function handleFiles(files) {
            for (let i = 0; i < files.length; i++) {
                uploadFile(files[i]);
            }
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
                        type: getFileType(file),
                        size: (file.size / 1024 / 1024).toFixed(2)
                    });
                    updateFileList();
                    updateHiddenInputs();
                } else {
                    Swal.fire('Error', data.message || 'Error al subir archivo', 'error');
                }
            })
            .catch(error => {
                removeLoadingPlaceholder(tempId);
                console.error('Error:', error);
                Swal.fire('Error', 'Error de conexión al subir el archivo', 'error');
            });
        }

        function addLoadingPlaceholder(id, name) {
            const div = document.createElement('div');
            div.id = 'loading-' + id;
            div.className = 'alert alert-info py-1 px-2 mb-1 small d-flex justify-content-between align-items-center';
            div.innerHTML = `<span><i class="fas fa-spinner fa-spin me-2"></i> Subiendo ${name}...</span>`;
            fileListContainer.appendChild(div);
        }

        function removeLoadingPlaceholder(id) {
            const el = document.getElementById('loading-' + id);
            if (el) el.remove();
        }

        function removeFile(index) {
            const file = selectedFiles[index];
            
            Swal.fire({
                title: '¿Quitar archivo?',
                text: "El archivo se eliminará del servidor temporal.",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#e74a3b',
                cancelButtonColor: '#858796',
                confirmButtonText: 'Sí, quitar',
                cancelButtonText: 'Cancelar'
            }).then((result) => {
                if (result.isConfirmed) {
                    fetch('{{ route("temp.delete") }}', {
                        method: 'DELETE',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': '{{ csrf_token() }}'
                        },
                        body: JSON.stringify({ path: file.path })
                    });

                    selectedFiles.splice(index, 1);
                    updateFileList();
                    updateHiddenInputs();
                }
            });
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

        function getFileType(file) {
            const extension = file.name.split('.').pop().toLowerCase();
            if (['jpg', 'jpeg', 'png', 'gif', 'webp'].includes(extension)) return 'image';
            if (extension === 'pdf') return 'pdf';
            if (['doc', 'docx'].includes(extension)) return 'word';
            if (['xls', 'xlsx', 'csv'].includes(extension)) return 'excel';
            return 'other';
        }

        function getFileIcon(type) {
            switch(type) {
                case 'image': return 'fas fa-file-image text-success';
                case 'pdf': return 'fas fa-file-pdf text-danger';
                case 'word': return 'fas fa-file-word text-primary';
                case 'excel': return 'fas fa-file-excel text-success';
                default: return 'fas fa-file text-secondary';
            }
        }

        function updateFileList() {
            fileListContainer.innerHTML = '';
            if (selectedFiles.length === 0) return;

            const row = document.createElement('div');
            row.className = 'row g-2';

            selectedFiles.forEach((file, index) => {
                const col = document.createElement('div');
                col.className = 'col-6 col-md-4 col-lg-3';
                
                const card = document.createElement('div');
                card.className = 'card h-100 border shadow-sm';
                
                const type = file.type;
                const isPreviewable = (type === 'image' || type === 'pdf');

                let previewHtml = '';
                if (type === 'image') {
                    const storageUrl = '{{ asset("storage") }}/' + file.path;
                    previewHtml = `<div class="preview-area d-flex align-items-center justify-content-center bg-light" style="height:80px; overflow:hidden;">
                                        <img class="img-fluid" style="width:100%; height:100%; object-fit:cover;" src="${storageUrl}">
                                   </div>`;
                } else {
                    const icon = getFileIcon(type);
                    previewHtml = `<div class="preview-area d-flex align-items-center justify-content-center bg-light" style="height:80px;">
                                        <i class="${icon} fa-2x"></i>
                                   </div>`;
                }

                card.innerHTML = `
                    ${previewHtml}
                    <div class="card-body p-2">
                        <p class="mb-0 small text-truncate" title="${file.name}">${file.name}</p>
                        <small class="text-muted">${file.size} MB</small>
                    </div>
                    <div class="card-footer p-1 d-flex justify-content-between bg-transparent">
                        <button type="button" class="btn btn-sm btn-outline-danger w-100 remove-btn" title="Eliminar"><i class="fas fa-trash me-1"></i> Quitar</button>
                    </div>
                `;
                
                col.appendChild(card);
                row.appendChild(col);

                card.querySelector('.remove-btn').addEventListener('click', () => removeFile(index));
            });

            fileListContainer.appendChild(row);
        }

        // Recover old files if any (placed after function definitions)
        @if(old('temp_attachments'))
            @php
                $oldFiles = [];
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
                    } else {
                        $oldFiles[] = [
                            'id' => basename($value),
                            'name' => basename($value), 
                            'path' => $value,
                            'type' => \Illuminate\Support\Str::endsWith($value, ['.jpg', '.jpeg', '.png', '.gif']) ? 'image' : (\Illuminate\Support\Str::endsWith($value, '.pdf') ? 'pdf' : 'other'),
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
