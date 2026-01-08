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
        <div class="col-lg-3 col-md-6">
            <div class="card h-100 shadow-sm border-0 border-top border-4 border-primary">
                <div class="card-body text-center d-flex flex-column">
                    <div>
                        <div class="mb-3">
                            <div class="rounded-circle bg-primary bg-opacity-10 mx-auto d-flex align-items-center justify-content-center" style="width: 60px; height: 60px;">
                                <i class="fas fa-project-diagram fa-2x text-primary"></i>
                            </div>
                        </div>
                        <h5 class="card-title fw-bold">Reportes de Proyectos</h5>
                        <p class="card-text text-muted small mb-4">Genera listados completos de todos los proyectos vinculados.</p>
                    </div>
                    
                    <div class="d-grid gap-2 mt-auto">
                        @if(auth()->user()->hasRole(['admin', 'coordinador', 'docente']))
                        <a href="{{ route('reports.projects.pdf') }}" class="btn btn-outline-primary btn-sm">
                            <i class="fas fa-file-pdf me-2"></i> Descargar PDF
                        </a>
                        <a href="{{ route('reports.projects.excel') }}" class="btn btn-outline-success btn-sm">
                            <i class="fas fa-file-excel me-2"></i> Descargar Excel
                        </a>
                        @else
                        <button class="btn btn-secondary btn-sm" disabled>
                            <i class="fas fa-lock me-2"></i> Sin permisos
                        </button>
                        @endif
                    </div>
                </div>
            </div>
        </div>

        <!-- Reportes de Tareas -->
        <div class="col-lg-3 col-md-6">
            <div class="card h-100 shadow-sm border-0 border-top border-4 border-warning">
                <div class="card-body text-center d-flex flex-column">
                    <div>
                        <div class="mb-3">
                            <div class="rounded-circle bg-warning bg-opacity-10 mx-auto d-flex align-items-center justify-content-center" style="width: 60px; height: 60px;">
                                <i class="fas fa-tasks fa-2x text-warning"></i>
                            </div>
                        </div>
                        <h5 class="card-title fw-bold">Reportes de Tareas</h5>
                        <p class="card-text text-muted small mb-4">Obtén detalles sobre el cumplimiento de tareas y plazos asignados.</p>
                    </div>
                    
                    <div class="d-grid gap-2 mt-auto">
                        @if(auth()->user()->hasRole(['admin', 'coordinador', 'docente']))
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
                        @endif
                    </div>
                </div>
            </div>
        </div>

        <!-- Reportes de Innovaciones -->
        <div class="col-lg-3 col-md-6">
            <div class="card h-100 shadow-sm border-0 border-top border-4 border-info">
                <div class="card-body text-center d-flex flex-column">
                    <div>
                        <div class="mb-3">
                            <div class="rounded-circle bg-info bg-opacity-10 mx-auto d-flex align-items-center justify-content-center" style="width: 60px; height: 60px;">
                                <i class="fas fa-lightbulb fa-2x text-info"></i>
                            </div>
                        </div>
                        <h5 class="card-title fw-bold">Innovaciones</h5>
                        <p class="card-text text-muted small mb-4">Exporta el registro de iniciativas de innovación pedagógica.</p>
                    </div>
                    
                    <div class="d-grid gap-2 mt-auto">
                        @if(auth()->user()->hasRole(['admin', 'coordinador', 'docente']))
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
                        @endif
                    </div>
                </div>
            </div>
        </div>

        <!-- Sincronización de Calendario -->
        <div class="col-lg-3 col-md-6">
            <div class="card h-100 shadow-sm border-0 border-top border-4 border-success">
                <div class="card-body text-center d-flex flex-column">
                    <div>
                        <div class="mb-3">
                            <div class="rounded-circle bg-success bg-opacity-10 mx-auto d-flex align-items-center justify-content-center" style="width: 60px; height: 60px;">
                                <i class="fas fa-calendar-alt fa-2x text-success"></i>
                            </div>
                        </div>
                        <h5 class="card-title fw-bold">Cronograma Personal</h5>
                        <p class="card-text text-muted small mb-4">Sincroniza tus fechas de entrega y reuniones con tu calendario personal.</p>
                    </div>
                    
                    <div class="d-grid gap-2 mt-auto">
                        <div class="btn-group w-100">
                            <a href="{{ route('calendar.export') }}" class="btn btn-success btn-sm">
                                <i class="fas fa-sync me-2"></i> Exportar a Calendario
                            </a>
                            <button class="btn btn-success btn-sm border-start border-white border-opacity-25" data-bs-toggle="modal" data-bs-target="#helpCalendarModal" style="width: 45px; flex: none;">
                                <i class="fas fa-question-circle"></i>
                            </button>
                        </div>
                        <a href="{{ route('calendar.index') }}" class="btn btn-outline-secondary btn-sm">
                            <i class="fas fa-eye me-2"></i> Ver Calendario
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row g-4">
        <!-- Reporte de Participación Docente (SOLO ADMIN) -->
        @role('admin')
        <div class="col-md-6">
            <div class="card shadow-sm border-0 h-100">
                <div class="card-body d-flex align-items-center p-4">
                    <div class="me-4 text-primary opacity-50">
                        <i class="fas fa-users-cog fa-3x"></i>
                    </div>
                    <div>
                        <h5 class="fw-bold text-primary mb-1">Participación Docente</h5>
                        <p class="text-muted small mb-3">Visualiza métricas de desempeño y participación de todo el personal.</p>
                        <a href="{{ route('reports.participation') }}" class="btn btn-primary btn-sm">
                            Ver Reporte <i class="fas fa-arrow-right ms-1"></i>
                        </a>
                    </div>
                </div>
            </div>
        </div>
        @endrole

        <!-- Reportes Comparativos (ADMIN Y COORDINADOR) -->
        @hasanyrole('admin|coordinador')
        <div class="col-md-{{ auth()->user()->hasRole('admin') ? '6' : '12' }}">
            <div class="card shadow-sm border-0 h-100">
                <div class="card-body d-flex align-items-center p-4">
                    <div class="me-4 text-success opacity-50">
                        <i class="fas fa-chart-line fa-3x"></i>
                    </div>
                    <div>
                        <h5 class="fw-bold text-success mb-1">Reportes Comparativos</h5>
                        <p class="text-muted small mb-3">Compara métricas institucionales entre diferentes períodos.</p>
                        <a href="{{ route('reports.comparative') }}" class="btn btn-success btn-sm">
                            Ver Comparativa <i class="fas fa-arrow-right ms-1"></i>
                        </a>
                    </div>
                </div>
            </div>
        </div>
        @endrole
    </div>

    <!-- Información de Permisos -->
    <div class="alert alert-info mt-4 text-center small">
        <i class="fas fa-info-circle me-1"></i> 
        @role('docente')
            Como docente, usted solo puede exportar reportes de los proyectos y tareas en los que participa activamente.
        @else
            Los reportes generados contienen la información consolidada según su nivel de acceso institucional.
        @endrole
    </div>
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

<!-- Help Modal (Calendar) -->
<div class="modal fade" id="helpCalendarModal" tabindex="-1" aria-labelledby="helpCalendarModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow">
            <div class="modal-header bg-info text-white border-0">
                <h5 class="modal-title fw-bold" id="helpCalendarModalLabel">
                    <i class="fas fa-info-circle me-2"></i> ¿Cómo sincronizar tu calendario?
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body p-4">
                <p class="text-muted">La opción <strong>Exportar a Calendario</strong> descarga un archivo <code>.ics</code> que puedes importar en tu calendario personal para recibir recordatorios automáticos.</p>
                
                <div class="mb-4">
                    <h6 class="fw-bold text-dark"><i class="fab fa-google text-danger me-2"></i> Google Calendar</h6>
                    <ol class="small text-muted">
                        <li>Haz clic en "Exportar a Calendario" para descargar el archivo.</li>
                        <li>En tu PC, busca la sección <strong>"Otros calendarios"</strong> en la barra lateral izquierda.</li>
                        <li>Haz clic en el botón <strong>"+"</strong> y selecciona la opción <strong>"Importar"</strong>.</li>
                        <li>Selecciona el archivo descargado y confirma.</li>
                    </ol>
                </div>

                <div class="mb-4">
                    <h6 class="fw-bold text-dark"><i class="fab fa-microsoft text-primary me-2"></i> Outlook / Celular</h6>
                    <ol class="small text-muted">
                        <li>Descarga el archivo <code>.ics</code>.</li>
                        <li>Simplemente abre el archivo en tu computadora o envíalo a tu móvil por correo.</li>
                        <li>El sistema te preguntará si deseas añadir los eventos a tu calendario.</li>
                    </ol>
                </div>

                <div class="alert alert-light border-0 small mb-0">
                    <i class="fas fa-lightbulb text-warning me-2"></i> <strong>Tip:</strong> El archivo es personalizado y solo contiene los eventos en los que participas.
                </div>
            </div>
            <div class="modal-footer border-0 pt-0">
                <button type="button" class="btn btn-secondary btn-sm" data-bs-dismiss="modal">Entendido</button>
            </div>
        </div>
    </div>
</div>
