@extends('layouts.admin')

@section('title', 'Editar Innovación')

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

                        <!-- Impacto -->
                        <div class="mb-3">
                            <label for="impact_score" class="form-label">Puntuación de Impacto (1-10) *</label>
                            <input type="number" min="1" max="10" class="form-control @error('impact_score') is-invalid @enderror" 
                                   id="impact_score" name="impact_score" value="{{ old('impact_score', $innovation->impact_score) }}">
                            @error('impact_score')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
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
<script>
    function confirmDeleteAttachment(url) {
        Swal.fire({
            title: '¿Eliminar este archivo?',
            text: "Esta acción no se puede deshacer.",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#e74a3b',
            cancelButtonColor: '#858796',
            confirmButtonText: 'Sí, eliminar',
            cancelButtonText: 'Cancelar'
        }).then((result) => {
            if (result.isConfirmed) {
                const form = document.getElementById('deleteAttachmentForm');
                form.action = url;
                form.submit();
            }
        });
    }

    let selectedFiles = [];

    function previewFiles() {
        const input = document.getElementById('evidence_files');
        const container = document.getElementById('file-preview-container');
        const list = document.getElementById('file-list');
        
        // Inicializar array si es la primera vez
        if (selectedFiles.length === 0 || input.files.length > 0) {
            selectedFiles = Array.from(input.files);
        }
        
        renderFileList();
    }

    function renderFileList() {
        const container = document.getElementById('file-preview-container');
        const list = document.getElementById('file-list');
        const input = document.getElementById('evidence_files');
        
        list.innerHTML = '';
        
        if (selectedFiles.length > 0) {
            container.style.display = 'block';
            
            selectedFiles.forEach((file, index) => {
                const { icon, color } = getFileInfo(file.name);
                const size = (file.size / 1024).toFixed(1) + ' KB';
                const canPreview = file.type === 'application/pdf' || file.type.startsWith('image/');
                const previewType = file.type.startsWith('image/') ? 'image' : 'pdf';
                
                // Crear URL temporal para previsualización local
                const localUrl = URL.createObjectURL(file);
                
                const item = document.createElement('div');
                item.className = 'list-group-item d-flex justify-content-between align-items-center px-0 py-2 bg-transparent';
                item.innerHTML = `
                    <div class="d-flex align-items-center overflow-hidden">
                        <div class="me-2 clickable-thumbnail" style="cursor: pointer;" onclick="${canPreview ? `openLocalPreview('${localUrl}', '${file.name}', '${previewType}')` : ''}">
                            <i class="${icon} ${color} fa-lg"></i>
                        </div>
                        <div class="text-truncate">
                            <span class="small fw-bold d-block text-truncate text-dark">${file.name}</span>
                            <span class="text-muted" style="font-size: 0.75rem;">${size}</span>
                        </div>
                    </div>
                    <div class="d-flex align-items-center gap-1">
                        ${canPreview ? `
                        <button type="button" class="btn btn-sm btn-outline-primary" onclick="openLocalPreview('${localUrl}', '${file.name}', '${previewType}')" title="Vista Previa">
                            <i class="fas fa-eye"></i>
                        </button>` : ''}
                        <a href="${localUrl}" download="${file.name}" class="btn btn-sm btn-outline-secondary" title="Descargar">
                            <i class="fas fa-download"></i>
                        </a>
                        <button type="button" class="btn btn-sm text-danger" onclick="removeFile(${index})" title="Eliminar de la lista">
                            <i class="fas fa-times"></i>
                        </button>
                    </div>
                `;
                list.appendChild(item);
            });
            
            // Sincronizar el input de archivos real (esto es necesario para que el form envíe lo correcto)
            const dataTransfer = new DataTransfer();
            selectedFiles.forEach(file => dataTransfer.items.add(file));
            input.files = dataTransfer.files;

        } else {
            container.style.display = 'none';
            input.value = ''; // Limpiar el input si no hay archivos
        }
    }

    function removeFile(index) {
        Swal.fire({
            title: '¿Quitar archivo?',
            text: "El archivo se eliminará de la lista de nuevos archivos.",
            icon: 'info',
            showCancelButton: true,
            confirmButtonColor: '#4e73df',
            cancelButtonColor: '#858796',
            confirmButtonText: 'Sí, quitar',
            cancelButtonText: 'Cancelar'
        }).then((result) => {
            if (result.isConfirmed) {
                selectedFiles.splice(index, 1);
                renderFileList();
            }
        });
    }

    function openLocalPreview(url, name, type) {
        // Usamos el mismo modal global para consistencia
        const previewTitle = document.getElementById('previewTitle');
        const previewContent = document.getElementById('previewContent');
        const globalModal = new bootstrap.Modal(document.getElementById('globalPreviewModal'));

        previewTitle.textContent = name;
        previewContent.innerHTML = '';

        if (type === 'image') {
            const img = document.createElement('img');
            img.src = url;
            img.className = 'img-fluid shadow-sm';
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
    }

    function getFileInfo(filename) {
        const ext = filename.split('.').pop().toLowerCase();
        const types = {
            'pdf':  { icon: 'fas fa-file-pdf', color: 'text-danger' },
            'doc':  { icon: 'fas fa-file-word', color: 'text-primary' },
            'docx': { icon: 'fas fa-file-word', color: 'text-primary' },
            'xls':  { icon: 'fas fa-file-excel', color: 'text-success' },
            'xlsx': { icon: 'fas fa-file-excel', color: 'text-success' },
            'jpg':  { icon: 'fas fa-file-image', color: 'text-success' },
            'jpeg': { icon: 'fas fa-file-image', color: 'text-success' },
            'png':  { icon: 'fas fa-file-image', color: 'text-success' },
            'zip':  { icon: 'fas fa-file-archive', color: 'text-muted' },
            'rar':  { icon: 'fas fa-file-archive', color: 'text-muted' }
        };
        return types[ext] || { icon: 'fas fa-file', color: 'text-muted' };
    }

document.addEventListener('DOMContentLoaded', function() {
    // Vista Previa Global
    const globalModal = new bootstrap.Modal(document.getElementById('globalPreviewModal'));
    const previewTitle = document.getElementById('previewTitle');
    const previewContent = document.getElementById('previewContent');

    window.openGlobalPreview = function(url, name, type) {
        previewTitle.textContent = name;
        previewContent.innerHTML = '<div class="spinner-border text-primary" role="status"></div>';

        if (type === 'image') {
            const img = document.createElement('img');
            img.src = url;
            img.className = 'img-fluid shadow-sm';
            img.style.maxHeight = '80vh';
            img.onload = () => { previewContent.innerHTML = ''; previewContent.appendChild(img); };
            img.onerror = () => { previewContent.innerHTML = '<div class="p-5">Error al cargar la imagen.</div>'; };
        } else if (type === 'pdf') {
            const iframe = document.createElement('iframe');
            iframe.src = url;
            iframe.style.width = '100%';
            iframe.style.height = '80vh';
            iframe.style.border = 'none';
            previewContent.innerHTML = '';
            previewContent.appendChild(iframe);
        } else {
            previewContent.innerHTML = '<div class="p-5">Este archivo no se puede previsualizar. Por favor, descárgalo.</div>';
        }

        globalModal.show();
    };
});
</script>

<script>
// Double submission protection
document.getElementById('innovationForm').addEventListener('submit', function() {
    const btn = document.getElementById('submitBtn');
    const text = btn.querySelector('.btn-text');
    const spinner = btn.querySelector('.spinner-border');
    
    btn.disabled = true;
    text.textContent = 'Actualizando...';
    spinner.classList.remove('d-none');
});
</script>
<style>
    .list-group-sm .list-group-item {
        padding: 0.5rem 0.75rem;
        font-size: 0.85rem;
    }
    .x-small {
        font-size: 0.75rem;
    }
    .clickable-thumbnail:hover {
        opacity: 0.8;
    }
</style>
@endpush
