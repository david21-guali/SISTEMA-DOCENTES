@extends('layouts.admin')

@section('title', 'Gestión de Tipos de Innovación')

@section('contenido')
<div class="container-fluid">
    <!-- Page Header -->
    <div class="d-flex flex-column flex-sm-row justify-content-between align-items-sm-center gap-3 mb-4">
        <div>
            <h5 class="mb-0 text-dark fw-bold"><i class="fas fa-tags me-2 text-primary"></i>Tipos de Innovación</h5>
            <p class="text-muted mb-0 small">Administra las clasificaciones para las innovaciones pedagógicas</p>
        </div>
        <button type="button" class="btn btn-primary shadow-sm" data-bs-toggle="modal" data-bs-target="#modalType">
            <i class="fas fa-plus me-1"></i> Nuevo Tipo
        </button>
    </div>

    <div class="card border-0 shadow-sm">
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-hover align-middle" id="tableTypes">
                    <thead>
                        <tr>
                            <th>Nombre</th>
                            <th>Descripción</th>
                            <th class="text-center">Innovaciones</th>
                            <th class="text-center">Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($types as $type)
                        <tr data-id="{{ $type->id }}" data-name="{{ $type->name }}" data-description="{{ $type->description }}">
                            <td class="fw-medium text-dark">{{ $type->name }}</td>
                            <td class="text-muted small text-truncate" style="max-width: 300px;">{{ $type->description ?: 'Sin descripción' }}</td>
                            <td class="text-center">
                                <span class="badge bg-primary bg-opacity-10 text-primary">
                                    {{ $type->innovations_count }}
                                </span>
                            </td>
                            <td>
                                <div class="d-flex justify-content-center gap-1">
                                    <button class="btn btn-sm btn-outline-warning btn-edit" title="Editar">
                                        <i class="fas fa-edit"></i>
                                    </button>
                                    @if($type->innovations_count == 0)
                                    <button class="btn btn-sm btn-outline-danger btn-delete" title="Eliminar">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                    @endif
                                </div>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<!-- Modal Create/Edit -->
<div class="modal fade" id="modalType" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content border-0 shadow">
            <div class="modal-header bg-light">
                <h5 class="modal-title fw-bold text-dark" id="modalTitle">Nuevo Tipo de Innovación</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="formType">
                @csrf
                <input type="hidden" id="type_id" name="type_id">
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="type_name" class="form-label fw-bold">Nombre</label>
                        <input type="text" class="form-control" id="type_name" name="name" required max="50" placeholder="Ej: Aprendizaje Basado en Proyectos">
                    </div>
                    <div class="mb-3">
                        <label for="type_description" class="form-label fw-bold">Descripción</label>
                        <textarea class="form-control" id="type_description" name="description" rows="3" max="255" placeholder="Breve descripción del tipo de innovación"></textarea>
                    </div>
                </div>
                <div class="modal-footer bg-light">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-primary" id="btnSave">Guardar</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const modal = new bootstrap.Modal(document.getElementById('modalType'));
        const form = document.getElementById('formType');
        const modalTitle = document.getElementById('modalTitle');
        const btnSave = document.getElementById('btnSave');
        
        // DataTable
        const table = $('#tableTypes').DataTable({
            language: { url: 'https://cdn.datatables.net/plug-ins/1.13.6/i18n/es-ES.json' },
            responsive: true,
            order: [[0, 'asc']],
            dom: "<'row gy-3 mb-3'<'col-12 col-md-6 d-flex justify-content-center justify-content-md-start'l><'col-12 col-md-6 d-flex flex-column flex-md-row justify-content-center justify-content-md-end align-items-center gap-2'fB>>" +
                "<'row'<'col-12'tr>>" +
                "<'row mt-3 gy-2 align-items-center'<'col-12 col-md-5 d-flex justify-content-center justify-content-md-start'i><'col-12 col-md-7 d-flex justify-content-center justify-content-md-end'p>>",
            buttons: [
                {
                    extend: 'excel',
                    className: 'btn btn-success btn-sm',
                    text: '<i class="fas fa-file-excel me-1"></i> Excel',
                    exportOptions: { columns: [0, 1, 2] }
                },
                {
                    extend: 'pdf',
                    className: 'btn btn-danger btn-sm',
                    text: '<i class="fas fa-file-pdf me-1"></i> PDF',
                    exportOptions: { columns: [0, 1, 2] }
                },
                {
                    extend: 'print',
                    className: 'btn btn-secondary btn-sm',
                    text: '<i class="fas fa-print me-1"></i> Imprimir',
                    exportOptions: { columns: [0, 1, 2] }
                }
            ]
        });

        // Open Modal for Create
        document.querySelector('[data-bs-target="#modalType"]').addEventListener('click', function() {
            form.reset();
            document.getElementById('type_id').value = '';
            modalTitle.innerText = 'Nuevo Tipo de Innovación';
            btnSave.innerText = 'Guardar';
        });

        // Open Modal for Edit
        $('#tableTypes').on('click', '.btn-edit', function() {
            const tr = $(this).closest('tr');
            const id = tr.data('id');
            const name = tr.data('name');
            const description = tr.data('description');

            document.getElementById('type_id').value = id;
            document.getElementById('type_name').value = name;
            document.getElementById('type_description').value = description;
            
            modalTitle.innerText = 'Editar Tipo de Innovación';
            btnSave.innerText = 'Actualizar';
            modal.show();
        });

        // Auto-focus name field on modal show
        document.getElementById('modalType').addEventListener('shown.bs.modal', function () {
            document.getElementById('type_name').focus();
        });

        // Handle Form Submit
        form.addEventListener('submit', function(e) {
            e.preventDefault();
            const id = document.getElementById('type_id').value;
            const url = id ? `/innovation-types/${id}` : '/innovation-types';
            const method = id ? 'PUT' : 'POST';

            btnSave.disabled = true;
            btnSave.innerHTML = '<span class="spinner-border spinner-border-sm me-1"></span> Procesando...';

            fetch(url, {
                method: method,
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                },
                body: JSON.stringify({
                    name: document.getElementById('type_name').value,
                    description: document.getElementById('type_description').value
                })
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    Swal.fire('¡Éxito!', data.message, 'success').then(() => location.reload());
                } else {
                    Swal.fire('Error', data.error || 'Ocurrió un error inesperado', 'error');
                }
            })
            .catch(error => {
                console.error('Error:', error);
                Swal.fire('Error', 'No se pudo completar la operación', 'error');
            })
            .finally(() => {
                btnSave.disabled = false;
                btnSave.innerText = id ? 'Actualizar' : 'Guardar';
            });
        });

        // Handle Delete
        $('#tableTypes').on('click', '.btn-delete', function() {
            const tr = $(this).closest('tr');
            const id = tr.data('id');
            const name = tr.data('name');

            Swal.fire({
                title: '¿Estás seguro?',
                text: `¿Deseas eliminar el tipo "${name}"?`,
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#e74a3b',
                cancelButtonColor: '#858796',
                confirmButtonText: 'Sí, eliminar',
                cancelButtonText: 'Cancelar'
            }).then((result) => {
                if (result.isConfirmed) {
                    fetch(`/innovation-types/${id}`, {
                        method: 'DELETE',
                        headers: {
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                        }
                    })
                    .then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            Swal.fire('¡Eliminado!', data.message, 'success').then(() => location.reload());
                        } else {
                            Swal.fire('Error', data.message || 'No se pudo eliminar', 'error');
                        }
                    });
                }
            });
        });
    });
</script>
@endsection
