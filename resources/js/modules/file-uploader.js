/**
 * Shared File Upload Manager
 */

export function initFileUploader(config) {
    const {
        dropZoneId,
        fileInputId,
        fileListContainerId,
        tempInputsContainerId,
        csrfToken,
        uploadRoute,
        deleteRoute,
        storagePreviewUrl,
        initialFiles = []
    } = config;

    const dropZone = document.getElementById(dropZoneId);
    const fileInput = document.getElementById(fileInputId);
    const fileListContainer = document.getElementById(fileListContainerId);
    const tempInputsContainer = document.getElementById(tempInputsContainerId);
    let selectedFiles = [...initialFiles];

    if (!dropZone || !fileInput) return;

    // Event Listeners
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
        formData.append('_token', csrfToken);

        const tempId = Math.random().toString(36).substring(7);
        addLoadingPlaceholder(tempId, file.name);

        fetch(uploadRoute, {
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
                    if (window.Swal) {
                        Swal.fire('Error', data.message || 'Error al subir archivo', 'error');
                    } else {
                        alert(data.message || 'Error al subir archivo');
                    }
                }
            })
            .catch(error => {
                removeLoadingPlaceholder(tempId);
                console.error('Error:', error);
                if (window.Swal) {
                    Swal.fire('Error', 'Error de conexión al subir el archivo', 'error');
                }
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

        const removeAction = () => {
            fetch(deleteRoute, {
                method: 'DELETE',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': csrfToken
                },
                body: JSON.stringify({ path: file.path })
            });

            selectedFiles.splice(index, 1);
            updateFileList();
            updateHiddenInputs();
        };

        if (window.Swal) {
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
                if (result.isConfirmed) removeAction();
            });
        } else if (confirm('¿Quitar archivo?')) {
            removeAction();
        }
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
        const fileName = file.name || file.path || '';
        const extension = fileName.split('.').pop().toLowerCase();
        if (['jpg', 'jpeg', 'png', 'gif', 'webp'].includes(extension)) return 'image';
        if (extension === 'pdf') return 'pdf';
        if (['doc', 'docx'].includes(extension)) return 'word';
        if (['xls', 'xlsx', 'csv'].includes(extension)) return 'excel';
        return 'other';
    }

    function getFileIcon(type) {
        switch (type) {
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
            const storageUrl = storagePreviewUrl + '/' + file.path;

            const onclickAction = isPreviewable ? `data-preview-url="${storageUrl}" data-preview-name="${file.name}" data-preview-type="${type}" style="cursor:pointer;" class="preview-trigger"` : '';

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
                            <button type="button" class="btn btn-sm btn-outline-info p-0 js-preview-btn" style="width:28px; height:28px;" title="Vista Previa"
                                    data-url="${storageUrl}" data-name="${file.name}" data-type="${type}">
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
                const trigger = card.querySelector('.preview-trigger');
                if (trigger) {
                    trigger.addEventListener('click', () => {
                        window.openGlobalPreview(storageUrl, file.name, type);
                    });
                }
                const btn = card.querySelector('.js-preview-btn');
                if (btn) {
                    btn.addEventListener('click', () => {
                        window.openGlobalPreview(storageUrl, file.name, type);
                    });
                }
            }

            col.appendChild(card);
            row.appendChild(col);

            card.querySelector('.remove-btn').addEventListener('click', () => removeFile(index));
        });

        fileListContainer.appendChild(row);
    }

    // Initial render
    updateFileList();
    updateHiddenInputs();
}

/**
 * Global Preview Modal logic
 */
export function initPreviewModal(modalId, titleId, contentId) {
    const modalEl = document.getElementById(modalId);
    if (!modalEl) return;

    const globalModal = new bootstrap.Modal(modalEl);
    const previewTitle = document.getElementById(titleId);
    const previewContent = document.getElementById(contentId);

    window.openGlobalPreview = function (url, name, type) {
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
}

/**
 * Form Submit protection
 */
export function protectForm(formId, submitBtnId) {
    const form = document.getElementById(formId);
    if (!form) return;

    form.addEventListener('submit', function () {
        const btn = document.getElementById(submitBtnId);
        if (!btn) return;

        const text = btn.querySelector('.btn-text');
        const spinner = btn.querySelector('.spinner-border');

        btn.disabled = true;
        if (text) text.textContent = 'Guardando...';
        if (spinner) spinner.classList.remove('d-none');
    });
}
