@extends('layouts.admin')

@section('title', 'Editar Reunión')

@section('contenido')
<div class="container-fluid">
    <div class="row mb-4 align-items-center">
        <div class="col">
            <h2 class="fw-bold mb-0 text-dark">Editar Reunión</h2>
            <p class="text-muted">Modificar detalles del evento</p>
        </div>
        <div class="col-auto">
             <!-- Buttons moved to bottom -->
        </div>
    </div>

    <form action="{{ route('meetings.update', $meeting) }}" method="POST" id="editMeetingForm" class="row" novalidate>
        @csrf
        @method('PUT')
        
        <!-- COLUMNA PRINCIPAL (Detalles) -->
        <div class="col-lg-8">
            <h5 class="fw-bold fs-5 mb-4 text-dark">Detalles del Evento</h5>
            
            <div class="card border-0 shadow-sm rounded-4 mb-4">
                <div class="card-body p-4">
                    <!-- Título -->
                    <div class="mb-4">
                        <label class="form-label text-uppercase text-muted fw-bold small ls-1">TÍTULO DEL EVENTO *</label>
                        <input type="text" 
                               class="form-control form-control-lg border-2 bg-light @error('title') is-invalid @enderror" 
                               name="title" 
                               value="{{ old('title', $meeting->title) }}" 
                               placeholder="Ej: Reunión de avance...">
                        @error('title')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="row">
                        <!-- Proyecto -->
                        <div class="col-md-6 mb-4">
                            <label class="form-label text-uppercase text-muted fw-bold small ls-1">PROYECTO (OPCIONAL)</label>
                            <select class="form-select form-select-lg border-2 bg-light @error('project_id') is-invalid @enderror" name="project_id">
                                <option value="">Seleccionar Proyecto</option>
                                @foreach($projects as $project)
                                    <option value="{{ $project->id }}" 
                                            {{ old('project_id', $meeting->project_id) == $project->id ? 'selected' : '' }}>
                                        {{ $project->title }}
                                    </option>
                                @endforeach
                            </select>
                            @error('project_id')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        
                        <!-- Uso la ubicación aquí -->
                        <div class="col-md-6 mb-4">
                             <label class="form-label text-uppercase text-muted fw-bold small ls-1">UBICACIÓN / ENLACE *</label>
                            <input type="text" 
                                   class="form-control form-control-lg border-2 bg-light @error('location') is-invalid @enderror" 
                                   name="location"
                                   value="{{ old('location', $meeting->location) }}"
                                   placeholder="Sala o Enlace">
                            @error('location')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <!-- Tipo (Visual) -->
                    <div class="mb-4">
                        <label class="form-label text-uppercase text-muted fw-bold small ls-1 d-block mb-2">TIPO DE REUNIÓN *</label>
                        <div class="btn-group w-100 w-md-50" role="group">
                            <input type="radio" class="btn-check" name="type" id="virtual" value="virtual" {{ old('type', $meeting->type) == 'virtual' ? 'checked' : '' }}>
                            <label class="btn btn-outline-primary rounded-pill me-2 border-0 fw-bold" for="virtual">
                                <i class="fas fa-video me-2"></i>Virtual
                            </label>

                            <input type="radio" class="btn-check" name="type" id="presencial" value="presencial" {{ old('type', $meeting->type) == 'presencial' ? 'checked' : '' }}>
                            <label class="btn btn-outline-primary rounded-pill border-0 fw-bold" for="presencial">
                                <i class="fas fa-map-marker-alt me-2"></i>Presencial
                            </label>
                        </div>
                        @error('type')
                            <div class="text-danger small mt-1">{{ $message }}</div>
                        @enderror
                    </div>

                    <!-- Horario -->
                    <h5 class="fw-bold fs-5 mt-5 mb-4 text-dark">Horario</h5>
                    <div class="row">
                        <div class="col-md-12 mb-4">
                            <label class="form-label text-uppercase text-muted fw-bold small ls-1">FECHA Y HORA DE INICIO *</label>
                            <div class="input-group">
                                <span class="input-group-text bg-light border-2 border-end-0 text-muted"><i class="far fa-calendar-alt"></i></span>
                                <input type="datetime-local" 
                                       class="form-control form-control-lg border-2 bg-light border-start-0 ps-0 @error('meeting_date') is-invalid @enderror" 
                                       name="meeting_date" 
                                       value="{{ old('meeting_date', $meeting->meeting_date->format('Y-m-d\TH:i')) }}">
                            </div>
                             @error('meeting_date')
                                <div class="text-danger small mt-1">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                    
                     <!-- Descripción -->
                    <div class="mb-4">
                         <label class="form-label text-uppercase text-muted fw-bold small ls-1">DESCRIPCIÓN / AGENDA *</label>
                         <textarea class="form-control border-2 bg-light @error('description') is-invalid @enderror" 
                                   name="description" 
                                   rows="4">{{ old('description', $meeting->description) }}</textarea>
                         @error('description')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                     <!-- Notas -->
                    <div class="mb-4">
                         <label class="form-label text-uppercase text-muted fw-bold small ls-1">NOTAS DE LA REUNIÓN</label>
                         <textarea class="form-control border-2 bg-light @error('notes') is-invalid @enderror" 
                                   name="notes" 
                                   rows="3"
                                   placeholder="Acuerdos y notas...">{{ old('notes', $meeting->notes) }}</textarea>
                    </div>

                    <!-- Botones de Acción (Moved) -->
                    <div class="d-flex justify-content-end pt-3 border-top">
                        <a href="{{ route('meetings.show', $meeting) }}" class="btn btn-outline-secondary fw-bold rounded-pill px-4">
                            Cancelar
                        </a>
                        <button type="submit" form="editMeetingForm" class="btn btn-primary fw-bold rounded-pill px-4 ms-2">
                            Guardar Cambios
                        </button>
                    </div>

                </div>
            </div>
        </div>

        <!-- SIDEBAR (Opciones) -->
        <div class="col-lg-4">
            <h5 class="fw-bold fs-5 mb-4 text-dark">Estado y Participantes</h5>
            
            <div class="card border-0 shadow-sm rounded-4 mb-4">
                <div class="card-body p-4">
                    <!-- Status -->
                    <div class="mb-4">
                        <div class="form-check mb-3 custom-radio">
                            <input class="form-check-input md-radio" type="radio" name="status" id="status_pending" value="pendiente" {{ $meeting->status == 'pendiente' ? 'checked' : '' }}>
                            <label class="form-check-label" for="status_pending">
                                <span class="d-block fw-bold text-dark">Pendiente</span>
                                <span class="d-block small text-muted">Programada</span>
                            </label>
                        </div>
                        <div class="form-check mb-3 custom-radio">
                             <input class="form-check-input md-radio" type="radio" name="status" id="status_completed" value="completada" {{ $meeting->status == 'completada' ? 'checked' : '' }}>
                            <label class="form-check-label" for="status_completed">
                                <span class="d-block fw-bold text-dark">Completada</span>
                                <span class="d-block small text-muted">Realizada</span>
                            </label>
                        </div>
                         <div class="form-check custom-radio">
                             <input class="form-check-input md-radio" type="radio" name="status" id="status_cancelled" value="cancelada" {{ $meeting->status == 'cancelada' ? 'checked' : '' }}>
                            <label class="form-check-label" for="status_cancelled">
                                <span class="d-block fw-bold text-dark">Cancelada</span>
                                <span class="d-block small text-muted">No realizada</span>
                            </label>
                        </div>
                    </div>
                </div>
            </div>

            <div class="card border-0 shadow-sm rounded-4">
                <div class="card-body p-4">
                    <h5 class="fw-bold mb-3 text-dark">Participantes</h5>
                    
                     <div class="mb-3">
                        <input type="text" class="form-control border-2 bg-light" id="searchParticipants" placeholder="Buscar...">
                    </div>

                    <div class="participants-list" style="max-height: 300px; overflow-y: auto;">
                         @php
                            $currentParticipants = old('participants', $meeting->participants->pluck('id')->toArray());
                        @endphp
                         @foreach($users as $user)
                            <div class="participant-item d-flex align-items-center mb-3 p-2 rounded hover-bg-light">
                                <div class="form-check w-100 d-flex align-items-center m-0">
                                    <input class="form-check-input me-3" type="checkbox" name="participants[]" value="{{ $user->id }}" id="participant-{{ $user->id }}" {{ $meeting->participants->contains($user) ? 'checked' : '' }}>
                                    <label class="form-check-label d-flex align-items-center w-100 cursor-pointer" for="participant-{{ $user->id }}">
                                        @if($user->profile && $user->profile->avatar)
                                            <img src="{{ asset('storage/' . $user->profile->avatar) }}" 
                                                 class="rounded-circle me-2 shadow-sm" 
                                                 style="width: 36px; height: 36px; object-fit: cover;">
                                        @else
                                            <div class="avatar-sm rounded-circle text-white d-flex align-items-center justify-content-center fw-bold me-2"
                                                 style="width: 36px; height: 36px; background: linear-gradient(135deg, {{ ['#4e73df', '#1cc88a', '#36b9cc', '#f6c23e'][$loop->index % 4] }} 0%, {{ ['#224abe', '#13855c', '#258391', '#dda20a'][$loop->index % 4] }} 100%);">
                                                {{ strtoupper(substr($user->name, 0, 1)) }}
                                            </div>
                                        @endif
                                    <div>
                                        <div class="fw-bold text-dark small">{{ $user->name }}</div>
                                        <div class="text-muted" style="font-size: 0.7rem;">{{ $user->email }}</div>
                                    </div>
                                </label>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>

            @error('participants')
                <div class="text-danger small mt-2 d-block">
                    <i class="fas fa-exclamation-circle me-1"></i>{{ $message }}
                </div>
            @enderror
        </div>
    </form>
</div>

@push('styles')
    <link rel="stylesheet" href="{{ asset('assets/back/css/meetings.css') }}">
@endpush
@endsection

@section('scripts')
<script>
    // Project members mapping
    const projectMembers = {
        @foreach($projects as $project)
            {{ $project->id }}: [
                @foreach($project->team as $member)
                    {{ $member->user->id }},
                @endforeach
            ],
        @endforeach
    };

    const projectSelect = document.querySelector('select[name="project_id"]');
    const participantItems = document.querySelectorAll('.participant-item');
    const searchInput = document.getElementById('searchParticipants');

    // Filter participants by project
    function filterByProject() {
        const selectedProjectId = parseInt(projectSelect.value);
        
        participantItems.forEach(function(item) {
            const checkbox = item.querySelector('input[type="checkbox"]');
            const userId = parseInt(checkbox.value);
            const wasChecked = checkbox.checked;
            
            // If no project selected, show all
            if (!selectedProjectId) {
                item.style.display = 'block';
                return;
            }
            
            // Show only if user is in selected project
            if (projectMembers[selectedProjectId] && projectMembers[selectedProjectId].includes(userId)) {
                item.style.display = 'block';
            } else {
                item.style.display = 'none';
                // Only uncheck if not previously selected
                if (!wasChecked) {
                    checkbox.checked = false;
                }
            }
        });
    }

    // Search functionality
    searchInput.addEventListener('input', function() {
        const search = this.value.toLowerCase();
        participantItems.forEach(function(item) {
            // Only search within visible items
            if (item.style.display === 'none') return;
            
            const text = item.textContent.toLowerCase();
            item.style.display = text.includes(search) ? 'block' : 'none';
        });
    });

    // Listen to project selection changes
    projectSelect.addEventListener('change', filterByProject);

    // Initial filter on page load
    filterByProject();
</script>
@endsection
