@extends('layouts.admin')

@section('title', 'Innovaciones Pedagógicas')

@section('contenido')
<div class="container-fluid">
    <!-- Page Header -->
    <div class="d-flex flex-column flex-sm-row justify-content-between align-items-sm-center gap-3 mb-4">
        <div>
            <h5 class="mb-0 text-dark"><i class="fas fa-lightbulb me-2 text-primary"></i>Innovaciones Pedagógicas</h5>
            <p class="text-muted mb-0 small">Explora y gestiona las iniciativas de innovación educativa</p>
        </div>
        @if(Auth::user()->hasRole('docente') || Auth::user()->hasRole('admin'))
        <a href="{{ route('innovations.create') }}" class="btn btn-primary">
            <i class="fas fa-plus me-1"></i> Nueva Innovación
        </a>
        @endif
    </div>

    <!-- Stats Row -->
    <div class="row row-cols-2 row-cols-sm-2 row-cols-lg-6 g-3 mb-4">
        <div class="col">
            <div class="card text-white h-100 clickable-card" style="background: linear-gradient(135deg, #4e73df 0%, #224abe 100%); cursor: pointer;">
                <div class="card-body p-2 p-sm-3">
                    <div class="d-flex justify-content-between align-items-start">
                        <div>
                            <div class="small text-white-50 text-uppercase fw-bold" style="font-size: 0.65rem;">Total</div>
                            <div class="h4 mb-0 fw-bold">{{ $stats['total'] }}</div>
                        </div>
                        <div class="opacity-50 d-none d-sm-block">
                            <i class="fas fa-lightbulb fa-lg"></i>
                        </div>
                    </div>
                </div>
                <div class="card-footer bg-transparent border-top-0 pt-0 pb-2">
                    <a href="{{ route('innovations.index') }}" class="text-white text-decoration-none" style="font-size: 0.7rem;">
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
                            <div class="small text-white-50 text-uppercase fw-bold" style="font-size: 0.65rem;">Aprobadas</div>
                            <div class="h4 mb-0 fw-bold">{{ $stats['aprobada'] }}</div>
                        </div>
                        <div class="opacity-50 d-none d-sm-block">
                            <i class="fas fa-check-circle fa-lg"></i>
                        </div>
                    </div>
                </div>
                <div class="card-footer bg-transparent border-top-0 pt-0 pb-2">
                    <a href="{{ route('innovations.index', ['status' => 'aprobada']) }}" class="text-white text-decoration-none" style="font-size: 0.7rem;">
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
                            <div class="small text-white-50 text-uppercase fw-bold" style="font-size: 0.65rem;">En Revisión</div>
                            <div class="h4 mb-0 fw-bold">{{ $stats['en_revision'] }}</div>
                        </div>
                        <div class="opacity-50 d-none d-sm-block">
                            <i class="fas fa-hourglass-half fa-lg"></i>
                        </div>
                    </div>
                </div>
                <div class="card-footer bg-transparent border-top-0 pt-0 pb-2">
                    <a href="{{ route('innovations.index', ['status' => 'en_revision']) }}" class="text-white text-decoration-none" style="font-size: 0.7rem;">
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
                            <div class="small text-white-50 text-uppercase fw-bold" style="font-size: 0.65rem;">Rechazadas</div>
                            <div class="h4 mb-0 fw-bold">{{ $stats['rechazada'] }}</div>
                        </div>
                        <div class="opacity-50 d-none d-sm-block">
                            <i class="fas fa-times-circle fa-lg"></i>
                        </div>
                    </div>
                </div>
                <div class="card-footer bg-transparent border-top-0 pt-0 pb-2">
                    <a href="{{ route('innovations.index', ['status' => 'rechazada']) }}" class="text-white text-decoration-none" style="font-size: 0.7rem;">
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
                            <div class="small text-white-50 text-uppercase fw-bold" style="font-size: 0.65rem;">Propuestas</div>
                            <div class="h4 mb-0 fw-bold">{{ $stats['propuesta'] }}</div>
                        </div>
                        <div class="opacity-50 d-none d-sm-block">
                            <i class="fas fa-file-alt fa-lg"></i>
                        </div>
                    </div>
                </div>
                <div class="card-footer bg-transparent border-top-0 pt-0 pb-2">
                    <a href="{{ route('innovations.index', ['status' => 'propuesta']) }}" class="text-white text-decoration-none" style="font-size: 0.7rem;">
                        Ver <i class="fas fa-arrow-right ms-1"></i>
                    </a>
                </div>
            </div>
        </div>
        <div class="col">
            <div class="card text-white h-100 clickable-card" style="background: linear-gradient(135deg, #6610f2 0%, #4b0db8 100%); cursor: pointer;">
                <div class="card-body p-2 p-sm-3">
                    <div class="d-flex justify-content-between align-items-start">
                        <div>
                            <div class="small text-white-50 text-uppercase fw-bold" style="font-size: 0.65rem;">En Implementación</div>
                            <div class="h4 mb-0 fw-bold">{{ $stats['en_implementacion'] }}</div>
                        </div>
                        <div class="opacity-50 d-none d-sm-block">
                            <i class="fas fa-tools fa-lg"></i>
                        </div>
                    </div>
                </div>
                <div class="card-footer bg-transparent border-top-0 pt-0 pb-2">
                    <a href="{{ route('innovations.index', ['status' => 'en_implementacion']) }}" class="text-white text-decoration-none" style="font-size: 0.7rem;">
                        Ver <i class="fas fa-arrow-right ms-1"></i>
                    </a>
                </div>
            </div>
        </div>
    </div>

    <!-- Innovations Content -->
    @if($innovations->count() > 0)
        <!-- Desktop Table View -->
        <div class="card d-none d-md-block border-0 shadow-sm mb-4">
            <div class="card-header py-3 bg-white border-bottom">
                <h6 class="m-0 fw-bold text-primary">Lista de Innovaciones</h6>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0" id="dataTable" width="100%" cellspacing="0">
                        <thead>
                            <tr>
                                <th>Innovación</th>
                                <th>Tipo</th>
                                <th>Autor</th>
                                <th>Estado</th>
                                <th>Impacto</th>
                                <th>Fecha</th>
                                <th class="text-center">Acciones</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($innovations as $innovation)
                            <tr>
                                <td>
                                    <a href="{{ route('innovations.show', $innovation) }}" class="text-decoration-none fw-medium text-dark d-inline-block text-truncate" style="max-width: 250px;" title="{{ $innovation->title }}">
                                        {{ $innovation->title }}
                                    </a>
                                </td>
                                <td>
                                    <span class="badge bg-light text-dark border">
                                        {{ $innovation->innovationType->name }}
                                    </span>
                                </td>
                                <td class="text-muted small">
                                    <span class="d-none export-names">{{ $innovation->profile->user->name ?? 'N/A' }}</span>
                                    <div class="d-flex align-items-center">
                                        <div class="rounded-circle bg-secondary d-flex justify-content-center align-items-center text-white small me-2" style="width: 24px; height: 24px; font-size: 10px;">
                                            {{ strtoupper(substr($innovation->profile->user->name ?? '?', 0, 1)) }}
                                        </div>
                                        <span class="small">{{ $innovation->profile->user->name ?? 'N/A' }}</span>
                                    </div>
                                </td>
                                <td>
                                    <span class="badge bg-{{ $innovation->status_color }} bg-opacity-10 text-{{ $innovation->status_color }}">
                                        {{ ucfirst(str_replace('_', ' ', $innovation->status)) }}
                                    </span>
                                </td>
                                <td>
                                    @if($innovation->impact_score)
                                        <div class="d-flex align-items-center" style="min-width: 100px;">
                                            <div class="progress flex-grow-1 me-2" style="height: 6px;">
                                                <div class="progress-bar bg-{{ $innovation->impact_score >= 8 ? 'success' : ($innovation->impact_score >= 5 ? 'info' : 'warning') }}" 
                                                     role="progressbar" 
                                                     style="width: {{ $innovation->impact_score * 10 }}%">
                                                </div>
                                            </div>
                                            <small class="text-muted">{{ $innovation->impact_score }}/10</small>
                                        </div>
                                    @else
                                        <span class="text-muted small">N/A</span>
                                    @endif
                                </td>
                                <td class="text-muted small">{{ $innovation->created_at->translatedFormat('d M, Y') }}</td>
                                <td>
                                    <div class="d-flex justify-content-center gap-1">
                                        <a href="{{ route('innovations.show', $innovation) }}" class="btn btn-sm btn-outline-primary" title="Ver">
                                            <i class="fas fa-eye"></i>
                                        </a>
                                        @can('update', $innovation)
                                        <a href="{{ route('innovations.edit', $innovation) }}" class="btn btn-sm btn-outline-warning" title="Editar">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                        @endcan
                                        @can('delete', $innovation)
                                        <form action="{{ route('innovations.destroy', $innovation) }}" method="POST" class="d-inline form-delete">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-sm btn-outline-danger" title="Eliminar">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        </form>
                                        @endcan
                                    </div>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <!-- Mobile Card View (Keep Grid) -->
        <div class="d-md-none">
            <div class="row g-4">
                @foreach($innovations as $innovation)
                    <div class="col-12">
                        <div class="card shadow-sm border-0 border-top border-4" style="border-top-color: {{ $innovation->status_color == 'success' ? '#1cc88a' : ($innovation->status_color == 'warning' ? '#f6c23e' : '#e74a3b') }} !important;">
                            <div class="card-body">
                                <div class="d-flex justify-content-between align-items-start mb-3">
                                    <span class="badge bg-light text-dark border">
                                        {{ $innovation->innovationType->name }}
                                    </span>
                                    <div class="dropdown">
                                        <button class="btn btn-link text-muted p-0" data-bs-toggle="dropdown">
                                            <i class="fas fa-ellipsis-v"></i>
                                        </button>
                                        <ul class="dropdown-menu dropdown-menu-end shadow">
                                            <li><a class="dropdown-item" href="{{ route('innovations.show', $innovation) }}">Ver detalles</a></li>
                                            @can('update', $innovation)
                                            <li><a class="dropdown-item" href="{{ route('innovations.edit', $innovation) }}">Editar</a></li>
                                            @endcan
                                            @can('delete', $innovation)
                                            <li><hr class="dropdown-divider"></li>
                                            <li>
                                                <form action="{{ route('innovations.destroy', $innovation) }}" method="POST" class="form-delete">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="dropdown-item text-danger">Eliminar</button>
                                                </form>
                                            </li>
                                            @endcan
                                        </ul>
                                    </div>
                                </div>

                                <h6 class="card-title fw-bold mb-2">
                                    <a href="{{ route('innovations.show', $innovation) }}" class="text-decoration-none text-dark">
                                        {{ $innovation->title }}
                                    </a>
                                </h6>
                                
                                <div class="mb-3">
                                    <div class="d-flex align-items-center mb-1">
                                        <span class="small text-muted"><i class="fas fa-user me-2"></i>{{ $innovation->profile->user->name }}</span>
                                    </div>
                                    <div class="d-flex align-items-center">
                                        <span class="small text-muted"><i class="fas fa-calendar me-2"></i>{{ $innovation->created_at->translatedFormat('d M, Y') }}</span>
                                    </div>
                                </div>

                                <div class="d-flex justify-content-between align-items-center">
                                    <span class="badge bg-{{ $innovation->status_color }} bg-opacity-10 text-{{ $innovation->status_color }} small">
                                        {{ ucfirst(str_replace('_', ' ', $innovation->status)) }}
                                    </span>
                                    @if($innovation->evidence_files && count($innovation->evidence_files) > 0)
                                        <small class="text-muted">
                                            <i class="fas fa-paperclip me-1"></i>{{ count($innovation->evidence_files) }}
                                        </small>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    @else
        <div class="card border-0 shadow-sm">
            <div class="card-body text-center py-5">
                <i class="fas fa-lightbulb fa-3x text-muted mb-3 d-block"></i>
                <p class="text-muted mb-3">No hay innovaciones registradas</p>
                <a href="{{ route('innovations.create') }}" class="btn btn-primary shadow-sm px-4">
                    <i class="fas fa-plus me-1"></i> Crear Primera Innovación
                </a>
            </div>
        </div>
    @endif
</div>

@push('styles')
<style>
    .text-xs { font-size: 0.7rem; }
</style>
@endpush
@endsection

@section('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        if (typeof jQuery !== 'undefined' && $.fn.DataTable) {
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
                        text: '<i class="fas fa-file-excel me-1"></i> Excel',
                        exportOptions: {
                            columns: [0, 1, 2, 3, 4, 5],
                            format: {
                                body: function (data, row, column, node) {
                                    const exportOnly = $(node).find('.export-names');
                                    if (exportOnly.length) return exportOnly.text().trim();
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
                                    const exportOnly = $(node).find('.export-names');
                                    if (exportOnly.length) return exportOnly.text().trim();
                                    return $(node).text().replace(/\s+/g, ' ').trim();
                                }
                            }
                        }
                    },
                    {
                        extend: 'print',
                        className: 'btn btn-secondary btn-sm',
                        text: '<i class="fas fa-print me-1"></i> Imprimir',
                        exportOptions: {
                            columns: [0, 1, 2, 3, 4, 5],
                            format: {
                                body: function (data, row, column, node) {
                                    const exportOnly = $(node).find('.export-names');
                                    if (exportOnly.length) return exportOnly.text().trim();
                                    return $(node).text().replace(/\s+/g, ' ').trim();
                                }
                            }
                        }
                    }
                ],
                responsive: true,
                order: [[ 5, "desc" ]]
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
