{{-- Attachments Section - Can be used for Projects and Tasks --}}
@php
    $attachableType = isset($project) ? 'project' : 'task';
    $attachableId = isset($project) ? $project->id : $task->id;
    $attachable = isset($project) ? $project : $task;
@endphp

<div class="card shadow mb-4">
    <div class="card-header bg-dark text-white d-flex justify-content-between align-items-center">
        <h5 class="mb-0"><i class="fas fa-paperclip"></i> Archivos Adjuntos</h5>
        <span class="badge bg-light text-dark">{{ $attachable->attachments->count() }}</span>
    </div>
    <div class="card-body">
        <!-- Upload Zone -->
        <div class="mb-4">
            <form action="{{ route('attachments.store', [$attachableType, $attachableId]) }}" 
                  method="POST" 
                  enctype="multipart/form-data"
                  id="attachmentForm">
                @csrf
                <div class="border border-2 border-dashed rounded p-4 text-center bg-light" 
                     id="dropZone"
                     style="cursor: pointer;">
                    <i class="fas fa-cloud-upload-alt fa-3x text-muted mb-3"></i>
                    <p class="mb-2 text-muted">Arrastra archivos aquí o <span class="text-primary fw-bold">haz clic para seleccionar</span></p>
                    <small class="text-muted">Máximo 10MB por archivo • PDF, Imágenes, Word, Excel</small>
                    <input type="file" name="files[]" id="fileInput" class="d-none" multiple 
                           accept=".pdf,.doc,.docx,.xls,.xlsx,.png,.jpg,.jpeg,.gif">
                </div>
                <div id="fileList" class="mt-3"></div>
                <button type="submit" id="uploadBtn" class="btn btn-primary mt-2 d-none">
                    <i class="fas fa-upload"></i> Subir Archivos
                </button>
            </form>
        </div>

        <!-- Files List -->
        @if($attachable->attachments->count() > 0)
            <div class="row g-3">
                @foreach($attachable->attachments as $attachment)
                    <div class="col-md-6 col-lg-4">
                        <div class="card h-100 border shadow-sm">
                            <!-- Preview Area -->
                            <div class="card-img-top bg-light d-flex align-items-center justify-content-center" 
                                 style="height: 120px; cursor: pointer;"
                                 @if($attachment->isImage() || $attachment->isPdf())
                                 data-bs-toggle="modal" data-bs-target="#previewModal{{ $attachment->id }}"
                                 @endif>
                                @if($attachment->isImage())
                                    <img src="{{ $attachment->url }}" alt="{{ $attachment->original_name }}" 
                                         class="img-fluid" style="max-height: 120px; object-fit: cover;">
                                @else
                                    <i class="{{ $attachment->icon }} fa-4x"></i>
                                @endif
                            </div>
                            
                            <div class="card-body py-2 px-3">
                                <p class="mb-1 small text-truncate fw-medium" title="{{ $attachment->original_name }}">
                                    {{ $attachment->original_name }}
                                </p>
                                <small class="text-muted">{{ $attachment->human_size }}</small>
                            </div>
                            
                            <div class="card-footer bg-transparent py-2 d-flex justify-content-between">
                                <a href="{{ route('attachments.download', $attachment) }}" 
                                   class="btn btn-sm btn-outline-primary" title="Descargar">
                                    <i class="fas fa-download"></i>
                                </a>
                                @if($attachment->isImage() || $attachment->isPdf())
                                <button class="btn btn-sm btn-outline-secondary" 
                                        data-bs-toggle="modal" data-bs-target="#previewModal{{ $attachment->id }}" 
                                        title="Vista Previa">
                                    <i class="fas fa-eye"></i>
                                </button>
                                @endif
                                @if(Auth::id() == $attachment->uploaded_by || Auth::user()->hasRole('admin'))
                                <form action="{{ route('attachments.destroy', $attachment) }}" method="POST" class="d-inline delete-attachment-form">
                                    @csrf
                                    @method('DELETE')
                                    <button type="button" class="btn btn-sm btn-outline-danger btn-delete-attachment" title="Eliminar">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </form>
                                @endif
                            </div>
                        </div>
                    </div>

                    <!-- Preview Modal -->
                    @if($attachment->isImage() || $attachment->isPdf())
                    <div class="modal fade" id="previewModal{{ $attachment->id }}" tabindex="-1">
                        <div class="modal-dialog modal-xl modal-dialog-centered">
                            <div class="modal-content">
                                <div class="modal-header">
                                    <h5 class="modal-title">
                                        <i class="{{ $attachment->icon }} me-2"></i>{{ $attachment->original_name }}
                                    </h5>
                                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                </div>
                                <div class="modal-body p-0 text-center bg-dark">
                                    @if($attachment->isImage())
                                        <img src="{{ $attachment->url }}" alt="{{ $attachment->original_name }}" 
                                             class="img-fluid" style="max-height: 80vh;">
                                    @elseif($attachment->isPdf())
                                        <iframe src="{{ $attachment->url }}" 
                                                style="width: 100%; height: 80vh; border: none;"></iframe>
                                    @endif
                                </div>
                                <div class="modal-footer">
                                    <a href="{{ route('attachments.download', $attachment) }}" class="btn btn-primary">
                                        <i class="fas fa-download"></i> Descargar
                                    </a>
                                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>
                                </div>
                            </div>
                        </div>
                    </div>
                    @endif
                @endforeach
            </div>
        @else
            <div class="text-center py-4">
                <i class="fas fa-folder-open fa-3x text-muted mb-3 opacity-50"></i>
                <p class="text-muted mb-0">No hay archivos adjuntos.</p>
                <small class="text-muted">Sube archivos usando el área de arriba.</small>
            </div>
        @endif
    </div>
</div>

@push('styles')
    <link rel="stylesheet" href="{{ asset('assets/back/css/components/attachments.css') }}">
@endpush

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const dropZone = document.getElementById('dropZone');
    const fileInput = document.getElementById('fileInput');
    const fileList = document.getElementById('fileList');
    const uploadBtn = document.getElementById('uploadBtn');

    // Click to select files
    dropZone.addEventListener('click', () => fileInput.click());

    // Drag and drop events
    dropZone.addEventListener('dragover', (e) => {
        e.preventDefault();
        dropZone.classList.add('dragover');
    });

    dropZone.addEventListener('dragleave', () => {
        dropZone.classList.remove('dragover');
    });

    dropZone.addEventListener('drop', (e) => {
        e.preventDefault();
        dropZone.classList.remove('dragover');
        fileInput.files = e.dataTransfer.files;
        updateFileList();
    });

    // File input change
    fileInput.addEventListener('change', updateFileList);

    function updateFileList() {
        const files = fileInput.files;
        fileList.innerHTML = '';
        
        if (files.length > 0) {
            uploadBtn.classList.remove('d-none');
            
            for (let i = 0; i < files.length; i++) {
                const file = files[i];
                const size = (file.size / 1024 / 1024).toFixed(2);
                const item = document.createElement('div');
                item.className = 'badge bg-light text-dark border me-2 mb-2 p-2';
                item.innerHTML = `<i class="fas fa-file me-1"></i> ${file.name} <small class="text-muted">(${size}MB)</small>`;
                fileList.appendChild(item);
            }
        } else {
            uploadBtn.classList.add('d-none');
        }
    }

    // Handle delete confirmation for existing files
    document.querySelectorAll('.btn-delete-attachment').forEach(button => {
        button.addEventListener('click', function() {
            const form = this.closest('form');
            Swal.fire({
                title: '¿Eliminar archivo?',
                text: "Esta acción no se puede deshacer.",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#e74a3b',
                cancelButtonColor: '#858796',
                confirmButtonText: 'Sí, eliminar',
                cancelButtonText: 'Cancelar'
            }).then((result) => {
                if (result.isConfirmed) {
                    form.submit();
                }
            });
        });
    });
});
</script>
@endpush
