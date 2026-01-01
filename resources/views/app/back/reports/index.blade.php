@extends('layouts.admin')

@section('title', 'Reportes')

@section('contenido')
<div class="container-fluid">
    <!-- Page Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h5 class="mb-0 text-dark"><i class="fas fa-chart-bar me-2 text-primary"></i>Generación de Reportes</h5>
            <p class="text-muted mb-0 small">Exporta y visualiza la información del sistema</p>
        </div>
    </div>

    <!-- Stats Summary -->
    <div class="row g-3 mb-4">
        <div class="col-md-3">
            <div class="card text-white h-100" style="background: linear-gradient(135deg, #4e73df 0%, #224abe 100%);">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-start">
                        <div>
                            <div class="small text-white-50 text-uppercase fw-bold">Total Proyectos</div>
                            <div class="h3 mb-0 fw-bold">{{ $stats['total_projects'] }}</div>
                        </div>
                        <div class="opacity-50">
                            <i class="fas fa-folder fa-2x"></i>
                        </div>
                    </div>
                </div>
                <div class="card-footer bg-transparent border-top-0 pt-0">
                    <a href="{{ route('projects.index') }}" class="text-white small text-decoration-none">
                        Ver Proyectos <i class="fas fa-arrow-right ms-1"></i>
                    </a>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card text-white h-100" style="background: linear-gradient(135deg, #f6c23e 0%, #dda20a 100%);">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-start">
                        <div>
                            <div class="small text-white-50 text-uppercase fw-bold">Total Tareas</div>
                            <div class="h3 mb-0 fw-bold">{{ $stats['total_tasks'] }}</div>
                        </div>
                        <div class="opacity-50">
                            <i class="fas fa-tasks fa-2x"></i>
                        </div>
                    </div>
                </div>
                <div class="card-footer bg-transparent border-top-0 pt-0">
                    <a href="{{ route('tasks.index') }}" class="text-white small text-decoration-none">
                        Ver Tareas <i class="fas fa-arrow-right ms-1"></i>
                    </a>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card text-white h-100" style="background: linear-gradient(135deg, #36b9cc 0%, #258391 100%);">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-start">
                        <div>
                            <div class="small text-white-50 text-uppercase fw-bold">Innovaciones</div>
                            <div class="h3 mb-0 fw-bold">{{ $stats['total_innovations'] }}</div>
                        </div>
                        <div class="opacity-50">
                            <i class="fas fa-lightbulb fa-2x"></i>
                        </div>
                    </div>
                </div>
                <div class="card-footer bg-transparent border-top-0 pt-0">
                    <a href="{{ route('innovations.index') }}" class="text-white small text-decoration-none">
                        Ver Innovaciones <i class="fas fa-arrow-right ms-1"></i>
                    </a>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card text-white h-100" style="background: linear-gradient(135deg, #e74a3b 0%, #be2617 100%);">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-start">
                        <div>
                            <div class="small text-white-50 text-uppercase fw-bold">Tareas Atrasadas</div>
                            <div class="h3 mb-0 fw-bold">{{ $stats['overdue_tasks'] }}</div>
                        </div>
                        <div class="opacity-50">
                            <i class="fas fa-exclamation-triangle fa-2x"></i>
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

    <div class="row g-4 mb-4">
        <!-- Reportes de Proyectos -->
        <div class="col-lg-4">
            <div class="card h-100 shadow-sm border-0 border-top border-4 border-primary">
                <div class="card-body text-center">
                    <div class="mb-3">
                        <div class="rounded-circle bg-primary bg-opacity-10 mx-auto d-flex align-items-center justify-content-center" style="width: 60px; height: 60px;">
                            <i class="fas fa-project-diagram fa-2x text-primary"></i>
                        </div>
                    </div>
                    <h5 class="card-title fw-bold">Reportes de Proyectos</h5>
                    <p class="card-text text-muted small mb-4">Genera listados completos de todos los proyectos, incluyendo estados y responsables.</p>
                    
                    <div class="d-grid gap-2">
                        @can('export-reports')
                        <a href="{{ route('reports.projects.pdf') }}" class="btn btn-outline-danger btn-sm">
                            <i class="fas fa-file-pdf me-2"></i> Descargar PDF
                        </a>
                        <a href="{{ route('reports.projects.excel') }}" class="btn btn-outline-success btn-sm">
                            <i class="fas fa-file-excel me-2"></i> Descargar Excel
                        </a>
                        @else
                        <button class="btn btn-secondary btn-sm" disabled>
                            <i class="fas fa-lock me-2"></i> Sin permisos
                        </button>
                        @endcan
                    </div>
                </div>
            </div>
        </div>

        <!-- Reportes de Tareas -->
        <div class="col-lg-4">
            <div class="card h-100 shadow-sm border-0 border-top border-4 border-warning">
                <div class="card-body text-center">
                    <div class="mb-3">
                        <div class="rounded-circle bg-warning bg-opacity-10 mx-auto d-flex align-items-center justify-content-center" style="width: 60px; height: 60px;">
                            <i class="fas fa-tasks fa-2x text-warning"></i>
                        </div>
                    </div>
                    <h5 class="card-title fw-bold">Reportes de Tareas</h5>
                    <p class="card-text text-muted small mb-4">Obtén detalles sobre el cumplimiento de tareas y plazos asignados.</p>
                    
                    <div class="d-grid gap-2">
                        @can('export-reports')
                        <a href="{{ route('reports.tasks.pdf') }}" class="btn btn-outline-danger btn-sm">
                            <i class="fas fa-file-pdf me-2"></i> Descargar PDF
                        </a>
                        <a href="{{ route('reports.tasks.excel') }}" class="btn btn-outline-success btn-sm">
                            <i class="fas fa-file-excel me-2"></i> Descargar Excel
                        </a>
                        @else
                        <button class="btn btn-secondary btn-sm" disabled>
                            <i class="fas fa-lock me-2"></i> Sin permisos
                        </button>
                        @endcan
                    </div>
                </div>
            </div>
        </div>

        <!-- Reportes de Innovaciones -->
        <div class="col-lg-4">
            <div class="card h-100 shadow-sm border-0 border-top border-4 border-info">
                <div class="card-body text-center">
                    <div class="mb-3">
                        <div class="rounded-circle bg-info bg-opacity-10 mx-auto d-flex align-items-center justify-content-center" style="width: 60px; height: 60px;">
                            <i class="fas fa-lightbulb fa-2x text-info"></i>
                        </div>
                    </div>
                    <h5 class="card-title fw-bold">Innovaciones</h5>
                    <p class="card-text text-muted small mb-4">Exporta el registro de iniciativas de innovación pedagógica.</p>
                    
                    <div class="d-grid gap-2">
                        @can('export-reports')
                        <a href="{{ route('reports.innovations.pdf') }}" class="btn btn-outline-danger btn-sm">
                            <i class="fas fa-file-pdf me-2"></i> Descargar PDF
                        </a>
                        <a href="{{ route('reports.innovations.excel') }}" class="btn btn-outline-success btn-sm">
                            <i class="fas fa-file-excel me-2"></i> Descargar Excel
                        </a>
                        @else
                        <button class="btn btn-secondary btn-sm" disabled>
                            <i class="fas fa-lock me-2"></i> Sin permisos
                        </button>
                        @endcan
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row g-4">
        <!-- Reporte de Participación Docente -->
        <div class="col-md-6">
            <div class="card shadow-sm border-0 h-100">
                <div class="card-body d-flex align-items-center p-4">
                    <div class="me-4 text-primary opacity-50">
                        <i class="fas fa-users-cog fa-3x"></i>
                    </div>
                    <div>
                        <h5 class="fw-bold text-primary mb-1">Participación Docente</h5>
                        <p class="text-muted small mb-3">Visualiza métricas de desempeño y participación.</p>
                        <a href="{{ route('reports.participation') }}" class="btn btn-primary btn-sm">
                            Ver Reporte <i class="fas fa-arrow-right ms-1"></i>
                        </a>
                    </div>
                </div>
            </div>
        </div>

        <!-- Reportes Comparativos -->
        <div class="col-md-6">
            <div class="card shadow-sm border-0 h-100">
                <div class="card-body d-flex align-items-center p-4">
                    <div class="me-4 text-success opacity-50">
                        <i class="fas fa-chart-line fa-3x"></i>
                    </div>
                    <div>
                        <h5 class="fw-bold text-success mb-1">Reportes Comparativos</h5>
                        <p class="text-muted small mb-3">Compara métricas entre diferentes períodos.</p>
                        <a href="{{ route('reports.comparative') }}" class="btn btn-success btn-sm">
                            Ver Comparativa <i class="fas fa-arrow-right ms-1"></i>
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Información de Permisos -->
    @cannot('export-reports')
    <div class="alert alert-warning mt-4 text-center small">
        <i class="fas fa-info-circle me-1"></i> 
        Solo los usuarios con rol de <strong>Admin</strong> o <strong>Coordinador</strong> pueden exportar documentos.
    </div>
    @endcannot
</div>

@push('styles')
<style>
    .text-xs { font-size: 0.7rem; }
    .border-left-primary { border-left: 4px solid #4e73df !important; }
    .border-left-success { border-left: 4px solid #1cc88a !important; }
    .border-left-warning { border-left: 4px solid #f6c23e !important; }
    .border-left-info { border-left: 4px solid #36b9cc !important; }
    .border-left-danger { border-left: 4px solid #e74a3b !important; }
</style>
@endpush
@endsection
