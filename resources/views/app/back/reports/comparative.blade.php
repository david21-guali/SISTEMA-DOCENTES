@extends('layouts.admin')

@section('title', 'Reportes Comparativos')

@section('contenido')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h2 class="mb-1"><i class="fas fa-chart-line text-primary"></i> Reportes Comparativos</h2>
            <p class="text-muted mb-0">Compara métricas entre dos períodos de tiempo</p>
        </div>
        <a href="{{ route('reports.index') }}" class="btn btn-outline-secondary">
            <i class="fas fa-arrow-left"></i> Volver
        </a>
    </div>

    <!-- Selector de Períodos -->
    <div class="card shadow-sm mb-4">
        <div class="card-header bg-primary text-white">
            <h5 class="mb-0"><i class="fas fa-calendar-alt"></i> Seleccionar Períodos</h5>
        </div>
        <div class="card-body">
            <form action="{{ route('reports.comparative') }}" method="GET" class="row g-3">
                <div class="col-md-6">
                    <div class="card bg-light">
                        <div class="card-header"><strong>📅 Período 1 (Base)</strong></div>
                        <div class="card-body">
                            <div class="row">
                                <div class="col-6">
                                    <label class="form-label">Desde</label>
                                    <input type="date" class="form-control @error('period1_start') is-invalid @enderror" name="period1_start" value="{{ $period1Start }}">
                                    @error('period1_start') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                </div>
                                <div class="col-6">
                                    <label class="form-label">Hasta</label>
                                    <input type="date" class="form-control @error('period1_end') is-invalid @enderror" name="period1_end" value="{{ $period1End }}">
                                    @error('period1_end') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="card bg-light">
                        <div class="card-header"><strong>📅 Período 2 (Comparación)</strong></div>
                        <div class="card-body">
                            <div class="row">
                                <div class="col-6">
                                    <label class="form-label">Desde</label>
                                    <input type="date" class="form-control @error('period2_start') is-invalid @enderror" name="period2_start" value="{{ $period2Start }}">
                                    @error('period2_start') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                </div>
                                <div class="col-6">
                                    <label class="form-label">Hasta</label>
                                    <input type="date" class="form-control @error('period2_end') is-invalid @enderror" name="period2_end" value="{{ $period2End }}">
                                    @error('period2_end') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-12 text-center">
                    <button type="submit" class="btn btn-primary btn-lg px-5">
                        <i class="fas fa-sync-alt"></i> Comparar Períodos
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- Comparativa de Proyectos -->
    <div class="card shadow-sm mb-4">
        <div class="card-header bg-primary text-white">
            <h5 class="mb-0"><i class="fas fa-project-diagram"></i> Proyectos</h5>
        </div>
        <div class="card-body">
            <div class="row g-4">
                <!-- Proyectos Creados -->
                <div class="col-md-3">
                    <div class="card h-100 border-0 shadow-sm">
                        <div class="card-body text-center">
                            <h6 class="text-muted mb-3">Proyectos Creados</h6>
                            <div class="d-flex justify-content-center align-items-center gap-3">
                                <div>
                                    <small class="text-muted">Período 1</small>
                                    <h3 class="mb-0">{{ $period1Stats['projects_created'] }}</h3>
                                </div>
                                <i class="fas fa-arrow-right text-muted"></i>
                                <div>
                                    <small class="text-muted">Período 2</small>
                                    <h3 class="mb-0">{{ $period2Stats['projects_created'] }}</h3>
                                </div>
                            </div>
                            <div class="mt-3">
                                @if($changes['projects_created'] > 0)
                                    <span class="badge bg-success fs-6"><i class="fas fa-arrow-up"></i> {{ $changes['projects_created'] }}%</span>
                                @elseif($changes['projects_created'] < 0)
                                    <span class="badge bg-danger fs-6"><i class="fas fa-arrow-down"></i> {{ abs($changes['projects_created']) }}%</span>
                                @else
                                    <span class="badge bg-secondary fs-6"><i class="fas fa-minus"></i> Sin cambio</span>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Proyectos Finalizados -->
                <div class="col-md-3">
                    <div class="card h-100 border-0 shadow-sm">
                        <div class="card-body text-center">
                            <h6 class="text-muted mb-3">Proyectos Finalizados</h6>
                            <div class="d-flex justify-content-center align-items-center gap-3">
                                <div>
                                    <small class="text-muted">Período 1</small>
                                    <h3 class="mb-0">{{ $period1Stats['projects_finished'] }}</h3>
                                </div>
                                <i class="fas fa-arrow-right text-muted"></i>
                                <div>
                                    <small class="text-muted">Período 2</small>
                                    <h3 class="mb-0">{{ $period2Stats['projects_finished'] }}</h3>
                                </div>
                            </div>
                            <div class="mt-3">
                                @if($changes['projects_finished'] > 0)
                                    <span class="badge bg-success fs-6"><i class="fas fa-arrow-up"></i> {{ $changes['projects_finished'] }}%</span>
                                @elseif($changes['projects_finished'] < 0)
                                    <span class="badge bg-danger fs-6"><i class="fas fa-arrow-down"></i> {{ abs($changes['projects_finished']) }}%</span>
                                @else
                                    <span class="badge bg-secondary fs-6"><i class="fas fa-minus"></i> Sin cambio</span>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Proyectos Activos -->
                <div class="col-md-3">
                    <div class="card h-100 border-0 shadow-sm">
                        <div class="card-body text-center">
                            <h6 class="text-muted mb-3">Proyectos Activos</h6>
                            <div class="d-flex justify-content-center align-items-center gap-3">
                                <div>
                                    <small class="text-muted">Período 1</small>
                                    <h3 class="mb-0">{{ $period1Stats['projects_active'] }}</h3>
                                </div>
                                <i class="fas fa-arrow-right text-muted"></i>
                                <div>
                                    <small class="text-muted">Período 2</small>
                                    <h3 class="mb-0">{{ $period2Stats['projects_active'] }}</h3>
                                </div>
                            </div>
                            <div class="mt-3">
                                @if($changes['projects_active'] > 0)
                                    <span class="badge bg-success fs-6"><i class="fas fa-arrow-up"></i> {{ $changes['projects_active'] }}%</span>
                                @elseif($changes['projects_active'] < 0)
                                    <span class="badge bg-danger fs-6"><i class="fas fa-arrow-down"></i> {{ abs($changes['projects_active']) }}%</span>
                                @else
                                    <span class="badge bg-secondary fs-6"><i class="fas fa-minus"></i> Sin cambio</span>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Proyectos en Riesgo -->
                <div class="col-md-3">
                    <div class="card h-100 border-0 shadow-sm">
                        <div class="card-body text-center">
                            <h6 class="text-muted mb-3">Proyectos en Riesgo</h6>
                            <div class="d-flex justify-content-center align-items-center gap-3">
                                <div>
                                    <small class="text-muted">Período 1</small>
                                    <h3 class="mb-0">{{ $period1Stats['projects_at_risk'] }}</h3>
                                </div>
                                <i class="fas fa-arrow-right text-muted"></i>
                                <div>
                                    <small class="text-muted">Período 2</small>
                                    <h3 class="mb-0">{{ $period2Stats['projects_at_risk'] }}</h3>
                                </div>
                            </div>
                            <div class="mt-3">
                                @if($changes['projects_at_risk'] < 0)
                                    <span class="badge bg-success fs-6"><i class="fas fa-arrow-down"></i> {{ abs($changes['projects_at_risk']) }}%</span>
                                @elseif($changes['projects_at_risk'] > 0)
                                    <span class="badge bg-danger fs-6"><i class="fas fa-arrow-up"></i> {{ $changes['projects_at_risk'] }}%</span>
                                @else
                                    <span class="badge bg-secondary fs-6"><i class="fas fa-minus"></i> Sin cambio</span>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Comparativa de Tareas -->
    <div class="card shadow-sm mb-4">
        <div class="card-header bg-warning">
            <h5 class="mb-0"><i class="fas fa-tasks"></i> Tareas</h5>
        </div>
        <div class="card-body">
            <div class="row g-4">
                <!-- Tareas Creadas -->
                <div class="col-md-4">
                    <div class="card h-100 border-0 shadow-sm">
                        <div class="card-body text-center">
                            <h6 class="text-muted mb-3">Tareas Creadas</h6>
                            <div class="d-flex justify-content-center align-items-center gap-3">
                                <div>
                                    <small class="text-muted">Período 1</small>
                                    <h3 class="mb-0">{{ $period1Stats['tasks_created'] }}</h3>
                                </div>
                                <i class="fas fa-arrow-right text-muted"></i>
                                <div>
                                    <small class="text-muted">Período 2</small>
                                    <h3 class="mb-0">{{ $period2Stats['tasks_created'] }}</h3>
                                </div>
                            </div>
                            <div class="mt-3">
                                @if($changes['tasks_created'] > 0)
                                    <span class="badge bg-success fs-6"><i class="fas fa-arrow-up"></i> {{ $changes['tasks_created'] }}%</span>
                                @elseif($changes['tasks_created'] < 0)
                                    <span class="badge bg-danger fs-6"><i class="fas fa-arrow-down"></i> {{ abs($changes['tasks_created']) }}%</span>
                                @else
                                    <span class="badge bg-secondary fs-6"><i class="fas fa-minus"></i> Sin cambio</span>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Tareas Completadas -->
                <div class="col-md-4">
                    <div class="card h-100 border-0 shadow-sm">
                        <div class="card-body text-center">
                            <h6 class="text-muted mb-3">Tareas Completadas</h6>
                            <div class="d-flex justify-content-center align-items-center gap-3">
                                <div>
                                    <small class="text-muted">Período 1</small>
                                    <h3 class="mb-0">{{ $period1Stats['tasks_completed'] }}</h3>
                                </div>
                                <i class="fas fa-arrow-right text-muted"></i>
                                <div>
                                    <small class="text-muted">Período 2</small>
                                    <h3 class="mb-0">{{ $period2Stats['tasks_completed'] }}</h3>
                                </div>
                            </div>
                            <div class="mt-3">
                                @if($changes['tasks_completed'] > 0)
                                    <span class="badge bg-success fs-6"><i class="fas fa-arrow-up"></i> {{ $changes['tasks_completed'] }}%</span>
                                @elseif($changes['tasks_completed'] < 0)
                                    <span class="badge bg-danger fs-6"><i class="fas fa-arrow-down"></i> {{ abs($changes['tasks_completed']) }}%</span>
                                @else
                                    <span class="badge bg-secondary fs-6"><i class="fas fa-minus"></i> Sin cambio</span>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Tareas Atrasadas -->
                <div class="col-md-4">
                    <div class="card h-100 border-0 shadow-sm">
                        <div class="card-body text-center">
                            <h6 class="text-muted mb-3">Tareas Atrasadas</h6>
                            <div class="d-flex justify-content-center align-items-center gap-3">
                                <div>
                                    <small class="text-muted">Período 1</small>
                                    <h3 class="mb-0">{{ $period1Stats['tasks_overdue'] }}</h3>
                                </div>
                                <i class="fas fa-arrow-right text-muted"></i>
                                <div>
                                    <small class="text-muted">Período 2</small>
                                    <h3 class="mb-0">{{ $period2Stats['tasks_overdue'] }}</h3>
                                </div>
                            </div>
                            <div class="mt-3">
                                @if($changes['tasks_overdue'] < 0)
                                    <span class="badge bg-success fs-6"><i class="fas fa-arrow-down"></i> {{ abs($changes['tasks_overdue']) }}%</span>
                                @elseif($changes['tasks_overdue'] > 0)
                                    <span class="badge bg-danger fs-6"><i class="fas fa-arrow-up"></i> {{ $changes['tasks_overdue'] }}%</span>
                                @else
                                    <span class="badge bg-secondary fs-6"><i class="fas fa-minus"></i> Sin cambio</span>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Comparativa de Innovaciones -->
    <div class="card shadow-sm mb-4">
        <div class="card-header bg-info text-white">
            <h5 class="mb-0"><i class="fas fa-lightbulb"></i> Innovaciones</h5>
        </div>
        <div class="card-body">
            <div class="row g-4">
                <!-- Innovaciones Creadas -->
                <div class="col-md-4">
                    <div class="card h-100 border-0 shadow-sm">
                        <div class="card-body text-center">
                            <h6 class="text-muted mb-3">Innovaciones Registradas</h6>
                            <div class="d-flex justify-content-center align-items-center gap-3">
                                <div>
                                    <small class="text-muted">Período 1</small>
                                    <h3 class="mb-0">{{ $period1Stats['innovations_created'] }}</h3>
                                </div>
                                <i class="fas fa-arrow-right text-muted"></i>
                                <div>
                                    <small class="text-muted">Período 2</small>
                                    <h3 class="mb-0">{{ $period2Stats['innovations_created'] }}</h3>
                                </div>
                            </div>
                            <div class="mt-3">
                                @if($changes['innovations_created'] > 0)
                                    <span class="badge bg-success fs-6"><i class="fas fa-arrow-up"></i> {{ $changes['innovations_created'] }}%</span>
                                @elseif($changes['innovations_created'] < 0)
                                    <span class="badge bg-danger fs-6"><i class="fas fa-arrow-down"></i> {{ abs($changes['innovations_created']) }}%</span>
                                @else
                                    <span class="badge bg-secondary fs-6"><i class="fas fa-minus"></i> Sin cambio</span>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Innovaciones Completadas -->
                <div class="col-md-4">
                    <div class="card h-100 border-0 shadow-sm">
                        <div class="card-body text-center">
                            <h6 class="text-muted mb-3">Innovaciones Completadas</h6>
                            <div class="d-flex justify-content-center align-items-center gap-3">
                                <div>
                                    <small class="text-muted">Período 1</small>
                                    <h3 class="mb-0">{{ $period1Stats['innovations_completed'] }}</h3>
                                </div>
                                <i class="fas fa-arrow-right text-muted"></i>
                                <div>
                                    <small class="text-muted">Período 2</small>
                                    <h3 class="mb-0">{{ $period2Stats['innovations_completed'] }}</h3>
                                </div>
                            </div>
                            <div class="mt-3">
                                @if($changes['innovations_completed'] > 0)
                                    <span class="badge bg-success fs-6"><i class="fas fa-arrow-up"></i> {{ $changes['innovations_completed'] }}%</span>
                                @elseif($changes['innovations_completed'] < 0)
                                    <span class="badge bg-danger fs-6"><i class="fas fa-arrow-down"></i> {{ abs($changes['innovations_completed']) }}%</span>
                                @else
                                    <span class="badge bg-secondary fs-6"><i class="fas fa-minus"></i> Sin cambio</span>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Promedio Impacto -->
                <div class="col-md-4">
                    <div class="card h-100 border-0 shadow-sm">
                        <div class="card-body text-center">
                            <h6 class="text-muted mb-3">Promedio de Impacto</h6>
                            <div class="d-flex justify-content-center align-items-center gap-3">
                                <div>
                                    <small class="text-muted">Período 1</small>
                                    <h3 class="mb-0">{{ number_format($period1Stats['avg_impact_score'], 1) }}</h3>
                                </div>
                                <i class="fas fa-arrow-right text-muted"></i>
                                <div>
                                    <small class="text-muted">Período 2</small>
                                    <h3 class="mb-0">{{ number_format($period2Stats['avg_impact_score'], 1) }}</h3>
                                </div>
                            </div>
                            <div class="mt-3">
                                @if($changes['avg_impact_score'] > 0)
                                    <span class="badge bg-success fs-6"><i class="fas fa-arrow-up"></i> {{ $changes['avg_impact_score'] }}%</span>
                                @elseif($changes['avg_impact_score'] < 0)
                                    <span class="badge bg-danger fs-6"><i class="fas fa-arrow-down"></i> {{ abs($changes['avg_impact_score']) }}%</span>
                                @else
                                    <span class="badge bg-secondary fs-6"><i class="fas fa-minus"></i> Sin cambio</span>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Gráficos de Tendencias -->
    <div class="row g-4 mb-4">
        <div class="col-lg-12">
            <div class="card shadow-sm">
                <div class="card-header bg-transparent">
                    <h5 class="mb-0"><i class="fas fa-chart-line text-primary"></i> Tendencias (Últimos 12 Meses)</h5>
                </div>
                <div class="card-body">
                    <canvas id="trendsChart" height="150"></canvas>
                </div>
            </div>
        </div>
    </div>

    <!-- Tabla Resumen -->
    <div class="card shadow-sm mb-4">
        <div class="card-header bg-transparent">
            <h5 class="mb-0"><i class="fas fa-table text-primary"></i> Resumen Comparativo</h5>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-hover">
                    <thead class="table-light">
                        <tr>
                            <th>Métrica</th>
                            <th class="text-center">Período 1<br><small class="text-muted">{{ \Carbon\Carbon::parse($period1Start)->format('d/m/Y') }} - {{ \Carbon\Carbon::parse($period1End)->format('d/m/Y') }}</small></th>
                            <th class="text-center">Período 2<br><small class="text-muted">{{ \Carbon\Carbon::parse($period2Start)->format('d/m/Y') }} - {{ \Carbon\Carbon::parse($period2End)->format('d/m/Y') }}</small></th>
                            <th class="text-center">Cambio</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td><i class="fas fa-project-diagram text-primary"></i> Proyectos Creados</td>
                            <td class="text-center">{{ $period1Stats['projects_created'] }}</td>
                            <td class="text-center">{{ $period2Stats['projects_created'] }}</td>
                            <td class="text-center">
                                <span class="badge bg-{{ $changes['projects_created'] >= 0 ? 'success' : 'danger' }}">
                                    {{ $changes['projects_created'] >= 0 ? '+' : '' }}{{ $changes['projects_created'] }}%
                                </span>
                            </td>
                        </tr>
                        <tr>
                            <td><i class="fas fa-check-circle text-success"></i> Proyectos Finalizados</td>
                            <td class="text-center">{{ $period1Stats['projects_finished'] }}</td>
                            <td class="text-center">{{ $period2Stats['projects_finished'] }}</td>
                            <td class="text-center">
                                <span class="badge bg-{{ $changes['projects_finished'] >= 0 ? 'success' : 'danger' }}">
                                    {{ $changes['projects_finished'] >= 0 ? '+' : '' }}{{ $changes['projects_finished'] }}%
                                </span>
                            </td>
                        </tr>
                        <tr>
                            <td><i class="fas fa-tasks text-warning"></i> Tareas Creadas</td>
                            <td class="text-center">{{ $period1Stats['tasks_created'] }}</td>
                            <td class="text-center">{{ $period2Stats['tasks_created'] }}</td>
                            <td class="text-center">
                                <span class="badge bg-{{ $changes['tasks_created'] >= 0 ? 'success' : 'danger' }}">
                                    {{ $changes['tasks_created'] >= 0 ? '+' : '' }}{{ $changes['tasks_created'] }}%
                                </span>
                            </td>
                        </tr>
                        <tr>
                            <td><i class="fas fa-check text-success"></i> Tareas Completadas</td>
                            <td class="text-center">{{ $period1Stats['tasks_completed'] }}</td>
                            <td class="text-center">{{ $period2Stats['tasks_completed'] }}</td>
                            <td class="text-center">
                                <span class="badge bg-{{ $changes['tasks_completed'] >= 0 ? 'success' : 'danger' }}">
                                    {{ $changes['tasks_completed'] >= 0 ? '+' : '' }}{{ $changes['tasks_completed'] }}%
                                </span>
                            </td>
                        </tr>
                        <tr>
                            <td><i class="fas fa-lightbulb text-info"></i> Innovaciones Registradas</td>
                            <td class="text-center">{{ $period1Stats['innovations_created'] }}</td>
                            <td class="text-center">{{ $period2Stats['innovations_created'] }}</td>
                            <td class="text-center">
                                <span class="badge bg-{{ $changes['innovations_created'] >= 0 ? 'success' : 'danger' }}">
                                    {{ $changes['innovations_created'] >= 0 ? '+' : '' }}{{ $changes['innovations_created'] }}%
                                </span>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
    // Gráfico de Tendencias
    const trendsCtx = document.getElementById('trendsChart').getContext('2d');
    new Chart(trendsCtx, {
        type: 'line',
        data: {
            labels: {!! json_encode(array_keys($projectsByMonth)) !!},
            datasets: [
                {
                    label: 'Proyectos',
                    data: {!! json_encode(array_values($projectsByMonth)) !!},
                    borderColor: '#3b82f6',
                    backgroundColor: 'rgba(59, 130, 246, 0.1)',
                    tension: 0.3,
                    fill: true
                },
                {
                    label: 'Tareas',
                    data: {!! json_encode(array_values($tasksByMonth)) !!},
                    borderColor: '#f59e0b',
                    backgroundColor: 'rgba(245, 158, 11, 0.1)',
                    tension: 0.3,
                    fill: true
                },
                {
                    label: 'Innovaciones',
                    data: {!! json_encode(array_values($innovationsByMonth)) !!},
                    borderColor: '#06b6d4',
                    backgroundColor: 'rgba(6, 182, 212, 0.1)',
                    tension: 0.3,
                    fill: true
                }
            ]
        },
        options: {
            responsive: true,
            plugins: {
                legend: {
                    position: 'top',
                },
                tooltip: {
                    mode: 'index',
                    intersect: false,
                }
            },
            scales: {
                y: {
                    beginAtZero: true,
                    ticks: {
                        stepSize: 1
                    }
                }
            },
            interaction: {
                mode: 'nearest',
                axis: 'x',
                intersect: false
            }
        }
    });
</script>
@endsection
