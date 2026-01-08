@extends('layouts.admin')

@section('title', 'Reuniones')

@section('contenido')
<div class="container-fluid">
    
    <!-- HEADER & SEARCH -->
    <div class="row align-items-center g-3 mb-4 mt-1">
        <div class="col-12 col-md-auto">
            <h3 class="fw-bold text-dark mb-0" style="letter-spacing: -0.5px;">Reuniones</h3>
            <p class="text-muted small mb-0">Gestión de eventos</p>
        </div>
        <div class="col-12 col-md d-flex flex-column flex-sm-row justify-content-md-end gap-2">
            <form action="{{ route('meetings.index') }}" method="GET" class="position-relative" style="min-width: 200px; flex-grow: 1; max-width: 350px;">
                <input type="text" name="search" value="{{ request('search') }}" class="form-control border-0 bg-white shadow-sm rounded-pill ps-4 pe-5 py-2" placeholder="Buscar reunión...">
                <button type="submit" class="btn btn-link position-absolute top-50 end-0 translate-middle-y text-muted pe-3 text-decoration-none">
                    <i class="fas fa-search"></i>
                </button>
            </form>
            <a href="{{ route('meetings.create') }}" class="btn btn-primary rounded-pill shadow-sm px-4 fw-bold d-flex align-items-center justify-content-center">
                <i class="fas fa-plus me-2"></i> Nuevo
            </a>
        </div>
    </div>

    <!-- Stats Row (Style like Tasks) -->
    <div class="row g-2 g-md-3 mb-4">
        <div class="col-6 col-md-3">
            <div class="card text-white h-100" style="background: linear-gradient(135deg, #4e73df 0%, #224abe 100%);">
                <div class="card-body p-3">
                    <div class="d-flex justify-content-between align-items-start">
                        <div>
                            <div class="small text-white-50 text-uppercase fw-bold" style="font-size: 0.7rem;">Próximas</div>
                            <div class="h4 mb-0 fw-bold">{{ $stats['upcoming'] }}</div>
                        </div>
                        <div class="opacity-50 d-none d-sm-block">
                            <i class="fas fa-clock fa-lg"></i>
                        </div>
                    </div>
                </div>
                <div class="card-footer bg-transparent border-top-0 pt-0 pb-2">
                    <a href="{{ route('meetings.index', ['status' => 'pendiente']) }}" class="text-white small text-decoration-none" style="font-size: 0.7rem;">
                        Ver <i class="fas fa-arrow-right ms-1"></i>
                    </a>
                </div>
            </div>
        </div>
        <div class="col-6 col-md-3">
            <div class="card text-white h-100" style="background: linear-gradient(135deg, #1cc88a 0%, #13855c 100%);">
                <div class="card-body p-3">
                    <div class="d-flex justify-content-between align-items-start">
                        <div>
                            <div class="small text-white-50 text-uppercase fw-bold" style="font-size: 0.7rem;">Completadas</div>
                            <div class="h4 mb-0 fw-bold">{{ $stats['completed'] }}</div>
                        </div>
                        <div class="opacity-50 d-none d-sm-block">
                            <i class="fas fa-check-circle fa-lg"></i>
                        </div>
                    </div>
                </div>
                <div class="card-footer bg-transparent border-top-0 pt-0 pb-2">
                    <a href="{{ route('meetings.index', ['status' => 'completada']) }}" class="text-white small text-decoration-none" style="font-size: 0.7rem;">
                        Ver <i class="fas fa-arrow-right ms-1"></i>
                    </a>
                </div>
            </div>
        </div>
        <div class="col-6 col-md-3">
            <div class="card text-white h-100" style="background: linear-gradient(135deg, #6c757d 0%, #495057 100%);">
                <div class="card-body p-3">
                    <div class="d-flex justify-content-between align-items-start">
                        <div>
                            <div class="small text-white-50 text-uppercase fw-bold" style="font-size: 0.7rem;">Canceladas</div>
                            <div class="h4 mb-0 fw-bold">{{ $stats['cancelled'] }}</div>
                        </div>
                        <div class="opacity-50 d-none d-sm-block">
                            <i class="fas fa-times-circle fa-lg"></i>
                        </div>
                    </div>
                </div>
                <div class="card-footer bg-transparent border-top-0 pt-0 pb-2">
                    <a href="{{ route('meetings.index', ['status' => 'cancelada']) }}" class="text-white small text-decoration-none" style="font-size: 0.7rem;">
                        Ver <i class="fas fa-arrow-right ms-1"></i>
                    </a>
                </div>
            </div>
        </div>
        <div class="col-6 col-md-3">
            <div class="card text-white h-100" style="background: linear-gradient(135deg, #f6c23e 0%, #dda20a 100%);">
                <div class="card-body p-3">
                    <div class="d-flex justify-content-between align-items-start">
                        <div>
                            <div class="small text-white-50 text-uppercase fw-bold" style="font-size: 0.7rem;">Total</div>
                            <div class="h4 mb-0 fw-bold">{{ $stats['total'] }}</div>
                        </div>
                        <div class="opacity-50 d-none d-sm-block">
                            <i class="fas fa-calendar-alt fa-lg"></i>
                        </div>
                    </div>
                </div>
                <div class="card-footer bg-transparent border-top-0 pt-0 pb-2">
                    <a href="{{ route('meetings.index') }}" class="text-white small text-decoration-none" style="font-size: 0.7rem;">
                        Todas <i class="fas fa-arrow-right ms-1"></i>
                    </a>
                </div>
            </div>
        </div>
    </div>

    <!-- TABS FILTER (Pill Style) -->
    <div class="mb-4">
        <div class="d-flex gap-2 flex-wrap">
            <a href="{{ route('meetings.index') }}" class="btn btn-sm {{ !request('status') ? 'btn-primary' : 'btn-light text-muted' }} rounded-pill px-3 fw-bold">
                Todas <span class="badge {{ !request('status') ? 'bg-white text-primary' : 'bg-secondary bg-opacity-25' }} rounded-pill ms-1">{{ $stats['total'] }}</span>
            </a>
            <a href="{{ route('meetings.index', ['status' => 'pendiente']) }}" class="btn btn-sm {{ request('status') == 'pendiente' ? 'btn-primary' : 'btn-light text-muted' }} rounded-pill px-3 fw-bold">
                Pendientes <span class="badge {{ request('status') == 'pendiente' ? 'bg-white text-primary' : 'bg-secondary bg-opacity-25' }} rounded-pill ms-1">{{ $stats['upcoming'] }}</span>
            </a>
            <a href="{{ route('meetings.index', ['status' => 'completada']) }}" class="btn btn-sm {{ request('status') == 'completada' ? 'btn-primary' : 'btn-light text-muted' }} rounded-pill px-3 fw-bold">
                Completadas <span class="badge {{ request('status') == 'completada' ? 'bg-white text-primary' : 'bg-secondary bg-opacity-25' }} rounded-pill ms-1">{{ $stats['completed'] }}</span>
            </a>
            <a href="{{ route('meetings.index', ['status' => 'cancelada']) }}" class="btn btn-sm {{ request('status') == 'cancelada' ? 'btn-primary' : 'btn-light text-muted' }} rounded-pill px-3 fw-bold">
                Canceladas <span class="badge {{ request('status') == 'cancelada' ? 'bg-white text-primary' : 'bg-secondary bg-opacity-25' }} rounded-pill ms-1">{{ $stats['cancelled'] }}</span>
            </a>
        </div>
    </div>

    <!-- CARDS GRID -->
    @if($meetings->isEmpty())
        <div class="text-center py-5">
            <div class="bg-light rounded-circle d-inline-flex align-items-center justify-content-center mb-3" style="width: 80px; height: 80px;">
                <i class="fas fa-inbox text-muted fa-2x opacity-50"></i>
            </div>
            <h5 class="fw-bold text-muted">Sin reuniones encontradas</h5>
        </div>
    @else
        <div class="row g-4">
            @foreach($meetings as $meeting)
            <div class="col-lg-6 col-md-12">
                <!-- Vibrant Event Card -->
                <div class="card border-0 shadow-sm rounded-4 h-100 overflow-hidden hover-lift transition-all position-relative active-glow-{{ $meeting->status_color }}" style="background: #fff;">
                    <!-- Top Gradient Accent -->
                    <div class="position-absolute top-0 start-0 w-100 {{ $meeting->status == 'pendiente' ? 'bg-gradient-primary' : 'bg-'.$meeting->status_color }}" style="height: 6px;"></div>

                    <div class="card-body p-4 d-flex flex-column position-relative z-1">
                        
                        <!-- Header: Date & Menu -->
                        <div class="d-flex justify-content-between align-items-start mb-3">
                            <div class="d-flex align-items-center gap-3">
                                <!-- Gradient Date Box -->
                                <div class="text-center rounded-4 shadow-sm overflow-hidden d-flex flex-column" 
                                     style="min-width: 65px; box-shadow: 0 .5rem 1rem rgba(0,0,0,.05)!important;">
                                    <div class="{{ $meeting->status == 'pendiente' ? 'bg-primary' : 'bg-'.$meeting->status_color }} text-white py-1 small fw-bold text-uppercase" 
                                         style="font-size: 0.7rem; letter-spacing: 1px;">
                                        {{ $meeting->meeting_date->translatedFormat('M') }}
                                    </div>
                                    <div class="bg-white text-dark fw-bold display-6 py-2 border-start border-end border-bottom border-light rounded-bottom-4" 
                                         style="line-height: 1; font-size: 1.8rem;">
                                        {{ $meeting->meeting_date->translatedFormat('d') }}
                                    </div>
                                </div>

                                <!-- Status & Time -->
                                <div>
                                    @if($meeting->status == 'pendiente')
                                        <span class="badge rounded-pill bg-primary bg-opacity-10 text-primary px-3 py-1 mb-1 border border-primary border-opacity-10 shadow-sm">
                                            <i class="fas fa-spinner fa-spin me-1 small"></i> {{ $meeting->status_label }}
                                        </span>
                                    @else
                                        <span class="badge rounded-pill bg-{{ $meeting->status_color }} bg-opacity-10 text-{{ $meeting->status_color }} px-3 py-1 mb-1 border border-{{ $meeting->status_color }} border-opacity-10">
                                            {{ $meeting->status_label }}
                                        </span>
                                    @endif
                                    
                                    <div class="text-muted small fw-bold mt-1 d-flex align-items-center">
                                        <div class="icon-shape icon-xxs {{ $meeting->status == 'pendiente' ? 'bg-primary text-primary' : 'bg-'.$meeting->status_color.' text-'.$meeting->status_color }} bg-opacity-10 rounded-circle me-1 d-flex align-items-center justify-content-center" style="width: 20px; height: 20px;">
                                            <i class="far fa-clock" style="font-size: 0.7rem;"></i>
                                        </div>
                                        {{ $meeting->meeting_date->format('H:i') }} hrs
                                    </div>
                                </div>
                            </div>

                            <!-- Actions Dropdown -->
                            @if($meeting->created_by == auth()->id() || auth()->user()->hasRole('admin'))
                            <div class="dropdown">
                                <button class="btn btn-light btn-sm rounded-circle shadow-sm text-muted hover-bg-light" type="button" data-bs-toggle="dropdown" style="width: 32px; height: 32px;">
                                    <i class="fas fa-ellipsis-v small"></i>
                                </button>
                                <ul class="dropdown-menu dropdown-menu-end border-0 shadow-lg rounded-4 p-2">
                                    <li><a class="dropdown-item rounded-3 small fw-bold py-2 mb-1" href="{{ route('meetings.edit', $meeting) }}">
                                        <i class="fas fa-edit me-2 text-warning"></i> Editar
                                    </a></li>
                                    <li>
                                        <form id="delete-form-{{ $meeting->id }}" action="{{ route('meetings.destroy', $meeting) }}" method="POST" class="d-inline">
                                            @csrf @method('DELETE')
                                            <button type="button" class="dropdown-item rounded-3 small fw-bold text-danger py-2" onclick="confirmDelete('{{ $meeting->id }}')">
                                                <i class="fas fa-trash-alt me-2"></i> Eliminar
                                            </button>
                                        </form>
                                    </li>
                                </ul>
                            </div>
                            @endif
                        </div>

                        <!-- Content: Title & Details -->
                        <div class="mb-4 ps-1 position-relative">
                            <h5 class="fw-bold text-dark mb-3" style="letter-spacing: -0.5px;">
                                <a href="{{ route('meetings.show', $meeting) }}" class="text-decoration-none text-dark stretched-link">
                                    {{ $meeting->title }}
                                </a>
                            </h5>

                            <div class="vstack gap-2">
                                <!-- Project -->
                                @if($meeting->project)
                                <div class="d-flex align-items-center">
                                    <div class="icon-shape icon-xs bg-indigo bg-opacity-10 text-indigo rounded-3 me-2 d-flex align-items-center justify-content-center" style="width: 28px; height: 28px;">
                                        <i class="fas fa-layer-group" style="font-size: 0.8rem; color: #6610f2;"></i>
                                    </div>
                                    <span class="text-muted small fw-bold">Project: <span class="text-dark">{{ $meeting->project->title }}</span></span>
                                </div>
                                @endif

                                <!-- Location -->
                                <div class="d-flex align-items-center">
                                    <div class="icon-shape icon-xs bg-danger bg-opacity-10 text-danger rounded-3 me-2 d-flex align-items-center justify-content-center" style="width: 28px; height: 28px;">
                                        <i class="fas fa-map-marker-alt" style="font-size: 0.8rem;"></i>
                                    </div>
                                    <span class="{{ !$meeting->location ? 'text-muted fst-italic small' : 'text-dark small fw-bold' }}">
                                        {{ $meeting->location ?? 'Ubicación pendiente' }}
                                    </span>
                                </div>
                            </div>
                        </div>

                        <!-- Footer: Participants with nicer avatars -->
                        <div class="mt-auto pt-3 border-top border-dashed d-flex justify-content-between align-items-center">
                            <div class="d-flex align-items-center">
                                <div class="avatar-group d-flex ps-2">
                                    @foreach($meeting->participants->take(4) as $p)
                                        <div class="avatar-sm rounded-circle border border-2 border-white text-white d-flex align-items-center justify-content-center fw-bold shadow-sm transition-transform hover-scale"
                                             style="width: 34px; height: 34px; margin-left: -12px; background: linear-gradient(45deg, {{ ['#4e73df', '#1cc88a', '#36b9cc', '#f6c23e'][rand(0,3)] }}, {{ ['#224abe', '#13855c', '#258391', '#d4a017'][rand(0,3)] }}); font-size: 0.75rem;" 
                                             title="{{ $p->user->name }}">
                                            {{ strtoupper(substr($p->user->name, 0, 1)) }}
                                        </div>
                                    @endforeach
                                    @if($meeting->participants->count() > 4)
                                        <div class="avatar-sm rounded-circle bg-light text-muted border border-2 border-white d-flex align-items-center justify-content-center fw-bold shadow-sm"
                                             style="width: 34px; height: 34px; margin-left: -12px; font-size: 0.75rem;">
                                            +{{ $meeting->participants->count() - 4 }}
                                        </div>
                                    @endif
                                </div>
                                <span class="ms-3 text-muted small fw-bold" style="font-size: 0.75rem;">
                                    {{ $meeting->participants->count() }} Invitados
                                </span>
                            </div>
                            
                            <a href="{{ route('meetings.show', $meeting) }}" class="btn btn-icon btn-sm btn-light rounded-circle text-primary shadow-sm">
                                <i class="fas fa-arrow-right small"></i>
                            </a>
                        </div>
                    </div>
                    
                    <!-- Decorative Large Icon (Watermark) -->
                    <i class="fas fa-calendar-check position-absolute text-{{ $meeting->status_color }} opacity-10" 
                       style="font-size: 8rem; right: -20px; top: 20px; transform: rotate(-15deg); pointer-events: none; opacity: 0.03;"></i>
                </div>
            </div>
            @endforeach

        <div class="mt-5 d-flex justify-content-center">
            {{ $meetings->withQueryString()->links() }}
        </div>
    @endif

</div>

@push('styles')
<style>
    .ls-1 { letter-spacing: 1px; }
    .hover-lift:hover { transform: translateY(-5px); box-shadow: 0 10px 30px rgba(0,0,0,0.08) !important; }
    .transition-all { transition: all 0.3s ease; }
    .text-xs { font-size: 0.75rem; }
    .hover-scale:hover { transform: scale(1.1); z-index: 10 !important; cursor: pointer; }
    .bg-gradient-primary { background: linear-gradient(45deg, #4e73df, #224abe); }
    .border-dashed { border-top-style: dashed !important; }
    .active-glow-primary:hover { border-color: #4e73df !important; }
    .icon-xxs { width: 20px; height: 20px; }
    .bg-indigo { background-color: #6610f2; }
    .text-indigo { color: #6610f2; }
</style>
@endpush

@section('scripts')
<script>
    function confirmDelete(meetingId) {
        Swal.fire({
            title: '¿Eliminar reunión?',
            text: "Esta acción no se puede deshacer",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#d33',
            cancelButtonColor: '#3085d6',
            confirmButtonText: 'Sí, eliminar',
            cancelButtonText: 'Cancelar'
        }).then((result) => {
            if (result.isConfirmed) {
                // Submit the form associated with this meeting
                // We need to find the form based on the button clicked, but since we are outside, 
                // let's assign IDs to forms or use DOM traversal.
                // A better approach for the loop is to give the form a unique ID.
                document.getElementById('delete-form-' + meetingId).submit();
            }
        })
    }
</script>
@endsection
@endsection
