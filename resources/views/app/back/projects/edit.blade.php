@extends('layouts.admin')

@section('title', 'Editar Proyecto')

@section('contenido')
<div class="container-fluid">
    <div class="row justify-content-center">
        <div class="col-md-10">
            <div class="card shadow">
                <div class="card-header bg-warning text-dark">
                    <h4 class="mb-0"><i class="fas fa-edit"></i> Editar Proyecto</h4>
                </div>
                <div class="card-body">
                    <form action="{{ route('projects.update', $project) }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        @method('PUT')

                        <div class="row">
                            <!-- Título -->
                            <div class="col-md-8 mb-3">
                                <label for="title" class="form-label">Título del Proyecto *</label>
                                <input type="text" class="form-control @error('title') is-invalid @enderror" 
                                       id="title" name="title" value="{{ old('title', $project->title) }}">
                                @error('title')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <!-- Categoría -->
                            <div class="col-md-4 mb-3">
                                <label for="category_id" class="form-label">Categoría *</label>
                                <select class="form-select @error('category_id') is-invalid @enderror" 
                                        id="category_id" name="category_id">
                                    @foreach($categories as $category)
                                        <option value="{{ $category->id }}" 
                                            {{ old('category_id', $project->category_id) == $category->id ? 'selected' : '' }}>
                                            {{ $category->name }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('category_id')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <!-- Equipo de Trabajo -->
                        <div class="mb-3">
                            <label class="form-label">Equipo de Trabajo (Checklist y Búsqueda)</label>
                            
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
                                                   {{ (collect(old('team_members', $project->team->pluck('id')))->contains($user->id)) ? 'checked' : '' }}>
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

                        <script>
                        function filterUsers() {
                            const input = document.getElementById('user_search');
                            const filter = input.value.toLowerCase();
                            const list = document.getElementById('user_list');
                            const items = list.getElementsByClassName('user-item');

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

                        <!-- Descripción -->
                        <div class="mb-3">
                            <label for="description" class="form-label">Descripción *</label>
                            <textarea class="form-control @error('description') is-invalid @enderror" 
                                      id="description" name="description" rows="3">{{ old('description', $project->description) }}</textarea>
                            @error('description')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Objetivos -->
                        <div class="mb-3">
                            <label for="objectives" class="form-label">Objetivos</label>
                            <textarea class="form-control @error('objectives') is-invalid @enderror" 
                                      id="objectives" name="objectives" rows="3">{{ old('objectives', $project->objectives) }}</textarea>
                            @error('objectives')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="row">
                            <!-- Fecha Inicio -->
                            <div class="col-md-3 mb-3">
                                <label for="start_date" class="form-label">Fecha de Inicio *</label>
                                <input type="date" class="form-control @error('start_date') is-invalid @enderror" 
                                       id="start_date" name="start_date" value="{{ old('start_date', $project->start_date->format('Y-m-d')) }}">
                                @error('start_date')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <!-- Fecha Fin -->
                            <div class="col-md-3 mb-3">
                                <label for="end_date" class="form-label">Fecha de Fin *</label>
                                <input type="date" class="form-control @error('end_date') is-invalid @enderror" 
                                       id="end_date" name="end_date" value="{{ old('end_date', $project->end_date->format('Y-m-d')) }}">
                                @error('end_date')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <!--  Estado -->
                            <div class="col-md-3 mb-3">
                                <label for="status" class="form-label">Estado *</label>
                                <select class="form-select @error('status') is-invalid @enderror" 
                                        id="status" name="status">
                                    <option value="planificacion" {{ old('status', $project->status) == 'planificacion' ? 'selected' : '' }}>Planificación</option>
                                    <option value="en_progreso" {{ old('status', $project->status) == 'en_progreso' ? 'selected' : '' }}>En Progreso</option>
                                    <option value="finalizado" {{ old('status', $project->status) == 'finalizado' ? 'selected' : '' }}>Finalizado</option>
                                    <option value="en_riesgo" {{ old('status', $project->status) == 'en_riesgo' ? 'selected' : '' }}>En Riesgo</option>
                                </select>
                                @error('status')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <!-- Avance -->
                            <div class="col-md-3 mb-3">
                                <label for="completion_percentage" class="form-label">Avance (%)</label>
                                <input type="number" class="form-control bg-light" 
                                       id="completion_percentage" name="completion_percentage" 
                                       value="{{ old('completion_percentage', $project->completion_percentage) }}" readonly>
                                <small class="text-muted">Calculado automáticamente por tareas</small>
                            </div>
                        </div>

                        <!-- Opciones Financieras -->
                        <div class="mb-3 border p-3 rounded bg-light">
                            <div class="form-check form-switch">
                                <input class="form-check-input" type="checkbox" id="project_needs_budget" 
                                        {{ (old('budget', $project->budget) > 0) ? 'checked' : '' }}
                                        onchange="toggleBudget()">
                                <label class="form-check-label fw-bold" for="project_needs_budget">
                                    <i class="fas fa-coins"></i> ¿Este proyecto requiere adquisición de bienes o presupuesto?
                                </label>
                            </div>
                            
                            <!-- Presupuesto (Oculto por defecto) -->
                            <div class="mt-3" id="budget_container" style="display: {{ (old('budget', $project->budget) > 0) ? 'block' : 'none' }};">
                                <label for="budget" class="form-label">Monto del Presupuesto ($)</label>
                                <div class="input-group">
                                    <span class="input-group-text">$</span>
                                    <input type="number" step="0.01" min="0" max="9999999.99"
                                            class="form-control @error('budget') is-invalid @enderror" 
                                            id="budget" name="budget" value="{{ old('budget', $project->budget) }}"
                                            placeholder="0.00" oninput="if(this.value.length > 10) this.value = this.value.slice(0, 10);">
                                </div>
                                <small class="text-muted">Ingrese el valor total (Máx 7 dígitos).</small>
                                @error('budget')
                                    <div class="invalid-feedback d-block">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <script>
                        function toggleBudget() {
                            const checkBox = document.getElementById('project_needs_budget');
                            const container = document.getElementById('budget_container');
                            const input = document.getElementById('budget');
                            
                            if (checkBox.checked) {
                                container.style.display = 'block';
                                input.focus();
                            } else {
                                container.style.display = 'none';
                                input.value = ''; // Limpiar si se desmarca
                            }
                        }
                        </script>

                        <!-- Descripción de Impacto -->
                        <div class="mb-3">
                            <label for="impact_description" class="form-label text-muted small fw-bold text-uppercase">Descripción del Impacto</label>
                            <textarea class="form-control @error('impact_description') is-invalid @enderror" 
                                      id="impact_description" name="impact_description" rows="2">{{ old('impact_description', $project->impact_description) }}</textarea>
                            @error('impact_description')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Gestión de Archivos Adjuntos -->
                        <div class="mb-4">
                            <label class="form-label fw-bold"><i class="fas fa-paperclip"></i> Archivos Adjuntos</label>
                            
                            <!-- Archivos Actuales -->
                            @if($project->attachments->count() > 0)
                                <div class="mb-3">
                                    <p class="small text-muted mb-1">Archivos actuales:</p>
                                    <div class="row g-2" id="currentAttachments">
                                        @foreach($project->attachments as $attachment)
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
                            <a href="{{ route('projects.index') }}" class="btn btn-secondary">
                                <i class="fas fa-times"></i> Cancelar
                            </a>
                            <button type="submit" class="btn btn-warning">
                                <i class="fas fa-save"></i> Actualizar Proyecto
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
