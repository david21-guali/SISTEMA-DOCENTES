@extends('layouts.admin')

@section('title', 'Configuración de Cuenta')

@section('contenido')
<div class="container-fluid">
    <!-- Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h5 class="mb-0 text-dark fw-bold">Configuración de Cuenta - Perfil</h5>
    </div>

    <!-- Tabs Navigation -->
    <ul class="nav nav-tabs mb-4 border-bottom-0" id="profileTabs" role="tablist">
        <!-- Profile Tab -->
        <li class="nav-item me-1" role="presentation">
            <button class="nav-link active fw-bold pb-3" 
                    id="profile-tab" data-bs-toggle="tab" data-bs-target="#profile" type="button" role="tab" aria-selected="true">
                <i class="fas fa-user me-2"></i> PERFIL
            </button>
        </li>
        <!-- Notifications Tab -->
        <li class="nav-item me-1" role="presentation">
            <button class="nav-link fw-bold pb-3" 
                    id="notifications-tab" data-bs-toggle="tab" data-bs-target="#notifications" type="button" role="tab" aria-selected="false">
                <i class="fas fa-bell me-2"></i> NOTIFICACIONES
            </button>
        </li>
        <!-- Security Tab -->
        <li class="nav-item me-1" role="presentation">
            <button class="nav-link fw-bold pb-3" 
                    id="security-tab" data-bs-toggle="tab" data-bs-target="#security" type="button" role="tab" aria-selected="false">
                <i class="fas fa-shield-alt me-2"></i> SEGURIDAD
            </button>
        </li>
    </ul>

    <!-- Tabs Content -->
    <div class="tab-content" id="profileTabsContent">
        @if (session('status'))
            <div class="alert alert-success alert-dismissible fade show shadow-sm border-0 mb-4" role="alert">
                <i class="fas fa-check-circle me-1"></i> 
                {{ session('status') === 'profile-updated' ? 'Perfil actualizado correctamente.' : '' }}
                {{ session('status') === 'avatar-deleted' ? 'Imagen de perfil eliminada.' : '' }}
                {{ session('status') === 'password-updated' ? 'Contraseña actualizada.' : '' }}
                @if(!in_array(session('status'), ['profile-updated', 'avatar-deleted', 'password-updated']))
                    {{ session('status') }}
                @endif
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif

        <!-- TAB 1: PROFILE -->
        <div class="tab-pane fade show active" id="profile" role="tabpanel" aria-labelledby="profile-tab">
            <form method="post" action="{{ route('profile.update') }}" enctype="multipart/form-data">
                @csrf
                @method('patch')
                <input type="hidden" name="form_type" value="profile">

                <div class="row">
                    <!-- Left Column: Avatar -->
                    <div class="col-xl-4 col-lg-5 mb-4">
                        <div class="card shadow-sm border-0 h-100">
                            <div class="card-body text-center p-5">
                                <h6 class="fw-bold mb-3 text-start">Imagen de Perfil</h6>
                                <p class="text-muted small text-start mb-4">Esta imagen será visible públicamente para otros usuarios.</p>
                                
                                <div class="mb-4 position-relative d-inline-block">
                                    @if(auth()->user()->profile && auth()->user()->profile->avatar)
                                        <img src="{{ asset('storage/' . auth()->user()->profile->avatar) }}" class="rounded-circle img-fluid border border-3 border-white shadow-sm" style="width: 150px; height: 150px; object-fit: cover;">
                                    @else
                                        <div class="rounded-circle d-flex align-items-center justify-content-center mx-auto border shadow-sm" 
                                             style="width: 150px; height: 150px; background: var(--primary-gradient); font-size: 4rem; color: white; font-weight: 700;">
                                            {{ strtoupper(substr(auth()->user()->name, 0, 1)) }}
                                        </div>
                                    @endif
                                </div>
                                
                                <p class="small text-muted mb-3 fst-italic">JPG o PNG no mayor a 5 MB</p>
                                
                                <div class="d-grid gap-2">
                                    <label class="btn btn-outline-primary shadow-sm fw-bold mb-0">
                                        <i class="fas fa-upload me-2"></i> SUBIR NUEVA IMAGEN
                                        <input type="file" name="avatar" class="d-none" onchange="this.form.submit()">
                                    </label>

                                    @if(auth()->user()->profile && auth()->user()->profile->avatar)
                                        <button type="button" class="btn btn-outline-danger shadow-sm fw-bold" onclick="confirmDeleteAvatar()">
                                            <i class="fas fa-trash-alt me-2"></i> ELIMINAR IMAGEN
                                        </button>
                                    @endif
                                </div>

                            </div>
                        </div>
                    </div>

                    <!-- Right Column: Account Details -->
                    <div class="col-xl-8 col-lg-7 mb-4">
                        <div class="card shadow-sm border-0 h-100">
                            <div class="card-body p-4">
                                <h6 class="fw-bold mb-3">Detalles de la Cuenta</h6>
                                <p class="text-muted small mb-4">Revisa y actualiza la información de tu cuenta a continuación.</p>

                                <div class="mb-3">
                                    <label class="form-label small text-muted text-uppercase fw-bold">Nombre Completo</label>
                                    <input type="text" class="form-control @error('name') is-invalid @enderror" name="name" value="{{ old('name', auth()->user()->name) }}">
                                    @error('name')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                    @error('name') <div class="text-danger small mt-1">{{ $message }}</div> @enderror
                                </div>

                                <div class="row gx-3 mb-3">
                                    <div class="col-md-6">
                                        <label class="form-label small text-muted text-uppercase fw-bold">Correo Electrónico</label>
                                        <input type="email" class="form-control @error('email') is-invalid @enderror" name="email" value="{{ old('email', auth()->user()->email) }}">
                                        @error('email')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                        @error('email') <div class="text-danger small mt-1">{{ $message }}</div> @enderror
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label small text-muted text-uppercase fw-bold">Teléfono</label>
                                        <input type="text" class="form-control" name="phone" value="{{ old('phone', auth()->user()->profile->phone ?? '') }}" placeholder="Ej: +593 99 517 5269">
                                    </div>
                                </div>

                                <div class="row gx-3 mb-3">
                                    <div class="col-md-6">
                                        <label class="form-label small text-muted text-uppercase fw-bold">Departamento</label>
                                        <input type="text" class="form-control" name="department" value="{{ old('department', auth()->user()->profile->department ?? '') }}" placeholder="Ej: Ciencias Exactas">
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label small text-muted text-uppercase fw-bold">Especialidad</label>
                                        <input type="text" class="form-control" name="specialty" value="{{ old('specialty', auth()->user()->profile->specialty ?? '') }}" placeholder="Ej: Matemáticas">
                                    </div>
                                </div>

                                <div class="mb-3">
                                    <label class="form-label small text-muted text-uppercase fw-bold">Cargo</label>
                                    <input type="text" class="form-control" name="position" value="{{ old('position', auth()->user()->profile->position ?? '') }}" placeholder="Ej: Docente, Coordinador, Director">
                                </div>

                                <div class="mb-3">
                                    <label class="form-label small text-muted text-uppercase fw-bold">Ubicación</label>
                                    <input type="text" class="form-control" name="location" value="{{ old('location', auth()->user()->profile->location ?? '') }}" placeholder="Ej: Guayaquil, Ecuador">
                                </div>

                                <div class="mb-4">
                                    <label class="form-label small text-muted text-uppercase fw-bold">Sobre Mí</label>
                                    <textarea class="form-control" name="about" rows="3" placeholder="Cuéntanos un poco sobre ti...">{{ old('about', auth()->user()->profile->about ?? '') }}</textarea>
                                </div>

                                <div class="d-flex justify-content-end">
                                    <button type="submit" class="btn btn-primary px-4 fw-bold shadow-sm">
                                        GUARDAR CAMBIOS
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </form>

            @if(auth()->user()->profile && auth()->user()->profile->avatar)
                <form id="delete-avatar-form" action="{{ route('profile.avatar.destroy') }}" method="POST" class="d-none">
                    @csrf
                    @method('DELETE')
                </form>
            @endif
        </div>

        <!-- TAB 2: NOTIFICATIONS -->
        <div class="tab-pane fade" id="notifications" role="tabpanel" aria-labelledby="notifications-tab">
            <form method="post" action="{{ route('profile.update') }}">
                @csrf
                @method('patch')
                <input type="hidden" name="form_type" value="notifications">
                <!-- Needs a hidden input for name to pass validation if required, or partial update -->
                <input type="hidden" name="name" value="{{ auth()->user()->name }}">
                <input type="hidden" name="email" value="{{ auth()->user()->email }}">

                <div class="row">
                    <div class="col-lg-8 offset-lg-2">
                        <div class="card shadow-sm border-0 mb-4">
                            <div class="card-body p-4">
                                <h6 class="fw-bold mb-4">Preferencias de Notificación</h6>
                                <p class="text-muted small mb-4">Elige qué tipos de actualizaciones deseas recibir.</p>

                                <div class="mb-4">
                                    <h6 class="small text-muted text-uppercase fw-bold mb-3">Notificaciones del Sistema</h6>
                                    
                                    @php 
                                        $prefs = auth()->user()->profile->notification_preferences ?? []; 
                                    @endphp

                                    <div class="form-check mb-2">
                                        <input class="form-check-input" type="checkbox" name="notification_preferences[meetings]" id="notifyMeetings" 
                                            {{ ($prefs['meetings'] ?? true) ? 'checked' : '' }}>
                                        <label class="form-check-label text-dark" for="notifyMeetings">Nuevas reuniones programadas</label>
                                    </div>
                                    <div class="form-check mb-2">
                                        <input class="form-check-input" type="checkbox" name="notification_preferences[projects]" id="notifyProjects"
                                            {{ ($prefs['projects'] ?? true) ? 'checked' : '' }}>
                                        <label class="form-check-label text-dark" for="notifyProjects">Actualizaciones de proyectos</label>
                                    </div>
                                    <div class="form-check mb-2">
                                        <input class="form-check-input" type="checkbox" name="notification_preferences[tasks]" id="notifyTasks"
                                            {{ ($prefs['tasks'] ?? true) ? 'checked' : '' }}>
                                        <label class="form-check-label text-dark" for="notifyTasks">Asignación y vencimiento de tareas</label>
                                    </div>
                                    <div class="form-check mb-2">
                                        <input class="form-check-input" type="checkbox" name="notification_preferences[resources]" id="notifyResources"
                                            {{ ($prefs['resources'] ?? true) ? 'checked' : '' }}>
                                        <label class="form-check-label text-dark" for="notifyResources">Recursos compartidos</label>
                                    </div>
                                    <div class="form-check mb-2">
                                        <input class="form-check-input" type="checkbox" name="notification_preferences[reminders]" id="notifyReminders"
                                            {{ ($prefs['reminders'] ?? true) ? 'checked' : '' }}>
                                        <label class="form-check-label text-dark" for="notifyReminders">Recordatorios generales</label>
                                    </div>
                                </div>

                                <div class="mb-4 pt-3 border-top">
                                    <h6 class="small text-muted text-uppercase fw-bold mb-3">Canales de Comunicación</h6>
                                    
                                    <div class="form-check form-switch mb-3">
                                        <input class="form-check-input" type="checkbox" name="notification_preferences[email_enabled]" id="emailEnabled"
                                            {{ ($prefs['email_enabled'] ?? true) ? 'checked' : '' }}>
                                        <label class="form-check-label text-dark fw-bold" for="emailEnabled">
                                            Recibir notificaciones por Correo Electrónico
                                        </label>
                                        <p class="text-muted small mb-0">Si se desactiva, solo verás las notificaciones dentro de la plataforma.</p>
                                    </div>
                                </div>

                                <div class="d-flex justify-content-end">
                                    <button type="submit" class="btn btn-primary px-4 fw-bold shadow-sm">
                                        GUARDAR PREFERENCIAS
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </form>
        </div>

        <!-- TAB 3: SECURITY -->
        <div class="tab-pane fade" id="security" role="tabpanel" aria-labelledby="security-tab">
            <div class="row justify-content-center">
                <div class="col-lg-8">
                    <!-- Update Password -->
                    <div class="card shadow-sm border-0 mb-4">
                        <div class="card-body p-4">
                            @include('profile.partials.update-password-form')
                        </div>
                    </div>

                    <!-- Delete Account -->
                    <div class="card shadow-sm border-0 border-start border-danger border-3">
                        <div class="card-body p-4">
                            @include('profile.partials.delete-user-form')
                        </div>
                    </div>
                </div>
            </div>
        </div>

    </div>
</div>

@push('styles')
<style>
    .nav-tabs .nav-link {
        color: #6c757d;
        font-size: 0.85rem;
        letter-spacing: 0.5px;
        border: none !important;
        background-color: transparent !important;
    }
    .nav-tabs .nav-link:hover {
        color: #4e73df;
        border-bottom-color: #d1d3e2 !important; 
    }
    .nav-tabs .nav-link.active {
        color: #4e73df !important;
        background-color: transparent !important;
        border-bottom: 2px solid #4e73df !important;
        border-top: none !important;
        border-left: none !important;
        border-right: none !important;
    }
    /* Hide tab content border if bootstrap adds one */
    .tab-content {
        border: none;
    }
</style>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        @if(session('welcome_new_user'))
            Swal.fire({
                title: '¡Bienvenido al Sistema!',
                text: 'Por favor, completa tu perfil y configura tus preferencias para empezar.',
                icon: 'info',
                confirmButtonText: 'Entendido',
                confirmButtonColor: '#4e73df'
            });
        @endif
    });

    function confirmDeleteAvatar() {
        Swal.fire({
            title: '¿Estás seguro?',
            text: "Tu imagen de perfil será eliminada permanentemente.",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#e74a3b',
            cancelButtonColor: '#858796',
            confirmButtonText: 'Sí, eliminar',
            cancelButtonText: 'Cancelar'
        }).then((result) => {
            if (result.isConfirmed) {
                document.getElementById('delete-avatar-form').submit();
            }
        });
    }
</script>
@endpush
@endsection
