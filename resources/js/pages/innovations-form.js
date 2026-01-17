document.addEventListener('DOMContentLoaded', function () {
    const input = document.getElementById('evidence_files');
    const container = document.getElementById('file-preview-container');
    const list = document.getElementById('file-list');
    const form = document.getElementById('innovationForm');

    let selectedFiles = [];

    if (input) {
        input.addEventListener('change', function () {
            if (selectedFiles.length === 0 || input.files.length > 0) {
                selectedFiles = Array.from(input.files);
            }
            renderFileList();
        });
    }

    function renderFileList() {
        if (!list) return;
        list.innerHTML = '';

        if (selectedFiles.length > 0) {
            if (container) container.style.display = 'block';

            selectedFiles.forEach((file, index) => {
                const { icon, color } = getFileInfo(file.name);
                const size = (file.size / 1024).toFixed(1) + ' KB';
                const canPreview = file.type === 'application/pdf' || file.type.startsWith('image/');
                const previewType = file.type.startsWith('image/') ? 'image' : 'pdf';
                const localUrl = URL.createObjectURL(file);

                const item = document.createElement('div');
                item.className = 'list-group-item d-flex justify-content-between align-items-center px-0 py-2 bg-transparent';
                item.innerHTML = `
                    <div class="d-flex align-items-center overflow-hidden">
                        <div class="me-2 clickable-thumbnail" style="cursor: pointer;" onclick="${canPreview ? `window.openLocalPreview('${localUrl}', '${file.name}', '${previewType}')` : ''}">
                            <i class="${icon} ${color} fa-lg"></i>
                        </div>
                        <div class="text-truncate">
                            <span class="small fw-bold d-block text-truncate text-dark">${file.name}</span>
                            <span class="text-muted" style="font-size: 0.75rem;">${size}</span>
                        </div>
                    </div>
                    <div class="d-flex align-items-center gap-1">
                        ${canPreview ? `
                        <button type="button" class="btn btn-sm btn-outline-primary" onclick="window.openLocalPreview('${localUrl}', '${file.name}', '${previewType}')" title="Vista Previa">
                            <i class="fas fa-eye"></i>
                        </button>` : ''}
                        <a href="${localUrl}" download="${file.name}" class="btn btn-sm btn-outline-secondary" title="Descargar">
                            <i class="fas fa-download"></i>
                        </a>
                        <button type="button" class="btn btn-sm text-danger" onclick="window.removeInnovationFile(${index})" title="Eliminar de la lista">
                            <i class="fas fa-times"></i>
                        </button>
                    </div>
                `;
                list.appendChild(item);
            });

            const dataTransfer = new DataTransfer();
            selectedFiles.forEach(file => dataTransfer.items.add(file));
            input.files = dataTransfer.files;

        } else {
            if (container) container.style.display = 'none';
            input.value = '';
        }
    }

    window.removeInnovationFile = function (index) {
        Swal.fire({
            title: '¿Quitar archivo?',
            text: "El archivo se eliminará de la lista de selección.",
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

    window.openLocalPreview = function (url, name, type) {
        const previewTitle = document.getElementById('previewTitle');
        const previewContent = document.getElementById('previewContent');
        const modalElement = document.getElementById('globalPreviewModal');

        if (!modalElement) return;

        const globalModal = new bootstrap.Modal(modalElement);
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
            'pdf': { icon: 'fas fa-file-pdf', color: 'text-danger' },
            'doc': { icon: 'fas fa-file-word', color: 'text-primary' },
            'docx': { icon: 'fas fa-file-word', color: 'text-primary' },
            'xls': { icon: 'fas fa-file-excel', color: 'text-success' },
            'xlsx': { icon: 'fas fa-file-excel', color: 'text-success' },
            'jpg': { icon: 'fas fa-file-image', color: 'text-success' },
            'jpeg': { icon: 'fas fa-file-image', color: 'text-success' },
            'png': { icon: 'fas fa-file-image', color: 'text-success' },
            'zip': { icon: 'fas fa-file-archive', color: 'text-muted' },
            'rar': { icon: 'fas fa-file-archive', color: 'text-muted' }
        };
        return types[ext] || { icon: 'fas fa-file', color: 'text-muted' };
    }

    // Double submission protection
    if (form) {
        form.addEventListener('submit', function () {
            const btn = document.getElementById('submitBtn');
            if (!btn) return;
            const text = btn.querySelector('.btn-text');
            const spinner = btn.querySelector('.spinner-border');

            btn.disabled = true;
            if (text) text.textContent = 'Guardando...';
            if (spinner) spinner.classList.remove('d-none');
        });
    }

    // Existing Attachments Delete (for Edit view)
    window.confirmDeleteInnovationAttachment = function (url) {
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
                const deleteForm = document.getElementById('deleteAttachmentForm');
                if (deleteForm) {
                    deleteForm.action = url;
                    deleteForm.submit();
                }
            }
        });
    }

    // Global Preview for existing attachments
    window.openGlobalPreview = function (url, name, type) {
        const previewTitle = document.getElementById('previewTitle');
        const previewContent = document.getElementById('previewContent');
        const modalElement = document.getElementById('globalPreviewModal');
        if (!modalElement) return;

        const globalModal = new bootstrap.Modal(modalElement);

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
