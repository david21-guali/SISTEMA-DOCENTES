@extends('layouts.admin')

@section('title', $meeting->title)

@section('contenido')
<div class="container-fluid">
    <!-- Breadcrumb / Back -->
    <div class="mb-4">
        <a href="{{ route('meetings.index') }}" class="text-decoration-none text-muted small fw-bold text-uppercase ls-1">
            <i class="fas fa-arrow-left me-1"></i> Volver a Reuniones
        </a>
    </div>

    <div class="row g-4 justify-content-center">
        <!-- Main Content Column -->
        <div class="col-lg-8">
            
            <!-- HERO CARD (Like the reference image) -->
            <div class="card border-0 shadow-sm rounded-4 overflow-hidden mb-4">
                <div class="card-body p-0">
                    <!-- Header with Color Strip -->
                    <div class="p-4 bg-white border-bottom border-light relative" style="border-top: 4px solid #4f46e5;">
                        <!-- Status Badge (Floating Top Right) -->
                        <div class="d-flex justify-content-between align-items-start mb-3">
                            <span class="badge rounded-pill px-3 py-2 fw-bold shadow-sm {{ $meeting->status === 'pendiente' ? 'bg-warning text-dark' : ($meeting->status === 'completada' ? 'bg-success text-white' : 'bg-secondary text-white') }}" style="font-size: 0.8rem; letter-spacing: 0.5px;">
                                {{ strtoupper($meeting->status_label) }}
                            </span>
                            
                            <!-- Admin Actions Dropdown -->
                            @if($meeting->created_by == auth()->id() || auth()->user()->hasRole('admin'))
                            <div class="dropdown">
                                <button class="btn btn-white border btn-sm rounded-circle shadow-sm hover-scale" type="button" data-bs-toggle="dropdown">
                                    <i class="fas fa-ellipsis-h text-dark"></i>
                                </button>
                                <ul class="dropdown-menu dropdown-menu-end shadow-lg border-0 rounded-4">
                                    <li><a class="dropdown-item fw-bold small" href="{{ route('meetings.edit', $meeting) }}">Editar</a></li>
                                    @if($meeting->status === 'pendiente')
                                    <li>
                                        <form action="{{ route('meetings.sendReminders', $meeting) }}" method="POST">
                                            @csrf
                                            <button type="submit" class="dropdown-item fw-bold small">Enviar Recordatorios</button>
                                        </form>
                                    </li>
                                    <li><hr class="dropdown-divider"></li>
                                    <li><button class="dropdown-item fw-bold small text-danger" type="button" onclick="cancelMeeting()">Cancelar Reunión</button></li>
                                    @endif
                                </ul>
                            </div>
                            @endif
                        </div>

                        <!-- Title -->
                        <h2 class="fw-bold text-dark mb-2" style="font-size: 1.85rem; letter-spacing: -0.5px;">{{ $meeting->title }}</h2>
                        
                        <!-- Project Link -->
                        @if($meeting->project)
                            <div class="mb-4">
                                <a href="{{ route('projects.show', $meeting->project) }}" class="text-decoration-none fw-bold badge bg-primary bg-opacity-10 text-primary border border-primary border-opacity-10 px-3 py-2 rounded-pill">
                                    <i class="fas fa-folder me-1"></i> 
                                    {{ $meeting->project->title }}
                                </a>
                            </div>
                        @endif

                        <!-- Key Details Row (Grid) -->
                        <div class="row g-4 mt-2">
                            <!-- Date -->
                            <div class="col-sm-6">
                                <div class="d-flex align-items-center p-2 rounded-3 hover-bg-light transition-all">
                                    <div class="icon-square bg-primary text-white shadow-sm rounded-3 me-3 d-flex align-items-center justify-content-center" style="width: 50px; height: 50px; background: linear-gradient(135deg, #3b82f6 0%, #2563eb 100%);">
                                        <i class="far fa-calendar-alt fa-lg"></i>
                                    </div>
                                    <div>
                                        <div class="text-xs text-uppercase text-primary fw-bold ls-1 mb-1">Fecha</div>
                                        <div class="fw-bold text-dark fs-5">{{ $meeting->meeting_date->translatedFormat('d M, Y') }}</div>
                                    </div>
                                </div>
                            </div>
                            
                            <!-- Time -->
                            <div class="col-sm-6">
                                <div class="d-flex align-items-center p-2 rounded-3 hover-bg-light transition-all">
                                    <div class="icon-square text-white shadow-sm rounded-3 me-3 d-flex align-items-center justify-content-center" style="width: 50px; height: 50px; background: linear-gradient(135deg, #8b5cf6 0%, #7c3aed 100%);">
                                        <i class="far fa-clock fa-lg"></i>
                                    </div>
                                    <div>
                                        <div class="text-xs text-uppercase text-purple fw-bold ls-1 mb-1">Hora</div>
                                        <div class="fw-bold text-dark fs-5">{{ $meeting->meeting_date->format('H:i') }} hrs</div>
                                    </div>
                                </div>
                            </div>

                            <!-- Location -->
                            <div class="col-12">
                                <div class="d-flex align-items-center bg-white border shadow-sm rounded-4 p-3 mt-3">
                                    <div class="icon-square text-white shadow-sm rounded-circle me-3 d-flex align-items-center justify-content-center" style="width: 42px; height: 42px; background: linear-gradient(135deg, #ef4444 0%, #dc2626 100%);">
                                        <i class="fas fa-map-marker-alt"></i>
                                    </div>
                                    <div class="flex-grow-1">
                                        <div class="text-xs text-uppercase text-danger fw-bold ls-1 mb-1">{{ $meeting->type === 'virtual' ? 'Enlace Virtual' : 'Ubicación Física' }}</div>
                                        <div class="fw-bold text-dark text-break">{{ $meeting->location ?? 'No especificada' }}</div>
                                    </div>
                                    @if($meeting->location && ($meeting->type === 'virtual' ? (Str::startsWith($meeting->location, ['http://', 'https://']) || filter_var($meeting->location, FILTER_VALIDATE_URL)) : true))
                                        <a href="{{ $meeting->type === 'virtual' ? $meeting->location : 'https://www.google.com/maps/search/?api=1&query='.urlencode($meeting->location) }}" 
                                           target="_blank" 
                                           class="btn btn-dark rounded-pill px-4 fw-bold shadow-sm hover-scale">
                                            @if($meeting->type === 'virtual')
                                                Unirse <i class="fas fa-external-link-alt ms-1"></i>
                                            @else
                                                Ver Dirección <i class="fas fa-map-marked-alt ms-1"></i>
                                            @endif
                                        </a>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Agenda / Description Section -->
                    <div class="p-4 bg-white">
                        <h6 class="text-uppercase text-muted small fw-bold ls-1 mb-3">Agenda & Detalles</h6>
                        @if($meeting->description)
                            <div class="text-secondary" style="line-height: 1.7; font-size: 0.95rem;">
                                {!! nl2br(e($meeting->description)) !!}
                            </div>
                        @else
                            <div class="text-center py-4 bg-light rounded-4 border border-dashed">
                                <i class="fas fa-paragraph text-muted mb-2 opacity-50" style="font-size: 1.5rem;"></i>
                                <p class="text-muted small mb-0">Sin descripción detallada para esta reunión.</p>
                            </div>
                        @endif
                    </div>
                </div>
            </div>

            <!-- Notes Section -->
            @if($meeting->notes)
            <div class="card border-0 shadow-sm rounded-4 mb-4">
                <div class="card-body p-4">
                    <h6 class="text-uppercase text-dark small fw-bold ls-1 mb-3">
                        <i class="fas fa-clipboard-list me-2 text-primary"></i>Notas y Acuerdos
                    </h6>
                    <div class="p-3 bg-blue-50 rounded-4 text-dark" style="background-color: #f8f9fc;">
                        {!! nl2br(e($meeting->notes)) !!}
                    </div>
                </div>
            </div>
            @endif

        </div>

        <!-- Sidebar Column -->
        <div class="col-lg-4">
            
            <!-- Participants Card (Visual Stack Style) -->
            <div class="card border-0 shadow-sm rounded-4 mb-4">
                <div class="card-body p-4">
                    <div class="d-flex justify-content-between align-items-center mb-4">
                        <h6 class="text-uppercase text-primary fw-bold ls-1 mb-0" style="font-size: 0.8rem;">Equipo & Participantes</h6>
                        <span class="badge bg-primary text-white rounded-pill shadow-sm">{{ $meeting->participants->count() }}</span>
                    </div>

                    <!-- Organizer -->
                    <div class="mb-4">
                        <div class="d-flex align-items-center p-3 rounded-4 bg-light">
                            <div class="avatar-md rounded-circle bg-dark text-white d-flex align-items-center justify-content-center fw-bold me-3 shadow-sm border border-2 border-white" style="width: 45px; height: 45px; background: linear-gradient(135deg, #1f2937 0%, #111827 100%);">
                                {{ strtoupper(substr($meeting->creator->user->name, 0, 1)) }}
                            </div>
                            <div>
                                <div class="fw-bold text-dark">{{ $meeting->creator->user->name }}</div>
                                <div class="text-muted text-xs text-uppercase fw-bold">Organizador</div>
                            </div>
                        </div>
                    </div>

                    <hr class="my-4 opacity-50">

                    <!-- Participants Stack -->
                    <div class="mb-4">
                        <div class="d-flex align-items-center ps-2">
                             @foreach($meeting->participants->take(5) as $index => $participant)
                                @if($participant->user->profile && $participant->user->profile->avatar)
                                    <img src="{{ asset('storage/' . $participant->user->profile->avatar) }}" 
                                         class="rounded-circle border border-2 border-white shadow-sm" 
                                         style="width: 38px; height: 38px; margin-left: -12px; z-index: {{ 10 - $index }}; object-fit: cover;" 
                                         title="{{ $participant->user->name }} ({{ ucfirst($participant->pivot->attendance) }})">
                                @else
                                    <div class="avatar-sm rounded-circle bg-white border border-2 border-white text-secondary d-flex align-items-center justify-content-center fw-bold shadow-sm" 
                                         style="width: 38px; height: 38px; margin-left: -12px; z-index: {{ 10 - $index }};"
                                         title="{{ $participant->user->name }} ({{ ucfirst($participant->pivot->attendance) }})">
                                        {{ strtoupper(substr($participant->user->name, 0, 1)) }}
                                    </div>
                                @endif
                            @endforeach
                            @if($meeting->participants->count() > 5)
                                <div class="avatar-sm rounded-circle bg-light border border-2 border-white text-muted d-flex align-items-center justify-content-center fw-bold shadow-sm" 
                                     style="width: 38px; height: 38px; margin-left: -12px; z-index: 0;">
                                    +{{ $meeting->participants->count() - 5 }}
                                </div>
                            @endif
                        </div>
                    </div>

                    <!-- Attendance List (Compact) -->
                    <div class="vstack gap-2">
                         @foreach($meeting->participants as $participant)
                            <div class="d-flex align-items-center justify-content-between p-2 rounded hover-bg-light">
                                <span class="fw-bold text-dark small" style="font-size: 0.85rem;">{{ Str::limit($participant->user->name, 20) }}</span>
                                @if($participant->pivot->attendance === 'confirmada' || $participant->pivot->attendance === 'asistio') 
                                    <i class="fas fa-check-circle text-success small" title="Confirmado/Asistió"></i>
                                @elseif($participant->pivot->attendance === 'rechazada') 
                                    <i class="fas fa-times-circle text-danger small" title="Rechazado/No Asistió"></i>
                                @else 
                                    <i class="far fa-circle text-muted small" title="Pendiente"></i>
                                @endif
                            </div>
                        @endforeach
                    </div>

                </div>
                
                <!-- Action Footer (RSVP) -->
                @if($meeting->status === 'pendiente' && $meeting->participants->contains('id', auth()->id()) && auth()->id() !== $meeting->created_by)
                    <div class="card-footer bg-light p-3 border-0">
                         @php $myAttendance = $meeting->participants->find(auth()->id())->pivot->attendance; @endphp
                         @if($myAttendance === 'pendiente')
                            <p class="text-center small fw-bold text-dark mb-2">¿Asistirás?</p>
                            <div class="d-flex gap-2">
                                <form id="attendance-form" action="{{ route('meetings.updateAttendance', $meeting) }}" method="POST" class="w-100 d-flex gap-2">
                                    @csrf
                                    <input type="hidden" name="attendance" id="attendance-input">
                                    <input type="hidden" name="rejection_reason" id="rejection-reason-input">
                                    <button type="button" onclick="confirmAttendance()" class="btn btn-dark btn-sm flex-grow-1 rounded-pill fw-bold">Sí</button>
                                    <button type="button" onclick="rejectAttendance()" class="btn btn-outline-secondary btn-sm flex-grow-1 rounded-pill fw-bold bg-white">No</button>
                                </form>
                            </div>
                        @else
                            <div class="text-center small py-2">
                                <span class="badge {{ $myAttendance == 'confirmada' ? 'bg-success' : 'bg-danger' }} rounded-pill">
                                    {{ $myAttendance == 'confirmada' ? 'Asistencia Confirmada' : 'Invitación Rechazada' }}
                                </span>
                            </div>
                        @endif
                    </div>
                @endif
            </div>

            <!-- Complete Action -->
            @if($meeting->status === 'pendiente' && $meeting->is_past && ($meeting->created_by == auth()->id() || auth()->user()->hasRole('admin')))
                <div class="card border-0 shadow-lg rounded-4 text-white overflow-hidden" style="background: linear-gradient(135deg, #4f46e5 0%, #7c3aed 100%);">
                    <div class="card-body p-4 position-relative">
                        <i class="fas fa-flag-checkered position-absolute text-white" style="font-size: 8rem; right: -20px; bottom: -20px; opacity: 0.15; transform: rotate(-15deg);"></i>
                        <h6 class="fw-bold mb-2 text-white ls-1 text-uppercase" style="font-size: 0.8rem;">Acción Requerida</h6>
                        <h5 class="fw-bold mb-2 text-white">Reunión Finalizada</h5>
                        <p class="text-white small mb-4 opacity-100" style="max-width: 90%;">La fecha ha pasado. Registra las conclusiones para cerrar el evento.</p>
                        <button type="button" class="btn btn-white bg-white text-dark fw-bold rounded-pill w-100 shadow-sm transition-all hover-scale" data-bs-toggle="modal" data-bs-target="#completeModal">
                            <i class="fas fa-check-circle me-1"></i> Completar Reunión
                        </button>
                    </div>
                </div>

                <!-- Modal for Completion -->
                <div class="modal fade" id="completeModal" tabindex="-1">
                    <div class="modal-dialog modal-dialog-centered">
                        <div class="modal-content rounded-4 border-0 shadow-lg">
                            <div class="modal-header border-0 pb-0">
                                <h5 class="modal-title fw-bold">Finalizar Reunión</h5>
                                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                            </div>
                            <div class="modal-body pt-2">
                                <form action="{{ route('meetings.complete', $meeting) }}" method="POST">
                                    @csrf
                                    <div class="mb-3">
                                        <label class="small fw-bold text-muted mb-2">Asistencia</label>
                                        <div class="d-flex flex-wrap gap-2">
                                             @foreach($meeting->participants as $participant)
                                                <div class="form-check form-check-inline m-0">
                                                    <input class="btn-check" type="checkbox" name="attended[]" value="{{ $participant->id }}" id="att-{{ $participant->id }}" {{ $participant->pivot->attendance === 'confirmada' ? 'checked' : '' }}>
                                                    <label class="btn btn-outline-dark btn-sm rounded-pill" for="att-{{ $participant->id }}">{{ $participant->user->name }}</label>
                                                </div>
                                            @endforeach
                                        </div>
                                    </div>
                                    <div class="mb-3">
                                        <label class="small fw-bold text-muted mb-2">Conclusiones</label>
                                        <textarea class="form-control rounded-3 bg-light border-0" name="notes" rows="4" placeholder="Escribe los acuerdos..."></textarea>
                                    </div>
                                    <button type="submit" class="btn btn-dark w-100 rounded-pill fw-bold">Guardar y Finalizar</button>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            @endif

        </div>
    </div>
</div>

@push('styles')
    <link rel="stylesheet" href="{{ asset('assets/back/css/meetings.css') }}">
@endpush

@section('scripts')
<script>
    function confirmAttendance() {
        document.getElementById('attendance-input').value = 'confirmada';
        document.getElementById('attendance-form').submit();
    }

    function rejectAttendance() {
        Swal.fire({
            title: '¿Rechazar?',
            input: 'text',
            inputPlaceholder: 'Motivo...',
            showCancelButton: true,
            confirmButtonText: 'Confirmar',
            confirmButtonColor: '#343a40'
        }).then((result) => {
            if (result.isConfirmed) {
                document.getElementById('attendance-input').value = 'rechazada';
                document.getElementById('rejection-reason-input').value = result.value;
                document.getElementById('attendance-form').submit();
            }
        });
    }

    function cancelMeeting() {
        Swal.fire({
            title: '¿Cancelar?',
            text: "Esta acción no se puede deshacer",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#d33',
            confirmButtonText: 'Sí, cancelar'
        }).then((result) => {
            if (result.isConfirmed) {
                const form = document.createElement('form');
                form.method = 'POST';
                form.action = '{{ route("meetings.cancel", $meeting) }}';
                form.innerHTML = '@csrf <input type="hidden" name="cancellation_reason" value="Cancelada por administrador">';
                document.body.appendChild(form);
                form.submit();
            }
        });
    }
</script>
@endsection
@endsection
