import { initFileUploader, initPreviewModal, protectForm } from '../modules/file-uploader';

document.addEventListener('DOMContentLoaded', function () {
    // 1. Initialize File Uploader (for new/temp files)
    if (window.FileUploadConfig) {
        initFileUploader({
            dropZoneId: window.FileUploadConfig.dropZoneId || 'taskDropZone',
            fileInputId: window.FileUploadConfig.fileInputId || 'taskFileInput',
            fileListContainerId: window.FileUploadConfig.fileListContainerId || 'taskFileList',
            tempInputsContainerId: 'tempFileInputs',
            csrfToken: window.AppConfig.csrfToken,
            uploadRoute: window.AppConfig.routes.tempUpload,
            deleteRoute: window.AppConfig.routes.tempDelete,
            storagePreviewUrl: window.AppConfig.urls.storagePreview,
            initialFiles: window.FileUploadConfig.initialFiles || []
        });
    }

    // 2. Initialize Preview Modal
    initPreviewModal('globalPreviewModal', 'previewTitle', 'previewContent');

    // 3. Protect Form
    if (window.AppConfig.formId) {
        protectForm(window.AppConfig.formId, 'submitBtn');
    }

    // 4. Handle EXISTING Attachments (for Edit views)
    const deleteButtons = document.querySelectorAll('.js-destroy-attachment-btn');
    deleteButtons.forEach(btn => {
        btn.addEventListener('click', function (e) {
            e.preventDefault();
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
                    const url = `${window.AppConfig.routes.attachmentsBase}/${id}`;

                    fetch(url, {
                        method: 'DELETE',
                        headers: {
                            'X-CSRF-TOKEN': window.AppConfig.csrfToken,
                            'Accept': 'application/json',
                            'X-Requested-With': 'XMLHttpRequest'
                        }
                    })
                        .then(response => response.json())
                        .then(data => {
                            if (data.success) {
                                const el = document.getElementById(`attachment-${id}`);
                                if (el) el.remove();
                                Swal.fire('Eliminado', data.message, 'success');
                            } else {
                                Swal.fire('Error', data.error || 'No se pudo eliminar', 'error');
                            }
                        })
                        .catch(error => {
                            console.error('Error:', error);
                            Swal.fire('Error', 'Error de conexión', 'error');
                        });
                }
            });
        });
    });

    // 5. Handle Preview for existing attachments
    document.querySelectorAll('.js-preview-attachment').forEach(btn => {
        btn.addEventListener('click', function () {
            if (window.openGlobalPreview) {
                window.openGlobalPreview(this.dataset.url, this.dataset.name, this.dataset.type);
            }
        });
    });
});

// 6. Page Specific Logic: Filter Assignees
window.filterAssignees = function () {
    const input = document.getElementById('assignee_search');
    if (!input) return;

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
