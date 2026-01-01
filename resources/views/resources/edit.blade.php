@extends('layouts.admin')

@section('title', 'Editar Recurso')

@section('contenido')
<div class="container-fluid">
    <!-- Page Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h5 class="mb-0 text-dark"><i class="fas fa-edit me-2 text-primary"></i>Editar Recurso</h5>
            <p class="text-muted mb-0 small">Actualiza la información del recurso</p>
        </div>
        <a href="{{ route('resources.index') }}" class="btn btn-outline-secondary btn-sm">
            <i class="fas fa-arrow-left me-1"></i> Volver
        </a>
    </div>

    <div class="row justify-content-center">
        <div class="col-lg-8">
            <form action="{{ route('resources.update', $resource) }}" method="POST" enctype="multipart/form-data" novalidate>
                @csrf
                @method('PUT')
                
                <!-- Main Info Card -->
                <div class="card shadow-sm mb-4 border-0 border-top border-4 border-primary">
                    <div class="card-body p-4">
                        <div class="mb-4">
                            <label class="form-label fw-bold small text-muted">NOMBRE DEL RECURSO *</label>
                            <input type="text" name="name" class="form-control @error('name') is-invalid @enderror" value="{{ old('name', $resource->name) }}">
                            @error('name')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        
                        <div class="mb-4">
                            <label class="form-label fw-bold small text-muted">TIPO DE RECURSO *</label>
                            <div class="input-group">
                                <select name="resource_type_id" class="form-select @error('resource_type_id') is-invalid @enderror" id="resourceTypeSelect" onchange="toggleEditFileGroup(this)">
                                    <option value="">Seleccione un tipo...</option>
                                    @foreach($types as $type)
                                        <option value="{{ $type->id }}" 
                                            data-slug="{{ $type->slug }}"
                                            {{ old('resource_type_id', $resource->resource_type_id) == $type->id ? 'selected' : '' }}>
                                            {{ $type->name }}
                                        </option>
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
                        </div>
                        
                        <!-- File Group visibility logic needs to check current type slug -->
                        @php
                            $currentTypeSlug = $resource->type ? $resource->type->slug : '';
                            $showFile = str_contains($currentTypeSlug, 'digital') || str_contains($currentTypeSlug, 'archivo');
                        @endphp
                        
                        <div class="mb-4 {{ $showFile ? '' : 'd-none' }}" id="editFileGroup">
                            <label class="form-label fw-bold small text-muted">ARCHIVO ADJUNTO</label>
                            @if($resource->file_path)
                                <div class="alert alert-info py-2 mb-2 d-flex align-items-center">
                                    <i class="fas fa-file-alt me-2"></i>
                                    <span>Archivo actual disponible</span>
                                    <a href="{{ asset('storage/' . $resource->file_path) }}" target="_blank" class="btn btn-sm btn-info ms-auto text-white">
                                        <i class="fas fa-download"></i> Descargar
                                    </a>
                                </div>
                            @endif
                            <input type="file" name="file" class="form-control @error('file') is-invalid @enderror">
                            @error('file')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            <small class="text-muted">Sube un nuevo archivo solo si deseas reemplazar el actual.</small>
                        </div>

                        <div class="mb-4">
                            <label class="form-label fw-bold small text-muted">DESCRIPCIÓN</label>
                            <textarea name="description" class="form-control @error('description') is-invalid @enderror" rows="4">{{ old('description', $resource->description) }}</textarea>
                            @error('description')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                </div>

                <!-- Budget Section (Collapsible) -->
                <div class="accordion mb-4 shadow-sm" id="budgetEditAccordion">
                    <div class="accordion-item border-0">
                        <h2 class="accordion-header">
                            <button class="accordion-button collapsed bg-white fw-bold text-muted border-0 shadow-none" type="button" data-bs-toggle="collapse" data-bs-target="#budgetEditDetails">
                                <i class="fas fa-coins me-2 text-warning"></i> Información Financiera / Presupuesto
                            </button>
                        </h2>
                        <div id="budgetEditDetails" class="accordion-collapse collapse" data-bs-parent="#budgetEditAccordion">
                            <div class="accordion-body bg-gray-50 border-top">
                                <div class="mb-3">
                                    <label class="form-label fw-bold small text-muted">COSTO REFERENCIAL ($)</label>
                                    <div class="input-group">
                                        <span class="input-group-text">$</span>
                                        <input type="number" name="cost" class="form-control @error('cost') is-invalid @enderror" value="{{ old('cost', $resource->cost) }}" step="0.01" min="0" max="9999999.99" oninput="if(this.value.length > 10) this.value = this.value.slice(0, 10);">
                                        @error('cost')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                    <div class="form-text mt-2">
                                        <i class="fas fa-info-circle me-1"></i>
                                        Esta información se mantiene en el sistema pero no es visible en los listados principales.
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="d-flex justify-content-end gap-2 mb-5">
                    <a href="{{ route('resources.index') }}" class="btn btn-secondary">Cancelar</a>
                    <button type="submit" class="btn btn-primary px-4">Actualizar Recurso</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Modal Create Type (Same as index) -->
<div class="modal fade" id="createTypeModal" tabindex="-1" style="z-index: 1060;">
    <div class="modal-dialog modal-sm modal-dialog-centered">
        <form id="createTypeForm">
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
    .bg-gray-50 { background-color: #f8f9fa; }
</style>
@endpush

@push('scripts')
<script>
function toggleEditFileGroup(selectElement) {
    const fileGroup = document.getElementById('editFileGroup');
    const selectedOption = selectElement.options[selectElement.selectedIndex];
    const slug = selectedOption.getAttribute('data-slug');
    
    if(slug && (slug.includes('digital') || slug.includes('archivo') || slug.includes('plantilla'))) {
        fileGroup.classList.remove('d-none');
    } else {
        fileGroup.classList.add('d-none');
    }
}

// Reuse AJAX handling logic
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
    .then(response => response.json())
    .then(data => {
        if(data.success) {
            const select = document.getElementById('resourceTypeSelect');
            const option = new Option(data.type.name, data.type.id, true, true);
            option.setAttribute('data-slug', data.type.slug);
            select.add(option);
            
            // Trigger change manual to update file visibility if needed (unlikely for new type but safe)
            toggleEditFileGroup(select);

            const modal = bootstrap.Modal.getInstance(document.getElementById('createTypeModal'));
            modal.hide();
            document.getElementById('createTypeForm').reset();
            alert('Tipo creado correctamente');
        } else {
            alert('Error: ' + (data.message || 'Error desconocido'));
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('Error de conexión.');
    })
    .finally(() => {
        btn.innerHTML = originalText;
        btn.disabled = false;
    });
});
</script>
@endpush
@endsection
