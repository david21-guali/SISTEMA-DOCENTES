@extends('layouts.admin')

@section('title', 'Gestión de Proyectos')

@section('contenido')
<div class="container-fluid">
    <!-- Page Header -->
    <div class="d-flex flex-column flex-sm-row justify-content-between align-items-sm-center gap-3 mb-4">
        <div>
            <h5 class="mb-0 text-dark fw-bold"><i class="fas fa-project-diagram me-2 text-primary"></i>Proyectos</h5>
            <p class="text-muted mb-0 small text-truncate">Gestiona todos los proyectos de innovación</p>
        </div>
        <a href="{{ route('projects.create') }}" class="btn btn-primary shadow-sm">
            <i class="fas fa-plus me-1"></i> Nuevo Proyecto
        </a>
    </div>

    <!-- Stats Row -->
    <div class="row row-cols-2 row-cols-sm-2 row-cols-lg-5 g-3 mb-4">
        <div class="col">
            <div class="card text-white h-100 clickable-card" style="background: linear-gradient(135deg, #4e73df 0%, #224abe 100%); cursor: pointer;">
                <div class="card-body p-2 p-sm-3">
                    <div class="d-flex justify-content-between align-items-start">
                        <div>
                            <div class="small text-white-50 text-uppercase fw-bold" style="font-size: 0.65rem;">Total</div>
                            <div class="h4 mb-0 fw-bold">{{ $stats['total'] }}</div>
                        </div>
                        <div class="opacity-50 d-none d-sm-block">
                            <i class="fas fa-folder fa-lg"></i>
                        </div>
                    </div>
                </div>
                <div class="card-footer bg-transparent border-top-0 pt-0 pb-2">
                    <a href="{{ route('projects.index') }}" class="text-white text-decoration-none" style="font-size: 0.7rem;">
                        Ver <i class="fas fa-arrow-right ms-1"></i>
                    </a>
                </div>
            </div>
        </div>
        <div class="col">
            <div class="card text-white h-100 clickable-card" style="background: linear-gradient(135deg, #1cc88a 0%, #13855c 100%); cursor: pointer;">
                <div class="card-body p-2 p-sm-3">
                    <div class="d-flex justify-content-between align-items-start">
                        <div>
                            <div class="small text-white-50 text-uppercase fw-bold" style="font-size: 0.65rem;">Completados</div>
                            <div class="h4 mb-0 fw-bold">{{ $stats['finalizado'] }}</div>
                        </div>
                        <div class="opacity-50 d-none d-sm-block">
                            <i class="fas fa-check-circle fa-lg"></i>
                        </div>
                    </div>
                </div>
                <div class="card-footer bg-transparent border-top-0 pt-0 pb-2">
                    <a href="{{ route('projects.index', ['status' => 'finalizado']) }}" class="text-white text-decoration-none" style="font-size: 0.7rem;">
                        Ver <i class="fas fa-arrow-right ms-1"></i>
                    </a>
                </div>
            </div>
        </div>
        <div class="col">
            <div class="card text-white h-100 clickable-card" style="background: linear-gradient(135deg, #f6c23e 0%, #dda20a 100%); cursor: pointer;">
                <div class="card-body p-2 p-sm-3">
                    <div class="d-flex justify-content-between align-items-start">
                        <div>
                            <div class="small text-white-50 text-uppercase fw-bold" style="font-size: 0.65rem;">En Progreso</div>
                            <div class="h4 mb-0 fw-bold">{{ $stats['en_progreso'] }}</div>
                        </div>
                        <div class="opacity-50 d-none d-sm-block">
                            <i class="fas fa-spinner fa-lg"></i>
                        </div>
                    </div>
                </div>
                <div class="card-footer bg-transparent border-top-0 pt-0 pb-2">
                    <a href="{{ route('projects.index', ['status' => 'en_progreso']) }}" class="text-white text-decoration-none" style="font-size: 0.7rem;">
                        Ver <i class="fas fa-arrow-right ms-1"></i>
                    </a>
                </div>
            </div>
        </div>
        <div class="col">
            <div class="card text-white h-100 clickable-card" style="background: linear-gradient(135deg, #e74a3b 0%, #be2617 100%); cursor: pointer;">
                <div class="card-body p-2 p-sm-3">
                    <div class="d-flex justify-content-between align-items-start">
                        <div>
                            <div class="small text-white-50 text-uppercase fw-bold" style="font-size: 0.65rem;">En Riesgo</div>
                            <div class="h4 mb-0 fw-bold">{{ $stats['en_riesgo'] }}</div>
                        </div>
                        <div class="opacity-50 d-none d-sm-block">
                            <i class="fas fa-exclamation-triangle fa-lg"></i>
                        </div>
                    </div>
                </div>
                <div class="card-footer bg-transparent border-top-0 pt-0 pb-2">
                    <a href="{{ route('projects.index', ['status' => 'en_riesgo']) }}" class="text-white text-decoration-none" style="font-size: 0.7rem;">
                        Ver <i class="fas fa-arrow-right ms-1"></i>
                    </a>
                </div>
            </div>
        </div>
        <div class="col">
            <div class="card text-white h-100 clickable-card" style="background: linear-gradient(135deg, #36b9cc 0%, #258391 100%); cursor: pointer;">
                <div class="card-body p-2 p-sm-3">
                    <div class="d-flex justify-content-between align-items-start">
                        <div>
                            <div class="small text-white-50 text-uppercase fw-bold" style="font-size: 0.65rem;">Planificación</div>
                            <div class="h4 mb-0 fw-bold">{{ $stats['planificacion'] }}</div>
                        </div>
                        <div class="opacity-50 d-none d-sm-block">
                            <i class="fas fa-clipboard-list fa-lg"></i>
                        </div>
                    </div>
                </div>
                <div class="card-footer bg-transparent border-top-0 pt-0 pb-2">
                    <a href="{{ route('projects.index', ['status' => 'planificacion']) }}" class="text-white extra-small text-decoration-none" style="font-size: 0.7rem;">
                        Ver <i class="fas fa-arrow-right ms-1"></i>
                    </a>
                </div>
            </div>
        </div>
    </div>

    <!-- Projects Content -->
    @if($projects->count() > 0)
        <!-- Desktop Table View -->
        <div class="card d-none d-md-block border-0 shadow-sm mb-4">
            <div class="card-header py-3 bg-white border-bottom">
                <h6 class="m-0 fw-bold text-primary">Lista de Proyectos</h6>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0" id="dataTable" width="100%" cellspacing="0">
                        <thead>
                            <tr>
                                <th>Proyecto</th>
                                <th>Categoría</th>
                                <th>Responsable</th>
                                <th>Estado</th>
                                <th>Progreso</th>
                                <th>Fecha Fin</th>
                                <th class="text-center">Acciones</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($projects as $project)
                            <tr>
                                <td>
                                    <a href="{{ route('projects.show', $project) }}" class="text-decoration-none fw-medium text-dark d-inline-block text-truncate" style="max-width: 250px;" title="{{ $project->title }}">
                                        {{ $project->title }}
                                    </a>
                                </td>
                                <td>
                                    <span class="badge" style="background-color: {{ $project->category->color ?? '#6c757d' }};">
                                        {{ $project->category->name ?? 'Sin categoría' }}
                                    </span>
                                </td>
                                <td class="text-muted small">
                                    @if($project->team->count() > 0)
                                        <div class="d-flex align-items-center">
                                            @foreach($project->team->take(4) as $member)
                                                <div class="rounded-circle bg-secondary d-flex justify-content-center align-items-center text-white small border border-white" 
                                                     title="{{ $member->user->name }}"
                                                     style="width: 24px; height: 24px; font-size: 10px; margin-right: -8px; z-index: {{ 10 - $loop->index }}; cursor: pointer;"
                                                     data-bs-toggle="tooltip">
                                                    {{ strtoupper(substr($member->user->name, 0, 1)) }}
                                                </div>
                                            @endforeach
                                            @if($project->team->count() > 4)
                                                <div class="rounded-circle bg-light d-flex justify-content-center align-items-center text-muted small border border-white" 
                                                     style="width: 24px; height: 24px; font-size: 10px; margin-left: -4px; z-index: 0;">
                                                    +{{ $project->team->count() - 4 }}
                                                </div>
                                            @endif
                                        </div>
                                    @else
                                        <div class="d-flex align-items-center">
                                            <div class="rounded-circle bg-secondary d-flex justify-content-center align-items-center text-white small me-2" style="width: 24px; height: 24px; font-size: 10px;">
                                                {{ strtoupper(substr($project->profile->user->name ?? '?', 0, 1)) }}
                                            </div>
                                            <span class="small">{{ $project->profile->user->name ?? 'N/A' }}</span>
                                        </div>
                                    @endif
                                </td>
                                <td>
                                    <span class="badge bg-{{ $project->status_color ?? 'secondary' }} bg-opacity-10 text-{{ $project->status_color ?? 'secondary' }}">
                                        {{ ucfirst(str_replace('_', ' ', $project->status)) }}
                                    </span>
                                </td>
                                <td style="width: 140px;">
                                    <div class="d-flex align-items-center">
                                        <div class="progress flex-grow-1 me-2" style="height: 6px;">
                                            <div class="progress-bar bg-primary" style="width: {{ $project->completion_percentage ?? 0 }}%"></div>
                                        </div>
                                        <small class="text-muted">{{ $project->completion_percentage ?? 0 }}%</small>
                                    </div>
                                </td>
                                <td class="text-muted small">{{ $project->end_date->translatedFormat('d M, Y') }}</td>
                                <td>
                                    <div class="d-flex justify-content-center gap-1">
                                        <a href="{{ route('projects.show', $project) }}" class="btn btn-sm btn-outline-primary" title="Ver">
                                            <i class="fas fa-eye"></i>
                                        </a>
                                        @if(Auth::user()->profile->id == $project->profile_id || Auth::user()->hasRole('admin'))
                                        <a href="{{ route('projects.edit', $project) }}" class="btn btn-sm btn-outline-warning" title="Editar">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                        <form action="{{ route('projects.destroy', $project) }}" method="POST" class="d-inline form-delete">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-sm btn-outline-danger" title="Eliminar">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        </form>
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

        <!-- Mobile Card View -->
        <div class="d-md-none">
            @foreach($projects as $project)
            <div class="card mb-3 shadow-sm border-0">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-start mb-2 overflow-hidden">
                        <h6 class="card-title fw-bold mb-0 text-truncate pe-2">
                            <a href="{{ route('projects.show', $project) }}" class="text-dark text-decoration-none">
                                {{ $project->title }}
                            </a>
                        </h6>
                        <span class="badge bg-{{ $project->status_color ?? 'secondary' }} bg-opacity-10 text-{{ $project->status_color ?? 'secondary' }} flex-shrink-0">
                            {{ ucfirst(str_replace('_', ' ', $project->status)) }}
                        </span>
                    </div>
                    
                    <div class="mb-2">
                        <span class="badge me-1" style="background-color: {{ $project->category->color ?? '#6c757d' }};">
                            {{ $project->category->name ?? 'Sin categoría' }}
                        </span>
                         <div class="mt-2 text-muted small d-flex align-items-center">
                            <i class="fas fa-users me-2"></i>
                            @if($project->team->count() > 0)
                                <div class="d-flex align-items-center">
                                    @foreach($project->team->take(3) as $member)
                                        <div class="rounded-circle bg-secondary d-flex justify-content-center align-items-center text-white small border border-white" 
                                             title="{{ $member->user->name }}"
                                             style="width: 20px; height: 20px; font-size: 9px; margin-right: -5px; z-index: {{ 10 - $loop->index }};">
                                            {{ strtoupper(substr($member->user->name, 0, 1)) }}
                                        </div>
                                    @endforeach
                                    @if($project->team->count() > 3)
                                        <span class="ms-2 small">+{{ $project->team->count() - 3 }}</span>
                                    @endif
                                </div>
                            @else
                                {{ $project->profile->user->name ?? 'N/A' }}
                            @endif
                        </div>
                    </div>
                    
                    <div class="d-flex align-items-center mb-3">
                        <i class="fas fa-chart-line text-muted me-2 small"></i>
                        <div class="progress flex-grow-1 me-2" style="height: 6px;">
                            <div class="progress-bar bg-primary" style="width: {{ $project->completion_percentage ?? 0 }}%"></div>
                        </div>
                        <small class="fw-bold text-primary">{{ $project->completion_percentage ?? 0 }}%</small>
                    </div>

                    <div class="d-flex justify-content-between align-items-center border-top pt-2 mt-2">
                        <small class="text-muted"><i class="fas fa-calendar me-1"></i> Fin: {{ $project->end_date->translatedFormat('d/m/Y') }}</small>
                        <div class="d-flex gap-2">
                            <a href="{{ route('projects.show', $project) }}" class="btn btn-xs btn-primary px-3"> Ver </a>
                            @if(Auth::user()->profile->id == $project->profile_id || Auth::user()->hasRole('admin'))
                            <a href="{{ route('projects.edit', $project) }}" class="btn btn-xs btn-outline-warning"> Editar </a>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
            @endforeach
        </div>
    @else
        <div class="card border-0 shadow-sm">
            <div class="card-body text-center py-5">
                <i class="fas fa-folder-open fa-3x text-muted mb-3 d-block"></i>
                <p class="text-muted mb-3">No hay proyectos registrados</p>
                <a href="{{ route('projects.create') }}" class="btn btn-primary shadow-sm px-4">
                    <i class="fas fa-plus me-1"></i> Crear Primer Proyecto
                </a>
            </div>
        </div>
    @endif
</div>

@push('styles')
<style>
    .text-xs { font-size: 0.7rem; }
    .border-left-primary { border-left: 4px solid #4e73df !important; }
    .border-left-success { border-left: 4px solid #1cc88a !important; }
    .border-left-warning { border-left: 4px solid #f6c23e !important; }
    .border-left-info { border-left: 4px solid #36b9cc !important; }
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
                dom: "<'row mb-3 align-items-center'<'col-sm-12 col-md-4'l><'col-sm-12 col-md-8 d-flex justify-content-md-end gap-2'Bf>>" +
                    "<'row'<'col-12'tr>>" +
                    "<'row mt-3 align-items-center'<'col-sm-12 col-md-5'i><'col-sm-12 col-md-7 d-flex justify-content-md-end'p>>",
                buttons: [
                    {
                        extend: 'excel',
                        className: 'btn btn-success btn-sm',
                        text: '<i class="fas fa-file-excel me-1"></i> Excel'
                    },
                    {
                        extend: 'pdf',
                        className: 'btn btn-danger btn-sm',
                        text: '<i class="fas fa-file-pdf me-1"></i> PDF'
                    },
                    {
                        extend: 'print',
                        className: 'btn btn-secondary btn-sm',
                        text: '<i class="fas fa-print me-1"></i> Imprimir'
                    }
                ],
                responsive: true,
                order: [[ 5, "desc" ]]
            });
        }
    });

    // Make stat cards clickable
    document.querySelectorAll('.clickable-card').forEach(card => {
        card.addEventListener('click', function(e) {
            // Find the link inside the card
            const link = this.querySelector('.card-footer a');
            if (link && !e.target.closest('a')) {
                window.location.href = link.href;
            }
        });
    });
</script>
@endsection
