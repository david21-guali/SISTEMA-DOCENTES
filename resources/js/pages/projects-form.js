import { initFileUploader, initPreviewModal, protectForm } from '../modules/file-uploader';

document.addEventListener('DOMContentLoaded', function () {
    // 1. Initialize File Uploader
    if (window.FileUploadConfig) {
        initFileUploader({
            dropZoneId: window.FileUploadConfig.dropZoneId || 'createDropZone',
            fileInputId: window.FileUploadConfig.fileInputId || 'createFileInput',
            fileListContainerId: window.FileUploadConfig.fileListContainerId || 'createFileList',
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

    // 4. Handle EXISTING Attachments
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

// 6. Page Specific Logic: Filter Users
window.filterUsers = function () {
    const input = document.getElementById('user_search');
    if (!input) return;
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

// 7. Page Specific Logic: Toggle Budget
window.toggleBudget = function () {
    const checkBox = document.getElementById('project_needs_budget');
    const container = document.getElementById('budget_container');
    const input = document.getElementById('budget');
    if (!checkBox || !container || !input) return;

    if (checkBox.checked) {
        container.style.display = 'block';
        input.focus();
    } else {
        container.style.display = 'none';
        input.value = '';
    }
}

// 8. Page Specific Logic: Save Category (from create view)
window.saveCategory = function () {
    const nameInput = document.getElementById('new_category_name');
    const name = nameInput.value;
    if (!name) return;

    fetch(window.AppConfig.routes.categoriesStore, {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': window.AppConfig.csrfToken
        },
        body: JSON.stringify({ name: name })
    })
        .then(response => response.json())
        .then(data => {
            if (data.id) {
                const select = document.getElementById('category_id');
                const option = new Option(data.name, data.id, true, true);
                select.add(option);

                // Close modal
                const modal = bootstrap.Modal.getInstance(document.getElementById('addCategoryModal'));
                modal.hide();
                nameInput.value = '';

                Swal.fire('Éxito', 'Categoría guardada', 'success');
            }
        });
}
