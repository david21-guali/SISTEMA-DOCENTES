@extends('layouts.admin')

@section('title', 'Participación Docente')

@section('contenido')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2><i class="fas fa-chart-line"></i> Reporte de Participación Docente</h2>
        <a href="{{ route('reports.index') }}" class="btn btn-outline-secondary">
            <i class="fas fa-arrow-left"></i> Volver
        </a>
    </div>

    <div class="card shadow">
        <div class="card-body">
            @if($teachers->isEmpty())
                <div class="alert alert-info">No se encontraron docentes con actividad registrada.</div>
            @else
                <div class="table-responsive">
                    <table class="table table-hover align-middle">
                        <thead class="table-light">
                            <tr>
                                <th>Docente</th>
                                <th class="text-center">Tareas Asignadas</th>
                                <th class="text-center">Comentarios Publicados</th>
                                <th class="text-center">Nivel de Actividad</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($teachers as $teacher)
                            @php
                                $totalActivity = $teacher->assigned_tasks_count + $teacher->comments_count;
                                $activityLevel = $totalActivity > 50 ? 'Alto' : ($totalActivity > 20 ? 'Medio' : 'Bajo');
                                $badgeColor = $totalActivity > 50 ? 'success' : ($totalActivity > 20 ? 'warning' : 'secondary');
                            @endphp
                            <tr>
                                <td>
                                    <div class="d-flex align-items-center">
                                        <div class="avatar-circle bg-primary text-white me-2">
                                            {{ substr($teacher->name, 0, 1) }}
                                        </div>
                                        <div>
                                            <div class="fw-bold">{{ $teacher->name }}</div>
                                            <div class="small text-muted">{{ $teacher->email }}</div>
                                        </div>
                                    </div>
                                </td>
                                <td class="text-center">
                                    <span class="fs-5 fw-bold text-primary">{{ $teacher->assigned_tasks_count }}</span>
                                </td>
                                <td class="text-center">
                                    <span class="fs-5 fw-bold text-info">{{ $teacher->comments_count }}</span>
                                </td>
                                <td class="text-center">
                                    <span class="badge bg-{{ $badgeColor }} rounded-pill">{{ $activityLevel }}</span>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @endif

            <!-- Tabla de Depuración: Otros Usuarios -->
            @if(isset($otherUsers) && $otherUsers->count() > 0)
                <div class="mt-5">
                    <h5 class="text-secondary"><i class="fas fa-search"></i> Otros Usuarios Encontrados</h5>
                    <div class="alert alert-warning small">
                        Estos usuarios existen en el sistema pero <strong>no tienen el rol de "Docente"</strong> asignado, por eso no cuentan en las estadísticas principales.
                    </div>
                    <div class="table-responsive">
                        <table class="table table-sm table-bordered">
                            <thead class="table-light">
                                <tr>
                                    <th>Nombre</th>
                                    <th>Email</th>
                                    <th>Roles Actuales</th>
                                    <th>Acción Sugerida</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($otherUsers as $user)
                                <tr>
                                    <td>{{ $user->name }}</td>
                                    <td>{{ $user->email }}</td>
                                    <td>
                                        @if($user->roles->isEmpty())
                                            <span class="badge bg-secondary">Sin Rol</span>
                                        @else
                                            @foreach($user->roles as $role)
                                                <span class="badge bg-info text-dark">{{ $role->name }}</span>
                                            @endforeach
                                        @endif
                                    </td>
                                    <td>
                                        Contacte al administrador para asignar el rol de Docente.
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            @endif
        </div>
    </div>
</div>

<style>
.avatar-circle {
    width: 40px; 
    height: 40px; 
    border-radius: 50%; 
    display: flex; 
    align-items: center; 
    justify-content: center; 
    font-weight: bold;
}
</style>
@endsection
