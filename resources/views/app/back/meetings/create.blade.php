@extends('layouts.admin')

@section('title', 'Programar Reunión')

@section('contenido')
<div class="container-fluid">
    <div class="row mb-4 align-items-center">
        <div class="col">
            <h2 class="fw-bold mb-0 text-dark">Programar Reunión</h2>
            <p class="text-muted">Crear un nuevo evento de seguimiento</p>
        </div>
        <div class="col-auto">
            <!-- Buttons moved to bottom -->
        </div>
    </div>

    <form action="{{ route('meetings.store') }}" method="POST" id="createMeetingForm" class="row" novalidate>
        @csrf
        
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
                               value="{{ old('title') }}" 
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
                                    <option value="{{ $project->id }}" {{ old('project_id') == $project->id ? 'selected' : '' }}>
                                        {{ $project->title }}
                                    </option>
                                @endforeach
                            </select>
                            @error('project_id')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        
                        <!-- Ubicación (Replaces Topic) -->
                        <div class="col-md-6 mb-4">
                            <label class="form-label text-uppercase text-muted fw-bold small ls-1">UBICACIÓN / ENLACE *</label>
                            <input type="text" 
                                   class="form-control form-control-lg border-2 bg-light @error('location') is-invalid @enderror" 
                                   name="location"
                                   value="{{ old('location') }}"
                                   placeholder="Sala 1 o URL (Zoom/Meet)">
                             @error('location')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <!-- Tipo de Ubicación (Visual) -->
                    <div class="mb-4">
                        <label class="form-label text-uppercase text-muted fw-bold small ls-1 d-block mb-2">TIPO DE REUNIÓN *</label>
                        <div class="btn-group w-100 w-md-50" role="group">
                            <input type="radio" class="btn-check" name="type" id="virtual" value="virtual" {{ old('type', 'virtual') == 'virtual' ? 'checked' : '' }}>
                            <label class="btn btn-outline-primary rounded-pill me-2 border-0 fw-bold" for="virtual">
                                <i class="fas fa-video me-2"></i>Virtual
                            </label>

                            <input type="radio" class="btn-check" name="type" id="presencial" value="presencial" {{ old('type') == 'presencial' ? 'checked' : '' }}>
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
                                       value="{{ old('meeting_date') }}">
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
                                   rows="4" 
                                   placeholder="Detalles sobre los puntos a tratar...">{{ old('description') }}</textarea>
                         @error('description')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <!-- Botones de Acción (Moved) -->
                    <div class="d-flex justify-content-end pt-3 border-top">
                        <a href="{{ route('meetings.index') }}" class="btn btn-outline-secondary fw-bold rounded-pill px-4">
                            Cancelar
                        </a>
                        <button type="submit" form="createMeetingForm" class="btn btn-primary fw-bold rounded-pill px-4 ms-2">
                            Publicar Evento
                        </button>
                    </div>

                </div>
            </div>
        </div>

        <!-- BARRA LATERAL (Opciones) -->
        <div class="col-lg-4">
            <h5 class="fw-bold fs-5 mb-4 text-dark">Privacidad y Estado</h5>
            
            <div class="card border-0 shadow-sm rounded-4 mb-4">
                <div class="card-body p-4 text-center">
                    <div class="mb-0">
                        <input type="hidden" name="status" value="pendiente">
                        <i class="fas fa-clock fa-3x text-primary opacity-25 mb-3"></i>
                        <h6 class="fw-bold text-dark">Estado: Pendiente</h6>
                        <p class="small text-muted mb-0">La reunión se programará como pendiente por defecto.</p>
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
                         @foreach($users as $user)
                            <div class="participant-item d-flex align-items-center mb-3 p-2 rounded hover-bg-light">
                                <div class="form-check w-100 d-flex align-items-center m-0">
                                    <input class="form-check-input me-3 rounded-circle" 
                                           style="width: 1.25em; height: 1.25em;"
                                           type="checkbox" 
                                           name="participants[]" 
                                           value="{{ $user->id }}" 
                                           id="user-{{ $user->id }}"
                                           {{ $user->id == auth()->id() ? 'checked' : '' }}>
                                           
                                    <label class="form-check-label d-flex align-items-center flex-grow-1 cursor-pointer" for="user-{{ $user->id }}">
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

<script>
    window.MeetingConfig = {
        projectMembers: {
            @foreach($projects as $project)
                {{ $project->id }}: [
                    @foreach($project->team as $member)
                        {{ $member->user->id }},
                    @endforeach
                ],
            @endforeach
        }
    };
</script>
@vite(['resources/js/pages/meetings-form.js'])
