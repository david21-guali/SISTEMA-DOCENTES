@extends('layouts.admin')

@section('title', 'Gestión de Recursos')

@section('contenido')
<div class="container-fluid">
    <!-- Page Header -->
    <div class="d-flex flex-column flex-sm-row justify-content-between align-items-sm-center gap-3 mb-4">
        <div>
            <h5 class="mb-0 text-dark"><i class="fas fa-box-open me-2 text-primary"></i>Banco de Recursos</h5>
            <p class="text-muted mb-0 small">Gestiona los materiales y recursos disponibles para proyectos</p>
        </div>
        <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#createResourceModal">
            <i class="fas fa-plus me-1"></i> Nuevo Recurso
        </button>
    </div>

    @if($resources->count() > 0)
    <!-- Stats Row -->
    <div class="row g-3 mb-4">
        <div class="col-md-3">
            <div class="card border-left-primary h-100">
                <div class="card-body py-3">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <div class="text-xs text-uppercase fw-bold text-primary mb-1">Total Recursos</div>
                            <div class="h5 mb-0 fw-bold">{{ $resources->count() }}</div>
                        </div>
                        <i class="fas fa-box fa-2x text-muted opacity-25"></i>
                    </div>
                </div>
            </div>
        </div>
        <!-- Dynamic Stats based on Types -->
        @foreach($types->take(3) as $type)
        <div class="col-md-3">
            <div class="card border-left-{{ $loop->iteration == 1 ? 'info' : ($loop->iteration == 2 ? 'success' : 'warning') }} h-100">
                <div class="card-body py-3">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <div class="text-xs text-uppercase fw-bold text-{{ $loop->iteration == 1 ? 'info' : ($loop->iteration == 2 ? 'success' : 'warning') }} mb-1">
                                {{ $type->name }}
                            </div>
                            <div class="h5 mb-0 fw-bold">{{ $resources->where('resource_type_id', $type->id)->count() }}</div>
                        </div>
                        <i class="fas fa-layer-group fa-2x text-muted opacity-25"></i>
                    </div>
                </div>
            </div>
        </div>
        @endforeach
    </div>

    <!-- Distribution Chart -->
    @if($distributionData->count() > 0)
    <div class="row mb-4">
        <div class="col-lg-6 mx-auto">
            <div class="card shadow-sm border-0">
                <div class="card-header bg-white py-3 border-bottom">
                    <h6 class="m-0 fw-bold text-primary">
                        <i class="fas fa-chart-pie me-2"></i>Distribución por Tipo de Recurso
                    </h6>
                </div>
                <div class="card-body">
                    <div class="chart-container" style="position: relative; height: 300px;">
                        <canvas id="resourceDistributionChart"></canvas>
                    </div>
                    <div class="mt-3 text-center">
                        <small class="text-muted">
                            <i class="fas fa-info-circle me-1"></i>
                            Mostrando distribución de {{ $distributionData->count() }} tipo(s) con recursos asignados
                        </small>
                    </div>
                </div>
            </div>
        </div>
    </div>
    @endif

    <!-- Resources List -->
    <div class="card shadow-sm border-0">
        <div class="card-header bg-white py-3">
            <h6 class="m-0 fw-bold text-primary">Listado de Recursos</h6>
        </div>
        <div class="card-body p-0 p-md-3">
            <!-- Desktop View -->
            <div class="table-responsive d-none d-md-block">
                <table class="table table-hover mb-0 align-middle" id="dataTable" width="100%" cellspacing="0">
                    <thead class="bg-light">
                        <tr>
                            <th class="border-0 text-uppercase text-xs fw-bold text-muted ps-4">Nombre</th>
                            <th class="border-0 text-uppercase text-xs fw-bold text-muted">Tipo</th>
                            <th class="border-0 text-uppercase text-xs fw-bold text-muted">Descripción</th>
                            <th class="border-0 text-uppercase text-xs fw-bold text-muted text-end pe-4">Acciones</th>
                        </tr>
                    </thead>
                    <tbody class="border-top-0">
                        @foreach($resources as $resource)
                        <tr>
                            <td class="ps-4">
                                <div class="d-flex align-items-center">
                                    <div class="rounded-circle bg-light d-flex align-items-center justify-content-center me-3" style="width: 35px; height: 35px;">
                                        <i class="fas fa-box text-primary"></i>
                                    </div>
                                    <div class="fw-bold text-dark">{{ $resource->name }}</div>
                                </div>
                            </td>
                            <td>
                                <span class="badge bg-light text-dark border">
                                    {{ $resource->type->name ?? 'Sin Tipo' }}
                                </span>
                                @if($resource->file_path)
                                    <a href="{{ route('resources.download', $resource) }}" class="ms-1 text-primary" title="Descargar">
                                        <i class="fas fa-download"></i>
                                    </a>
                                @endif
                            </td>
                            <td>
                                <span class="text-muted small">{{ Str::limit($resource->description, 60) }}</span>
                            </td>
                            <td class="text-end pe-4">
                                <div class="btn-group">
                                    <a href="{{ route('resources.edit', $resource) }}" class="btn btn-sm btn-outline-warning">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                    <form action="{{ route('resources.destroy', $resource) }}" method="POST" class="d-inline form-delete">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-sm btn-outline-danger">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <!-- Mobile View -->
            <div class="d-md-none p-3">
                @foreach($resources as $resource)
                <div class="card mb-3 shadow-sm border-0 border-start border-4 border-primary">
                    <div class="card-body p-3">
                        <div class="d-flex justify-content-between align-items-start mb-2">
                            <h6 class="fw-bold mb-0 text-dark">{{ $resource->name }}</h6>
                            <span class="badge bg-light text-dark border small">
                                {{ $resource->type->name ?? 'Sin Tipo' }}
                            </span>
                        </div>
                        <p class="text-muted small mb-3">{{ $resource->description }}</p>
                        <div class="d-flex justify-content-between align-items-center pt-2 border-top">
                            <div>
                                @if($resource->file_path)
                                    <a href="{{ route('resources.download', $resource) }}" class="btn btn-sm btn-outline-primary rounded-pill">
                                        <i class="fas fa-download me-1"></i> Archivo
                                    </a>
                                @endif
                            </div>
                            <div class="btn-group">
                                <a href="{{ route('resources.edit', $resource) }}" class="btn btn-sm btn-light p-2">
                                    <i class="fas fa-edit text-warning"></i>
                                </a>
                                <form action="{{ route('resources.destroy', $resource) }}" method="POST" class="form-delete d-inline">
                                    @csrf @method('DELETE')
                                    <button type="submit" class="btn btn-sm btn-light p-2">
                                        <i class="fas fa-trash text-danger"></i>
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
                @endforeach
            </div>
        </div>
    </div>
    @else
    <div class="text-center py-5">
        <div class="mb-3">
            <div class="rounded-circle bg-light d-flex align-items-center justify-content-center mx-auto" style="width: 80px; height: 80px;">
                <i class="fas fa-box-open fa-3x text-muted opacity-50"></i>
            </div>
        </div>
        <h5 class="text-muted">No hay recursos registrados</h5>
        <p class="text-muted small mb-4">Comienza registrando los materiales y recursos disponibles.</p>
        <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#createResourceModal">
            <i class="fas fa-plus me-1"></i> Crear Primer Recurso
        </button>
    </div>
    @endif
</div>

<!-- Modal Create Resource -->
<div class="modal fade" id="createResourceModal" tabindex="-1">
    <div class="modal-dialog">
        <form action="{{ route('resources.store') }}" method="POST" enctype="multipart/form-data" novalidate>
            @csrf
            <div class="modal-content border-0 shadow">
                <div class="modal-header bg-primary text-white">
                    <h5 class="modal-title fw-bold"><i class="fas fa-plus-circle me-2"></i>Registrar Nuevo Recurso</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body p-4">
                    <div class="mb-3">
                        <label class="form-label fw-bold small text-muted">NOMBRE DEL RECURSO *</label>
                        <input type="text" name="name" class="form-control @error('name') is-invalid @enderror" value="{{ old('name') }}" placeholder="Ej. Proyector Epson, Licencias Zoom...">
                        @error('name')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    
                    <div class="mb-3">
                        <label class="form-label fw-bold small text-muted">TIPO DE RECURSO *</label>
                        <div class="input-group">
                            <select name="resource_type_id" class="form-select @error('resource_type_id') is-invalid @enderror" id="resourceTypeSelect" onchange="toggleFileGroup(this)">
                                <option value="">Seleccione un tipo...</option>
                                @foreach($types as $type)
                                    <option value="{{ $type->id }}" data-slug="{{ $type->slug }}" {{ old('resource_type_id') == $type->id ? 'selected' : '' }}>{{ $type->name }}</option>
                                @endforeach
                            </select>
                            @if(auth()->user()->hasRole('admin') || auth()->user()->hasRole('coordinador'))
                            <button class="btn btn-outline-primary" type="button" data-bs-toggle="modal" data-bs-target="#createTypeModal" title="Agregar nuevo tipo">
                                <i class="fas fa-plus"></i>
                            </button>
                            @endif
                        </div>
                        @error('resource_type_id')
                            <div class="invalid-feedback d-block">{{ $message }}</div>
                        @enderror
                        
                        @if(auth()->user()->hasRole('admin'))
                        <div class="mt-2">
                            <button class="btn btn-sm btn-link text-muted p-0" type="button" data-bs-toggle="collapse" data-bs-target="#manageTypesSection">
                                <i class="fas fa-cog me-1"></i> Gestionar tipos de recursos
                            </button>
                            <div class="collapse mt-2" id="manageTypesSection">
                                <div class="border rounded p-2 bg-light">
                                    <p class="small text-muted mb-2"><i class="fas fa-info-circle"></i> Solo puedes eliminar tipos sin recursos asociados.</p>
                                    <div class="list-group list-group-flush">
                                        @foreach($types as $type)
                                        <div class="list-group-item d-flex justify-content-between align-items-center px-2 py-1 bg-transparent border-0">
                                            <span class="small">{{ $type->name }} <span class="badge bg-secondary">{{ $type->resources->count() }}</span></span>
                                            <button type="button" class="btn btn-sm btn-link text-danger p-0" 
                                                    onclick="deleteResourceType({{ $type->id }}, '{{ $type->name }}', {{ $type->resources->count() }})"
                                                    title="Eliminar tipo">
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

                    <div class="mb-3 d-none" id="fileGroup">
                        <label class="form-label fw-bold small text-muted">ARCHIVO (OPCIONAL)</label>
                        <input type="file" name="file" class="form-control @error('file') is-invalid @enderror">
                        @error('file')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                        <small class="text-muted d-block mt-1"><i class="fas fa-info-circle me-1"></i>Sube aquí tu plantilla o documento digital.</small>
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-bold small text-muted">DESCRIPCIÓN</label>
                        <textarea name="description" class="form-control @error('description') is-invalid @enderror" rows="3" placeholder="Detalles adicionales sobre el recurso...">{{ old('description') }}</textarea>
                        @error('description')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <!-- Sección Apartada de Presupuesto -->
                    <div class="accordion" id="budgetAccordion">
                        <div class="accordion-item border rounded overflow-hidden">
                            <h2 class="accordion-header">
                                <button class="accordion-button collapsed bg-gray-100 text-muted" type="button" data-bs-toggle="collapse" data-bs-target="#budgetDetails">
                                    <i class="fas fa-coins me-2"></i> Información Financiera (Opcional)
                                </button>
                            </h2>
                            <div id="budgetDetails" class="accordion-collapse collapse" data-bs-parent="#budgetAccordion">
                                <div class="accordion-body bg-light">
                                    <label class="form-label fw-bold small text-muted">COSTO REFERENCIAL ($)</label>
                                    <div class="input-group">
                                        <span class="input-group-text">$</span>
                                        <input type="number" name="cost" class="form-control @error('cost') is-invalid @enderror" value="{{ old('cost') }}" step="0.01" placeholder="0.00" min="0" max="9999999.99" oninput="if(this.value.length > 10) this.value = this.value.slice(0, 10);">
                                        @error('cost')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                    <small class="text-muted d-block mt-2">
                                        Este valor es meramente referencial y no se mostrará en los listados generales.
                                    </small>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer bg-gray-50 border-top-0">
                    <button type="button" class="btn btn-link text-muted" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-primary px-4">Guardar Recurso</button>
                </div>
            </div>
        </form>
    </div>
</div>

<!-- Modal Create Type -->
<div class="modal fade" id="createTypeModal" tabindex="-1" style="z-index: 1060;">
    <div class="modal-dialog modal-sm modal-dialog-centered">
        <form id="createTypeForm" novalidate>
            @csrf
            <div class="modal-content shadow">
                <div class="modal-header bg-light">
                    <h6 class="modal-title fw-bold">Nuevo Tipo de Recurso</h6>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-2">
                        <label class="form-label small text-muted fw-bold">Nombre del Tipo *</label>
                        <input type="text" name="name" id="newTypeName" class="form-control form-control-sm" placeholder="Ej. Audiovisual">
                    </div>
                    <div class="mb-0">
                        <label class="form-label small text-muted fw-bold">Descripción (Opcional)</label>
                        <textarea name="description" id="newTypeDescription" class="form-control form-control-sm" rows="2"></textarea>
                    </div>
                </div>
                <div class="modal-footer p-2 bg-light">
                    <button type="button" class="btn btn-sm btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-sm btn-primary">Guardar</button>
                </div>
            </div>
        </form>
    </div>
</div>

@push('styles')
<style>
    .text-xs { font-size: 0.7rem; }
    .bg-gray-50 { background-color: #f8f9fa; }
    .bg-gray-100 { background-color: #f8f9fc; }
    .border-left-primary { border-left: 4px solid #4e73df !important; }
    .border-left-success { border-left: 4px solid #1cc88a !important; }
    .border-left-info { border-left: 4px solid #36b9cc !important; }
    .border-left-warning { border-left: 4px solid #f6c23e !important; }
</style>
@endpush

@push('scripts')
<script>
    // Handle File Group Visibility
    function toggleFileGroup(selectElement) {
        const fileGroup = document.getElementById('fileGroup');
        const selectedOption = selectElement.options[selectElement.selectedIndex];
        const slug = selectedOption.getAttribute('data-slug');
        
        // Check if slug contains 'digital' or 'archivo'
        if(slug && (slug.includes('digital') || slug.includes('archivo') || slug.includes('plantilla'))) {
            fileGroup.classList.remove('d-none');
        } else {
            fileGroup.classList.add('d-none');
        }
    }

    // Handle AJAX Type Creation
    document.getElementById('createTypeForm').addEventListener('submit', function(e) {
        e.preventDefault();
        
        const name = document.getElementById('newTypeName').value;
        const description = document.getElementById('newTypeDescription').value;
        const token = document.querySelector('input[name="_token"]').value;
        const btn = this.querySelector('button[type="submit"]');
        const originalText = btn.innerHTML;
        
        btn.innerHTML = '<i class="fas fa-spinner fa-spin"></i>';
        btn.disabled = true;

        fetch('{{ route("resource-types.store") }}', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': token,
                'Accept': 'application/json'
            },
            body: JSON.stringify({ name: name, description: description })
        })
        .then(response => {
            if (!response.ok) {
                return response.json().then(err => Promise.reject(err));
            }
            return response.json();
        })
        .then(data => {
            if(data.success) {
                // Add new option to select
                const select = document.getElementById('resourceTypeSelect');
                const option = new Option(data.type.name, data.type.id, true, true);
                option.setAttribute('data-slug', data.type.slug);
                select.add(option);
                
                // Update the management list if visible
                const manageList = document.querySelector('#manageTypesSection .list-group');
                if (manageList) {
                    const newItem = document.createElement('div');
                    newItem.className = 'list-group-item d-flex justify-content-between align-items-center px-2 py-1 bg-transparent border-0';
                    newItem.innerHTML = `
                        <span class="small">${data.type.name} <span class="badge bg-secondary">0</span></span>
                        <button type="button" class="btn btn-sm btn-link text-danger p-0" 
                                onclick="deleteResourceType(${data.type.id}, '${data.type.name}', 0)"
                                title="Eliminar tipo">
                            <i class="fas fa-trash"></i>
                        </button>
                    `;
                    manageList.appendChild(newItem);
                }
                
                // Close modal
                const modal = bootstrap.Modal.getInstance(document.getElementById('createTypeModal'));
                modal.hide();
                
                // Reset form
                document.getElementById('createTypeForm').reset();
                
                // Show success message with SweetAlert2
                Swal.fire('Éxito', 'Tipo de recurso creado correctamente', 'success');
            }
        })
        .catch(error => {
            console.error('Error:', error);
            let errorMessage = 'Error de conexión al guardar el tipo.';
            
            // Si hay errores de validación de Laravel
            if (error.errors) {
                const errors = Object.values(error.errors).flat();
                errorMessage = errors.join('\n');
            } else if (error.message) {
                errorMessage = error.message;
            }
            
            Swal.fire('Error', errorMessage, 'error');
        })
        .finally(() => {
            btn.innerHTML = originalText;
            btn.disabled = false;
        });
    });

    // Function to delete resource type
    function deleteResourceType(typeId, typeName, resourceCount) {
        if (resourceCount > 0) {
            Swal.fire({
                title: 'No se puede eliminar',
                text: `El tipo "${typeName}" tiene ${resourceCount} recurso(s) asociado(s). Elimina o reasigna los recursos primero.`,
                icon: 'warning',
                confirmButtonColor: '#4e73df',
                confirmButtonText: 'Entendido'
            });
            return;
        }

        Swal.fire({
            title: `¿Eliminar tipo "${typeName}"?`,
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
                
                fetch(`/resource-types/${typeId}`, {
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
                        // Remove from select
                        const select = document.getElementById('resourceTypeSelect');
                        const option = select.querySelector(`option[value="${typeId}"]`);
                        if (option) option.remove();
                        
                        // Reload page to update the management section
                        Swal.fire('Eliminado', data.message, 'success').then(() => {
                            window.location.reload();
                        });
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    const errorMessage = error.message || 'Error al eliminar el tipo de recurso';
                    Swal.fire('Error', errorMessage, 'error');
                });
            }
        });
    }

    document.addEventListener('DOMContentLoaded', function() {
        if (typeof jQuery !== 'undefined') {
            new DataTable('#dataTable', {
                language: {
                    url: 'https://cdn.datatables.net/plug-ins/1.13.6/i18n/es-ES.json'
                },
                pageLength: 10,
                lengthMenu: [5, 10, 25, 50],
                dom: "<'row mb-2'" +
                    "<'col-md-6'l>" +
                    "<'col-md-6 text-end'B>" +
                    ">" +
                    "<'row mb-2'" +
                    "<'col-md-6'f>" +
                    "<'col-md-6'>>" +
                    ">" +
                    "<'row'<'col-12'tr>>" +
                    "<'row mt-2'" +
                    "<'col-md-5'i>" +
                    "<'col-md-7'p>" +
                    ">",
                buttons: [
                    {
                        extend: 'excel',
                        className: 'btn btn-success btn-sm',
                        text: '<i class="fas fa-file-excel me-1"></i> Excel',
                        exportOptions: {
                            columns: [0, 1, 2]
                        }
                    },
                    {
                        extend: 'pdf',
                        className: 'btn btn-danger btn-sm',
                        text: '<i class="fas fa-file-pdf me-1"></i> PDF',
                        exportOptions: {
                            columns: [0, 1, 2]
                        }
                    },
                    {
                        extend: 'print',
                        className: 'btn btn-secondary btn-sm',
                        text: '<i class="fas fa-print me-1"></i> Imprimir',
                        exportOptions: {
                            columns: [0, 1, 2]
                        }
                    }
                ],
                responsive: true
            });
        }

        @if($errors->any())
            var createResourceModal = new bootstrap.Modal(document.getElementById('createResourceModal'));
            createResourceModal.show();
        @endif

        // Trigger file group visibility for old values or errors
        const typeSelect = document.getElementById('resourceTypeSelect');
        if(typeSelect && typeSelect.value) {
            toggleFileGroup(typeSelect);
        }

        // Initialize Distribution Chart
        @if(isset($distributionData) && $distributionData->count() > 0)
        const ctx = document.getElementById('resourceDistributionChart');
        if (ctx) {
            const chartData = @json($distributionData);
            
            // Generate vibrant colors
            const colors = [
                '#4e73df', '#1cc88a', '#36b9cc', '#f6c23e', '#e74a3b',
                '#858796', '#5a5c69', '#2e59d9', '#17a673', '#2c9faf',
                '#dda20a', '#be2617', '#6c757d', '#fd7e14', '#6610f2'
            ];
            
            new Chart(ctx, {
                type: 'doughnut',
                data: {
                    labels: chartData.map(item => item.label),
                    datasets: [{
                        data: chartData.map(item => item.count),
                        backgroundColor: colors.slice(0, chartData.length),
                        borderWidth: 2,
                        borderColor: '#fff'
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: {
                            position: 'right',
                            labels: {
                                boxWidth: 12,
                                padding: 10,
                                font: { size: 11 }
                            }
                        },
                        tooltip: {
                            callbacks: {
                                label: function(context) {
                                    const label = context.label || '';
                                    const value = context.parsed || 0;
                                    const total = context.dataset.data.reduce((a, b) => a + b, 0);
                                    const percentage = ((value / total) * 100).toFixed(1);
                                    return `${label}: ${value} (${percentage}%)`;
                                }
                            }
                        }
                    }
                }
            });
        }
        @endif
    });
</script>
@endpush
@endsection
