@extends('layouts.admin')

@section('title', 'Gestión de Tareas')

@section('contenido')
<div class="container-fluid">
    <!-- Page Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h5 class="mb-0 text-dark"><i class="fas fa-tasks me-2 text-primary"></i>Tareas</h5>
            <p class="text-muted mb-0 small">Gestiona y asigna las tareas de los proyectos</p>
        </div>
        @if(Auth::user()->hasRole('docente') || Auth::user()->hasRole('admin'))
        <a href="{{ route('tasks.create') }}" class="btn btn-primary">
            <i class="fas fa-plus me-1"></i> Nueva Tarea
        </a>
        @endif
    </div>

    <!-- Stats Row -->
    <div class="row row-cols-2 row-cols-sm-3 row-cols-lg-5 g-3 mb-4">
        <div class="col">
            <div class="card text-white h-100 clickable-card" style="background: linear-gradient(135deg, #4e73df 0%, #224abe 100%); cursor: pointer;">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-start">
                        <div>
                            <div class="small text-white-50 text-uppercase fw-bold">Total Tareas</div>
                            <div class="h3 mb-0 fw-bold">{{ $stats['total'] }}</div>
                        </div>
                        <div class="opacity-50">
                            <i class="fas fa-clipboard-list fa-2x"></i>
                        </div>
                    </div>
                </div>
                <div class="card-footer bg-transparent border-top-0 pt-0">
                    <a href="{{ route('tasks.index') }}" class="text-white small text-decoration-none">
                        Todas <i class="fas fa-arrow-right ms-1"></i>
                    </a>
                </div>
            </div>
        </div>
        <div class="col">
            <div class="card text-white h-100 clickable-card" style="background: linear-gradient(135deg, #f6c23e 0%, #dda20a 100%); cursor: pointer;">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-start">
                        <div>
                            <div class="small text-white-50 text-uppercase fw-bold">Pendientes</div>
                            <div class="h3 mb-0 fw-bold">{{ $stats['pendiente'] }}</div>
                        </div>
                        <div class="opacity-50">
                            <i class="fas fa-clock fa-2x"></i>
                        </div>
                    </div>
                </div>
                <div class="card-footer bg-transparent border-top-0 pt-0">
                    <a href="{{ route('tasks.index', ['status' => 'pendiente']) }}" class="text-white small text-decoration-none">
                        Ver Pendientes <i class="fas fa-arrow-right ms-1"></i>
                    </a>
                </div>
            </div>
        </div>
        <div class="col">
            <div class="card text-white h-100 clickable-card" style="background: linear-gradient(135deg, #36b9cc 0%, #258391 100%); cursor: pointer;">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-start">
                        <div>
                            <div class="small text-white-50 text-uppercase fw-bold">En Progreso</div>
                            <div class="h3 mb-0 fw-bold">{{ $stats['en_progreso'] }}</div>
                        </div>
                        <div class="opacity-50">
                            <i class="fas fa-spinner fa-2x"></i>
                        </div>
                    </div>
                </div>
                <div class="card-footer bg-transparent border-top-0 pt-0">
                    <a href="{{ route('tasks.index', ['status' => 'en_progreso']) }}" class="text-white small text-decoration-none">
                        Ver En Progreso <i class="fas fa-arrow-right ms-1"></i>
                    </a>
                </div>
            </div>
        </div>
        <div class="col">
            <div class="card text-white h-100 clickable-card" style="background: linear-gradient(135deg, #1cc88a 0%, #13855c 100%); cursor: pointer;">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-start">
                        <div>
                            <div class="small text-white-50 text-uppercase fw-bold">Completadas</div>
                            <div class="h3 mb-0 fw-bold">{{ $stats['completada'] }}</div>
                        </div>
                        <div class="opacity-50">
                            <i class="fas fa-check-circle fa-2x"></i>
                        </div>
                    </div>
                </div>
                <div class="card-footer bg-transparent border-top-0 pt-0">
                    <a href="{{ route('tasks.index', ['status' => 'completada']) }}" class="text-white small text-decoration-none">
                        Ver Completadas <i class="fas fa-arrow-right ms-1"></i>
                    </a>
                </div>
            </div>
        </div>
        <div class="col">
            <div class="card text-white h-100 clickable-card" style="background: linear-gradient(135deg, #e74a3b 0%, #be2617 100%); cursor: pointer;">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-start">
                        <div>
                            <div class="small text-white-50 text-uppercase fw-bold">Atrasadas</div>
                            <div class="h3 mb-0 fw-bold">{{ $stats['atrasada'] }}</div>
                        </div>
                        <div class="opacity-50">
                            <i class="fas fa-exclamation-circle fa-2x"></i>
                        </div>
                    </div>
                </div>
                <div class="card-footer bg-transparent border-top-0 pt-0">
                    <a href="{{ route('tasks.index', ['status' => 'atrasada']) }}" class="text-white small text-decoration-none">
                        Ver Atrasadas <i class="fas fa-arrow-right ms-1"></i>
                    </a>
                </div>
            </div>
        </div>
    </div>

    <!-- Filters -->
    <div class="card mb-4 border-0 shadow-sm">
        <div class="card-body p-2">
            <div class="nav nav-pills nav-fill">
                <a href="{{ route('tasks.index') }}" class="nav-link {{ !request('status') ? 'active bg-primary' : 'text-muted' }}">
                    Todas
                </a>
                <a href="{{ route('tasks.index', ['status' => 'pendiente']) }}" class="nav-link {{ request('status') == 'pendiente' ? 'active bg-warning text-dark' : 'text-muted' }}">
                    Pendientes
                </a>
                <a href="{{ route('tasks.index', ['status' => 'en_progreso']) }}" class="nav-link {{ request('status') == 'en_progreso' ? 'active bg-info' : 'text-muted' }}">
                    En Progreso
                </a>
                <a href="{{ route('tasks.index', ['status' => 'completada']) }}" class="nav-link {{ request('status') == 'completada' ? 'active bg-success' : 'text-muted' }}">
                    Completadas
                </a>
                <a href="{{ route('tasks.index', ['status' => 'atrasada']) }}" class="nav-link {{ request('status') == 'atrasada' ? 'active bg-danger' : 'text-muted' }}">
                    Atrasadas
                </a>
            </div>
        </div>
    </div>

    <!-- Tasks Table -->
    <div class="card">
        <div class="card-header py-3">
            <h6 class="m-0 fw-bold text-primary">Lista de Tareas</h6>
        </div>
        <div class="card-body p-0 p-md-3">
            @if($tasks->count() > 0)
            <!-- Desktop Table View -->
            <div class="d-none d-md-block">
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0" id="dataTable" width="100%" cellspacing="0">
                    <thead>
                        <tr>
                            <th>Tarea</th>
                            <th>Proyecto</th>
                            <th>Asignado a</th>
                            <th>Prioridad</th>
                            <th>Estado</th>
                            <th>Fecha Límite</th>
                            <th class="text-center">Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($tasks as $task)
                        <tr>
                            <td>
                                <a href="{{ route('tasks.show', $task) }}" class="text-decoration-none fw-medium text-dark">
                                    {{ $task->title }}
                                </a>
                                @if($task->description)
                                    <div class="small text-muted text-truncate" style="max-width: 200px;">
                                        {{ $task->description }}
                                    </div>
                                @endif
                            </td>
                            <td>
                                @if($task->project)
                                    <a href="{{ route('projects.show', $task->project) }}" class="text-decoration-none small text-muted">
                                        <i class="fas fa-folder me-1"></i>{{ $task->project->title }}
                                    </a>
                                @else
                                    <span class="text-muted small">Sin proyecto</span>
                                @endif
                            </td>
                            <td>
                                <span class="d-none export-names">{{ $task->assignees->pluck('user.name')->join(', ') ?: ($task->assignedProfile->user->name ?? 'Sin asignar') }}</span>
                                @if($task->assignees->count() > 0)
                                    <div class="d-flex align-items-center">
                                        @foreach($task->assignees->take(4) as $assignee)
                                            <div class="rounded-circle bg-secondary d-flex justify-content-center align-items-center text-white small border border-white" 
                                                 title="{{ $assignee->user->name }}"
                                                 style="width: 24px; height: 24px; font-size: 10px; margin-right: -8px; z-index: {{ 10 - $loop->index }}; cursor: pointer;"
                                                 data-bs-toggle="tooltip">
                                                {{ strtoupper(substr($assignee->user->name, 0, 1)) }}
                                            </div>
                                        @endforeach
                                        @if($task->assignees->count() > 4)
                                            <div class="rounded-circle bg-light d-flex justify-content-center align-items-center text-muted small border border-white" 
                                                 style="width: 24px; height: 24px; font-size: 10px; margin-left: -4px; z-index: 0;">
                                                +{{ $task->assignees->count() - 4 }}
                                            </div>
                                        @endif
                                    </div>
                                @elseif($task->assignedProfile)
                                    <!-- Legacy/Single fallback -->
                                    <div class="d-flex align-items-center">
                                        <div class="rounded-circle bg-secondary d-flex justify-content-center align-items-center text-white small me-2" style="width: 24px; height: 24px; font-size: 10px;">
                                            {{ strtoupper(substr($task->assignedProfile->user->name, 0, 1)) }}
                                        </div>
                                        <span class="small">{{ $task->assignedProfile->user->name }}</span>
                                    </div>
                                @else
                                    <span class="badge bg-light text-muted border">Sin asignar</span>
                                @endif
                            </td>
                            <td>
                                <span class="badge bg-{{ $task->priority_color }} bg-opacity-10 text-{{ $task->priority_color }}">
                                    {{ ucfirst($task->priority) }}
                                </span>
                            </td>
                            <td>
                                <span class="badge bg-{{ $task->status == 'completada' ? 'success' : ($task->is_overdue ? 'danger' : 'warning') }}">
                                    {{ ucfirst($task->status) }}
                                </span>
                            </td>
                            <td>
                                <div class="{{ $task->is_overdue ? 'text-danger fw-bold' : 'text-muted' }} small">
                                    {{ $task->due_date->translatedFormat('d M, Y') }}
                                </div>
                            </td>
                            <td class="text-center">
                                <div class="dropdown position-static">
                                    <button class="btn btn-sm btn-link text-muted" type="button" data-bs-toggle="dropdown" aria-expanded="false">
                                        <i class="fas fa-ellipsis-v"></i>
                                    </button>
                                    <ul class="dropdown-menu dropdown-menu-end shadow border-0">
                                        <li>
                                            <a class="dropdown-item" href="{{ route('tasks.show', $task) }}">
                                                <i class="fas fa-eye me-2 text-info"></i> Ver Detalles
                                            </a>
                                        </li>
                                        @if(Auth::user()->profile->id == $task->project->profile_id || Auth::user()->hasRole('admin'))
                                            <li>
                                                <a class="dropdown-item" href="{{ route('tasks.edit', $task) }}">
                                                    <i class="fas fa-edit me-2 text-warning"></i> Editar
                                                </a>
                                            </li>
                                        @endif
                                        @if($task->status !== 'completada')
                                            <li>
                                                <form action="{{ route('tasks.complete', $task) }}" method="POST">
                                                    @csrf
                                                    @method('PATCH')
                                                    <button type="submit" class="dropdown-item">
                                                        <i class="fas fa-check me-2 text-success"></i> Marcar Completada
                                                    </button>
                                                </form>
                                            </li>
                                        @endif
                                        @if(Auth::user()->profile->id == $task->project->profile_id || Auth::user()->hasRole('admin'))
                                            <li><hr class="dropdown-divider"></li>
                                            <li>
                                                <form action="{{ route('tasks.destroy', $task) }}" method="POST" class="form-delete">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="dropdown-item text-danger">
                                                        <i class="fas fa-trash me-2"></i> Eliminar
                                                    </button>
                                                </form>
                                            </li>
                                        @endif
                                    </ul>
                                </div>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            </div>

            <!-- Mobile Card View -->
            <div class="d-md-none p-3">
                @foreach($tasks as $task)
                <div class="card mb-3 shadow-sm border-0 border-start border-4 border-{{ $task->status == 'completada' ? 'success' : ($task->is_overdue ? 'danger' : 'warning') }}">
                    <div class="card-body p-3">
                        <div class="d-flex justify-content-between align-items-start mb-2">
                            <h6 class="fw-bold mb-0">
                                <a href="{{ route('tasks.show', $task) }}" class="text-dark text-decoration-none">
                                    {{ $task->title }}
                                </a>
                            </h6>
                            <span class="badge bg-{{ $task->status == 'completada' ? 'success' : ($task->is_overdue ? 'danger' : 'warning') }} bg-opacity-10 text-{{ $task->status == 'completada' ? 'success' : ($task->is_overdue ? 'danger' : 'warning') }}">
                                {{ ucfirst($task->status) }}
                            </span>
                        </div>

                        @if($task->project)
                        <div class="small text-muted mb-2">
                            <i class="fas fa-folder me-1"></i>{{ Str::limit($task->project->title, 40) }}
                        </div>
                        @endif

                        <div class="d-flex justify-content-between align-items-center mt-3">
                            <div class="d-flex align-items-center">
                                @if($task->assignees->count() > 0)
                                    <div class="avatar-group d-flex ps-1">
                                        @foreach($task->assignees->take(3) as $assignee)
                                            <div class="rounded-circle bg-secondary d-flex justify-content-center align-items-center text-white small border border-white" 
                                                 title="{{ $assignee->user->name }}"
                                                 style="width: 24px; height: 24px; font-size: 10px; margin-left: -8px; z-index: {{ 10 - $loop->index }};">
                                                {{ strtoupper(substr($assignee->user->name, 0, 1)) }}
                                            </div>
                                        @endforeach
                                    </div>
                                @endif
                                <span class="badge bg-{{ $task->priority_color }} text-white ms-2" style="font-size: 0.65rem;">
                                    {{ ucfirst($task->priority) }}
                                </span>
                            </div>
                            <div class="small {{ $task->is_overdue ? 'text-danger fw-bold' : 'text-muted' }}">
                                <i class="far fa-calendar-alt me-1"></i>{{ $task->due_date->translatedFormat('d/m/Y') }}
                            </div>
                        </div>

                        <div class="d-flex gap-2 mt-3 border-top pt-2">
                            <a href="{{ route('tasks.show', $task) }}" class="btn btn-sm btn-outline-primary flex-grow-1">Ver</a>
                            @if($task->status !== 'completada')
                            <form action="{{ route('tasks.complete', $task) }}" method="POST" class="flex-grow-1">
                                @csrf @method('PATCH')
                                <button type="submit" class="btn btn-sm btn-success w-100">Listo</button>
                            </form>
                            @endif
                        </div>
                    </div>
                </div>
                @endforeach
                <div class="mt-3">
                    {{ $tasks->links() }}
                </div>
            </div>
            

            @else
            <div class="text-center py-5">
                <i class="fas fa-tasks fa-3x text-muted mb-3 d-block"></i>
                <p class="text-muted mb-3">No hay tareas registradas</p>
                @can('create-tasks')
                <a href="{{ route('tasks.create') }}" class="btn btn-primary">
                    <i class="fas fa-plus me-1"></i> Crear Primera Tarea
                </a>
                @endcan
            </div>
            @endif
        </div>
    </div>
</div>

@push('styles')
<style>
    .text-xs { font-size: 0.7rem; }
    .border-left-primary { border-left: 4px solid #4e73df !important; }
    .border-left-success { border-left: 4px solid #1cc88a !important; }
    .border-left-warning { border-left: 4px solid #f6c23e !important; }
    .border-left-danger { border-left: 4px solid #e74a3b !important; }
</style>
@endpush
@endsection

@section('scripts')
<script>
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
                            columns: [0, 1, 2, 3, 4, 5],
                            format: {
                                 body: function (data, row, column, node) {
                                    // Always check if there's a specific export span first
                                    const exportOnly = $(node).find('.export-names');
                                    if (exportOnly.length) {
                                        return exportOnly.text().trim();
                                    }

                                    // Column 2 is "Asignado a" - Extract titles from avatars (including tooltips)
                                    if (column === 2) {
                                        // Try to find full names in avatar titles or Bootstrap tooltips
                                        let names = $(node).find('[data-bs-original-title], [title]').map(function() { 
                                            return $(this).attr('data-bs-original-title') || $(this).attr('title'); 
                                        }).get().filter(n => n && n.trim() !== '').join(', ');

                                        // Fallback for single user or legacy view (if no avatars with titles found)
                                        if (!names) {
                                            const spanText = $(node).find('span.small').text().trim();
                                            names = spanText || $(node).text().replace(/\s+/g, ' ').trim();
                                        }

                                        return names;
                                    }
                                    // Default: Strip HTML and collapse whitespace
                                    return $(node).text().replace(/\s+/g, ' ').trim();
                                }
                            }
                        }
                    },
                    {
                        extend: 'pdf',
                        className: 'btn btn-danger btn-sm',
                        text: '<i class="fas fa-file-pdf me-1"></i> PDF',
                        exportOptions: {
                            columns: [0, 1, 2, 3, 4, 5],
                            format: {
                                 body: function (data, row, column, node) {
                                    // Always check if there's a specific export span first
                                    const exportOnly = $(node).find('.export-names');
                                    if (exportOnly.length) {
                                        return exportOnly.text().trim();
                                    }

                                    // Column 2 is "Asignado a" - Extract titles from avatars (including tooltips)
                                    if (column === 2) {
                                        // Try to find full names in avatar titles or Bootstrap tooltips
                                        let names = $(node).find('[data-bs-original-title], [title]').map(function() { 
                                            return $(this).attr('data-bs-original-title') || $(this).attr('title'); 
                                        }).get().filter(n => n && n.trim() !== '').join(', ');

                                        // Fallback for single user or legacy view (if no avatars with titles found)
                                        if (!names) {
                                            const spanText = $(node).find('span.small').text().trim();
                                            names = spanText || $(node).text().replace(/\s+/g, ' ').trim();
                                        }

                                        return names;
                                    }
                                    // Default: Strip HTML and collapse whitespace
                                    return $(node).text().replace(/\s+/g, ' ').trim();
                                }
                            }
                        }
                    },
                    {
                        extend: 'print',
                        className: 'btn btn-secondary btn-sm',
                        text: '<i class="fas fa-print me-1"></i> Imprimir'
                    }
                ],
                responsive: true,
                order: [[ 5, "asc" ]],
                columnDefs: [
                    { orderable: false, targets: 6 } // Disable sorting on 'Actions' column
                ]
            });
        }

        // Make stat cards clickable
        document.querySelectorAll('.clickable-card').forEach(card => {
            card.addEventListener('click', function(e) {
                const link = this.querySelector('.card-footer a');
                if (link && !e.target.closest('a')) {
                    window.location.href = link.href;
                }
            });
        });
    });
</script>
@endsection
