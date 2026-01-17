@extends('layouts.admin')

@section('title', 'Dashboard')

@section('contenido')
<div class="container-fluid">
    <!-- Page Header -->
    <div class="d-flex flex-column flex-sm-row justify-content-between align-items-sm-center mb-4 gap-2">
        <div>
            <h5 class="mb-0 text-dark fw-bold"><i class="fas fa-tachometer-alt me-2 text-primary"></i>Dashboard</h5>
            <p class="text-muted mb-0 small">Resumen general del sistema</p>
        </div>
        <div class="text-muted small align-self-start align-self-sm-center">
            <i class="fas fa-calendar me-1"></i>{{ now()->locale('es')->isoFormat('D MMM, YYYY') }}
        </div>
    </div>

    <!-- Top Section: Welcome, Activity, Progress -->
    <div class="row g-4 mb-4">
        <!-- Welcome Card -->
        <div class="col-xl-4">
            <div class="card h-100" style="background: linear-gradient(135deg, #4e73df 0%, #224abe 100%);">
                <div class="card-body text-white d-flex flex-column justify-content-between">
                    <div>
                        <h5 class="fw-bold mb-2">¡Bienvenido, {{ Auth::user()->name }}!</h5>
                        <p class="opacity-75 mb-3" style="font-size: 0.9rem;">
                            Gestiona tus proyectos de innovación docente, tareas y reuniones desde un solo lugar.
                        </p>
                    </div>
                    <div class="text-center py-3">
                        @if(Auth::user()->profile && Auth::user()->profile->avatar)
                            <img src="{{ asset('storage/' . Auth::user()->profile->avatar) }}" 
                                 class="rounded-circle border border-3 border-white shadow-sm mx-auto" 
                                 style="width: 100px; height: 100px; object-fit: cover; display: block;">
                        @else
                            <div class="rounded-circle d-flex align-items-center justify-content-center mx-auto border border-3 border-white shadow-sm" 
                                 style="width: 100px; height: 100px; background: rgba(255,255,255,0.2); font-size: 3rem; color: white; font-weight: 700;">
                                {{ strtoupper(substr(Auth::user()->name, 0, 1)) }}
                            </div>
                        @endif
                    </div>
                    @can('create-projects')
                    <a href="{{ route('projects.create') }}" class="btn btn-light btn-sm align-self-start">
                        <i class="fas fa-plus me-1"></i> Nuevo Proyecto
                    </a>
                    @else
                    <button class="btn btn-light btn-sm align-self-start opacity-50" disabled>
                        <i class="fas fa-lock me-1"></i> Sin permisos
                    </button>
                    @endcan
                </div>
            </div>
        </div>

        <!-- Global Activity Timeline -->
        <div class="col-xl-4">
            <div class="card h-100">
                <div class="card-header py-3 d-flex justify-content-between align-items-center">
                    <h6 class="m-0 fw-bold text-primary"><i class="fas fa-stream me-1"></i> Actividad Global</h6>
                </div>
                <div class="card-body p-0" style="max-height: 280px; overflow-y: auto;">
                    @forelse($activityTimeline as $activity)
                        <div class="d-flex align-items-start px-3 py-2 border-bottom hover-bg-light">
                            <div class="flex-shrink-0 me-3 pt-1">
                                <div class="rounded-circle bg-{{ $activity['color'] }} bg-opacity-10 d-flex align-items-center justify-content-center" style="width: 36px; height: 36px;">
                                    <i class="{{ $activity['icon'] }} text-{{ $activity['color'] }} fa-sm"></i>
                                </div>
                            </div>
                            <div class="flex-grow-1">
                                <div class="d-flex justify-content-between align-items-center">
                                    <span class="small fw-bold text-{{ $activity['color'] }}">{{ $activity['title'] }}</span>
                                    <small class="text-muted" style="font-size: 0.7rem;">{{ $activity['date']->diffForHumans() }}</small>
                                </div>
                                <p class="mb-0 small text-dark">{{ Str::limit($activity['description'], 40) }}</p>
                                <small class="text-muted"><i class="fas fa-user fa-xs me-1"></i>{{ $activity['user'] }}</small>
                            </div>
                        </div>
                    @empty
                        <div class="text-center py-4">
                            <i class="fas fa-inbox text-muted mb-2 d-block fa-2x opacity-50"></i>
                            <small class="text-muted">Sin actividad reciente</small>
                        </div>
                    @endforelse
                </div>
            </div>
        </div>

        <!-- Progress Tracker -->
        <div class="col-xl-4">
            <div class="card h-100">
                <div class="card-header py-3">
                    <h6 class="m-0 fw-bold text-primary">Progreso General</h6>
                </div>
                <div class="card-body">
                    <!-- Projects Progress -->
                    <div class="mb-3">
                        <div class="d-flex justify-content-between small mb-1">
                            <span>Proyectos Completados</span>
                            <span class="fw-bold">{{ $stats['total_projects'] > 0 ? round(($stats['finished_projects'] / $stats['total_projects']) * 100) : 0 }}%</span>
                        </div>
                        <div class="progress" style="height: 8px;">
                            <div class="progress-bar bg-success" style="width: {{ $stats['total_projects'] > 0 ? ($stats['finished_projects'] / $stats['total_projects']) * 100 : 0 }}%"></div>
                        </div>
                    </div>

                    <!-- Tasks Progress -->
                    <div class="mb-3">
                        <div class="d-flex justify-content-between small mb-1">
                            <span>Tareas Completadas</span>
                            @php
                                $totalTasks = $stats['pending_tasks'] + $stats['completed_tasks'];
                                $tasksPercent = $totalTasks > 0 ? round(($stats['completed_tasks'] / $totalTasks) * 100) : 0;
                            @endphp
                            <span class="fw-bold">{{ $tasksPercent }}%</span>
                        </div>
                        <div class="progress" style="height: 8px;">
                            <div class="progress-bar bg-primary" style="width: {{ $tasksPercent }}%"></div>
                        </div>
                    </div>

                    <!-- Innovations Progress -->
                    <div class="mb-3">
                        <div class="d-flex justify-content-between small mb-1">
                            <span>Innovaciones Completadas</span>
                            <span class="fw-bold">{{ $stats['total_innovations'] > 0 ? round(($stats['completed_innovations'] / $stats['total_innovations']) * 100) : 0 }}%</span>
                        </div>
                        <div class="progress" style="height: 8px;">
                            <div class="progress-bar bg-info" style="width: {{ $stats['total_innovations'] > 0 ? ($stats['completed_innovations'] / $stats['total_innovations']) * 100 : 0 }}%"></div>
                        </div>
                    </div>

                    <!-- Risk Projects -->
                    <div class="mb-0">
                        <div class="d-flex justify-content-between small mb-1">
                            <span>Proyectos en Riesgo</span>
                            <span class="fw-bold text-danger">{{ $stats['at_risk_projects'] }}</span>
                        </div>
                        <div class="progress" style="height: 8px;">
                            <div class="progress-bar bg-danger" style="width: {{ $stats['total_projects'] > 0 ? min(($stats['at_risk_projects'] / $stats['total_projects']) * 100, 100) : 0 }}%"></div>
                        </div>
                    </div>

                    <hr class="my-3">
                    <a href="{{ route('projects.index') }}" class="small text-decoration-none">
                        <i class="fas fa-arrow-right me-1"></i> Ver todos los proyectos
                    </a>
                </div>
            </div>
        </div>
    </div>

    <!-- Stat Cards -->
    <div class="row row-cols-2 row-cols-md-2 row-cols-xl-4 g-3 mb-4">
        <div class="col">
            <div class="card text-white h-100" style="background: linear-gradient(135deg, #4e73df 0%, #224abe 100%);">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-start">
                        <div>
                            <div class="small text-white-50 text-uppercase fw-bold">Proyectos Activos</div>
                            <div class="h3 mb-0 fw-bold">{{ $stats['active_projects'] }}</div>
                        </div>
                        <div class="opacity-50">
                            <i class="fas fa-project-diagram fa-2x"></i>
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
        <div class="col">
            <div class="card text-white h-100" style="background: linear-gradient(135deg, #f6c23e 0%, #dda20a 100%);">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-start">
                        <div>
                            <div class="small text-white-50 text-uppercase fw-bold">Tareas Pendientes</div>
                            <div class="h3 mb-0 fw-bold">{{ $stats['pending_tasks'] }}</div>
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
        <div class="col">
            <div class="card text-white h-100" style="background: linear-gradient(135deg, #1cc88a 0%, #13855c 100%);">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-start">
                        <div>
                            <div class="small text-white-50 text-uppercase fw-bold">Tareas Completadas</div>
                            <div class="h3 mb-0 fw-bold">{{ $stats['completed_tasks'] }}</div>
                        </div>
                        <div class="opacity-50">
                            <i class="fas fa-check-circle fa-2x"></i>
                        </div>
                    </div>
                </div>
                <div class="card-footer bg-transparent border-top-0 pt-0">
                    <a href="{{ route('tasks.index') }}" class="text-white small text-decoration-none">
                        Ver Detalles <i class="fas fa-arrow-right ms-1"></i>
                    </a>
                </div>
            </div>
        </div>
        <div class="col">
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
    </div>

    <!-- Charts Row -->
    <div class="row g-4 mb-4">
        <!-- Tasks Chart (NEW) -->
        <div class="col-xl-4">
            <div class="card h-100">
                <div class="card-header py-3">
                    <h6 class="m-0 fw-bold text-primary">
                        <i class="fas fa-tasks me-1"></i> Estado de Tareas
                    </h6>
                </div>
                <div class="card-body d-flex flex-column justify-content-center">
                    <div style="position: relative; height: 250px; width: 100%;">
                        <canvas id="tasksChart"></canvas>
                    </div>
                    <div class="d-flex justify-content-between px-1 px-sm-3 mt-3">
                        <div class="text-center flex-fill">
                            <span class="badge bg-warning bg-opacity-10 text-warning px-2 py-2 rounded-pill mb-1">{{ $taskStats['pending'] }}</span>
                            <div class="text-muted" style="font-size: 0.75rem;">Pendientes</div>
                        </div>
                        <div class="text-center flex-fill">
                            <span class="badge bg-success bg-opacity-10 text-success px-2 py-2 rounded-pill mb-1">{{ $taskStats['completed'] }}</span>
                            <div class="text-muted" style="font-size: 0.75rem;">Listas</div>
                        </div>
                        <div class="text-center flex-fill">
                            <span class="badge bg-danger bg-opacity-10 text-danger px-2 py-2 rounded-pill mb-1">{{ $taskStats['overdue'] }}</span>
                            <div class="text-muted" style="font-size: 0.75rem;">Atrasadas</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Category Chart -->
        <div class="col-xl-4">
            <div class="card h-100">
                <div class="card-header py-3">
                    <h6 class="m-0 fw-bold text-primary">
                        <i class="fas fa-chart-pie me-1"></i> Proyectos por Categoría
                    </h6>
                </div>
                <div class="card-body">
                    <div style="position: relative; height: 250px; width: 100%;">
                        <canvas id="categoryChart"></canvas>
                    </div>
                </div>
            </div>
        </div>

        <!-- Monthly Chart -->
        <div class="col-xl-4">
            <div class="card h-100">
                <div class="card-header py-3">
                    <h6 class="m-0 fw-bold text-primary">
                        <i class="fas fa-chart-bar me-1"></i> Proyectos por Mes
                    </h6>
                </div>
                <div class="card-body">
                    <div style="position: relative; height: 250px; width: 100%;">
                        <canvas id="monthlyChart"></canvas>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Recent Projects Table -->
    <div class="card">
        <div class="card-header py-3 d-flex justify-content-between align-items-center">
            <h6 class="m-0 fw-bold text-primary">
                <i class="fas fa-folder-open me-1"></i> Proyectos Recientes
            </h6>
            <a href="{{ route('projects.index') }}" class="btn btn-primary btn-sm">
                Ver Todos
            </a>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover mb-0">
                    <thead>
                        <tr>
                            <th>Proyecto</th>
                            <th>Categoría</th>
                            <th>Responsable</th>
                            <th>Estado</th>
                            <th>Progreso</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($recentProjects as $project)
                            <tr>
                                <td>
                                    <a href="{{ route('projects.show', $project) }}" class="text-decoration-none fw-medium">
                                        {{ $project->title }}
                                    </a>
                                </td>
                                <td>
                                    <span class="badge" style="background-color: {{ $project->category->color ?? '#6c757d' }};">
                                        {{ $project->category->name ?? 'Sin categoría' }}
                                    </span>
                                </td>
                                <td class="text-muted small">{{ $project->profile->user->name ?? 'N/A' }}</td>
                                <td>
                                    <span class="badge bg-{{ $project->status_color ?? 'secondary' }} bg-opacity-10 text-{{ $project->status_color ?? 'secondary' }}">
                                        {{ ucfirst(str_replace('_', ' ', $project->status)) }}
                                    </span>
                                </td>
                                <td style="width: 150px;">
                                    <div class="d-flex align-items-center">
                                        <div class="progress flex-grow-1 me-2" style="height: 6px;">
                                            <div class="progress-bar bg-primary" style="width: {{ $project->progress ?? 0 }}%"></div>
                                        </div>
                                        <small class="text-muted">{{ $project->progress ?? 0 }}%</small>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="text-center py-4 text-muted">
                                    No hay proyectos registrados
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
@push('styles')
    <link rel="stylesheet" href="{{ asset('assets/back/css/dashboard.css') }}">
@endpush
@endsection

@push('scripts')
<script>
    window.DashboardConfig = {
        categoryData: {!! json_encode($projectsByCategory->values()) !!},
        categoryLabels: {!! json_encode($projectsByCategory->keys()) !!},
        monthlyData: {!! json_encode($projectsByMonth) !!},
        taskStats: [{{ $taskStats['pending'] }}, {{ $taskStats['completed'] }}, {{ $taskStats['overdue'] }}]
    };
</script>
@vite(['resources/js/pages/dashboard-index.js'])
@endpush
```
