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
<script>
function saveCategory() {
    const name = document.getElementById('new_cat_name').value;
    const color = document.getElementById('new_cat_color').value;
    const desc = document.getElementById('new_cat_desc').value;

    if(!name) {
        Swal.fire('Error', 'El nombre es obligatorio', 'error');
        return;
    }

    fetch('{{ route("categories.store") }}', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'Accept': 'application/json',
            'X-CSRF-TOKEN': '{{ csrf_token() }}'
        },
        body: JSON.stringify({ name: name, color: color, description: desc })
    })
    .then(response => {
        if (!response.ok) {
            return response.json().then(err => Promise.reject(err));
        }
        return response.json();
    })
    .then(data => {
        if(data.success) {
            // Agregar al select
            const select = document.getElementById('category_id');
            const option = new Option(data.category.name, data.category.id);
            select.add(option);
            select.value = data.category.id;
            
            // Cerrar modal
            const modal = bootstrap.Modal.getInstance(document.getElementById('createCategoryModal'));
            modal.hide();
            
            // Limpiar inputs
            document.getElementById('new_cat_name').value = '';
            document.getElementById('new_cat_desc').value = '';
            
            Swal.fire('Éxito', 'Categoría creada correctamente', 'success');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        let errorMessage = 'Ocurrió un error al guardar';
        
        // Si hay errores de validación de Laravel
        if (error.errors) {
            const errors = Object.values(error.errors).flat();
            errorMessage = errors.join('\n');
        } else if (error.message) {
            errorMessage = error.message;
        }
        
        Swal.fire('Error', errorMessage, 'error');
    });
}

// Función para eliminar categoría
function deleteCategory(categoryId, categoryName, projectCount) {
    if (projectCount > 0) {
        Swal.fire({
            title: 'No se puede eliminar',
            text: `La categoría "${categoryName}" tiene ${projectCount} proyecto(s) asociado(s). Elimina o cambia la categoría de esos proyectos primero.`,
            icon: 'warning',
            confirmButtonColor: '#4e73df',
            confirmButtonText: 'Entendido'
        });
        return;
    }

    Swal.fire({
        title: `¿Eliminar categoría "${categoryName}"?`,
        text: "Esta acción no se puede deshacer.",
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#e74a3b',
        cancelButtonColor: '#858796',
        confirmButtonText: 'Sí, eliminar',
        cancelButtonText: 'Cancelar'
    }).then((result) => {
        if (result.isConfirmed) {
            const token = document.querySelector('input[name="_token"]').value;
            
            fetch(`/categories/${categoryId}`, {
                method: 'DELETE',
                headers: {
                    'X-CSRF-TOKEN': token,
                    'Accept': 'application/json'
                }
            })
            .then(response => {
                 if (!response.ok) {
                    return response.json().then(err => Promise.reject(err));
                }
                return response.json();
            })
            .then(data => {
                if (data.success) {
                    // Remover del select de manera visual si existe
                    const select = document.getElementById('category_id');
                    const option = select.querySelector(`option[value="${categoryId}"]`);
                    if (option) option.remove();
                    
                    Swal.fire('Eliminado', data.message, 'success').then(() => {
                        window.location.reload(); // Recargar para actualizar la lista de gestión
                    });
                }
            })
            .catch(error => {
                console.error('Error:', error);
                const errorMessage = error.message || 'Error al eliminar la categoría';
                Swal.fire('Error', errorMessage, 'error');
            });
        }
    });
}

// File Upload AJAX with Preview and Persistence
document.addEventListener('DOMContentLoaded', function() {
    const dropZone = document.getElementById('createDropZone');
    const fileInput = document.getElementById('createFileInput');
    const fileListContainer = document.getElementById('createFileList');
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

            // Show loading state in list maybe?
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
                    // Call server to delete temp file
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
                // Store metadata as JSON to preserve name/size across validation errors
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
            const fileName = file.name || file.path || '';
            const extension = fileName.split('.').pop().toLowerCase();
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
                const storageUrl = '{{ url("storage-preview") }}/' + file.path;
                
                const onclickAction = isPreviewable ? `onclick="openGlobalPreview('${storageUrl}', '${file.name}', '${type}')" style="cursor:pointer;"` : '';

                if (type === 'image') {
                    previewHtml = `<div class="preview-area d-flex align-items-center justify-content-center bg-light" style="height:80px; overflow:hidden;" ${onclickAction}>
                                        <img class="img-fluid" style="width:100%; height:100%; object-fit:cover;" src="${storageUrl}">
                                   </div>`;
                } else {
                    const icon = getFileIcon(type);
                    previewHtml = `<div class="preview-area d-flex align-items-center justify-content-center bg-light" style="height:80px;" ${onclickAction}>
                                        <i class="${icon} fa-2x"></i>
                                   </div>`;
                }

                card.innerHTML = `
                    ${previewHtml}
                    <div class="card-body p-2 text-center overflow-hidden">
                        <p class="mb-1 small text-truncate fw-bold" title="${file.name}">${file.name}</p>
                        <div class="d-flex justify-content-between align-items-center">
                            <small class="text-muted">${file.size} MB</small>
                            <div class="d-flex gap-1">
                                ${isPreviewable ? `
                                <button type="button" class="btn btn-sm btn-outline-info p-0 js-preview-btn" style="width:28px; height:28px;" title="Vista Previa">
                                    <i class="fas fa-eye"></i>
                                </button>
                                ` : ''}
                                <button type="button" class="btn btn-sm btn-outline-danger p-0 remove-btn" style="width:28px; height:28px;" title="Quitar">
                                    <i class="fas fa-trash"></i>
                                </button>
                            </div>
                        </div>
                    </div>
                `;
                
                if (isPreviewable) {
                    card.querySelector('.js-preview-btn').addEventListener('click', () => {
                        openGlobalPreview(storageUrl, file.name, type);
                    });
                }
                
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
                        // Fallback for plain paths if any
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

    // 3. Lógica del Modal de Vista Previa
    const globalModal = new bootstrap.Modal(document.getElementById('globalPreviewModal'));
    const previewTitle = document.getElementById('previewTitle');
    const previewContent = document.getElementById('previewContent');

    window.openGlobalPreview = function(url, name, type) {
        previewTitle.textContent = name;
        previewContent.innerHTML = '';

        if (type === 'image') {
            const img = document.createElement('img');
            img.src = url;
            img.className = 'img-fluid';
            img.style.maxHeight = '80vh';
            previewContent.appendChild(img);
        } else if (type === 'pdf') {
            const iframe = document.createElement('iframe');
            iframe.src = url;
            iframe.style.width = '100%';
            iframe.style.height = '80vh';
            iframe.style.border = 'none';
            previewContent.appendChild(iframe);
        }

        globalModal.show();
    };
});

// Double submission protection
document.getElementById('projectForm').addEventListener('submit', function() {
    const btn = document.getElementById('submitBtn');
    const text = btn.querySelector('.btn-text');
    const spinner = btn.querySelector('.spinner-border');
    
    btn.disabled = true;
    text.textContent = 'Guardando...';
    spinner.classList.remove('d-none');
});
</script>
@endsection
