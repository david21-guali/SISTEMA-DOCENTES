<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Informe de Cumplimiento de Tareas</title>
    <style>
        @page { margin: 1.5cm; }
        body { font-family: 'Helvetica', 'Arial', sans-serif; font-size: 10pt; color: #333; line-height: 1.4; }
        .header { text-align: center; margin-bottom: 25px; border-bottom: 3px solid #F59E0B; padding-bottom: 10px; }
        .header h1 { margin: 0; color: #92400E; font-size: 20pt; text-transform: uppercase; }
        .header p { margin: 5px 0; color: #6B7280; font-style: italic; }
        
        .stats-container { margin-bottom: 20px; width: 100%; border-spacing: 10px; border-collapse: separate; }
        .stat-card { background: #FFFBEB; padding: 12px; border-radius: 8px; text-align: center; border: 1px solid #FEF3C7; width: 33.33%; }
        .stat-card .label { font-size: 8pt; color: #92400E; text-transform: uppercase; margin-bottom: 3px; font-weight: bold; }
        .stat-card .value { font-size: 14pt; color: #B45309; font-weight: bold; }
        
        /* VISUAL CHART (Pure CSS) */
        .chart-box { margin-bottom: 30px; background: #FFF; padding: 15px; border: 1px solid #FEF3C7; border-radius: 10px; }
        .chart-title { font-size: 9pt; font-weight: bold; color: #92400E; margin-bottom: 10px; text-transform: uppercase; text-align: center; }
        .progress-bar-distributed { height: 25px; width: 100%; background: #E5E7EB; border-radius: 12px; overflow: hidden; display: table; border-collapse: collapse; }
        .bar-segment { display: table-cell; height: 25px; }
        .bar-alta { background-color: #EF4444; } /* Red */
        .bar-media { background-color: #F59E0B; } /* Amber */
        .bar-baja { background-color: #10B981; }  /* Green */
        
        .legend { margin-top: 10px; text-align: center; font-size: 8pt; }
        .legend-item { display: inline-block; margin: 0 10px; }
        .dot { height: 8px; width: 8px; border-radius: 50%; display: inline-block; margin-right: 5px; }

        .section-title { font-size: 13pt; color: #111827; border-left: 4px solid #F59E0B; padding-left: 10px; margin: 20px 0 12px 0; font-weight: bold; }
        
        table { width: 100%; border-collapse: collapse; margin-top: 5px; }
        th { background-color: #F59E0B; color: white; padding: 10px; text-align: left; font-size: 9pt; text-transform: uppercase; }
        td { padding: 9px; border-bottom: 1px solid #E5E7EB; font-size: 9pt; }
        tr:nth-child(even) { background-color: #FFFBEB; }
        
        .badge { padding: 3px 8px; border-radius: 12px; font-size: 7.5pt; font-weight: bold; text-transform: uppercase; }
        .priority-alta { color: #DC2626; border-left: 3px solid #DC2626; padding-left: 5px; }
        .priority-media { color: #D97706; border-left: 3px solid #D97706; padding-left: 5px; }
        .priority-baja { color: #059669; border-left: 3px solid #059669; padding-left: 5px; }
        
        .status-pendiente { background: #FEF3C7; color: #92400E; }
        .status-completada { background: #D1FAE5; color: #065F46; }
        .status-atrasada { background: #FEE2E2; color: #991B1B; }
        
        .footer { position: fixed; bottom: 0; width: 100%; text-align: center; font-size: 8pt; color: #9CA3AF; border-top: 1px solid #E5E7EB; padding-top: 10px; }
    </style>
</head>
<body>
    <div class="header">
        <h1>Informe de Cumplimiento de Tareas</h1>
        <p>Seguimiento Operativo - Generado el {{ date('d/m/Y') }}</p>
    </div>

    <div class="section-title">Resumen de Productividad</div>
    <table class="stats-container">
        <tr>
            <td class="stat-card">
                <div class="label">Volumen Total</div>
                <div class="value">{{ $stats['total'] }} Tareas</div>
            </td>
            <td class="stat-card">
                <div class="label">Completadas</div>
                <div class="value">{{ $stats['completed'] }}</div>
            </td>
            <td class="stat-card">
                <div class="label">Tasa de Éxito</div>
                <div class="value">{{ number_format($stats['avg_compliance'], 1) }}%</div>
            </td>
        </tr>
    </table>

    <!-- VISUAL CHART: Distribución por Prioridad -->
    <div class="chart-box">
        <div class="chart-title">Mapa de Calor por Prioridad</div>
        <div class="progress-bar-distributed">
            @if($stats['pct_alta'] > 0)
                <div class="bar-segment bar-alta" style="width: {{ $stats['pct_alta'] }}%;"></div>
            @endif
            @if($stats['pct_media'] > 0)
                <div class="bar-segment bar-media" style="width: {{ $stats['pct_media'] }}%;"></div>
            @endif
            @if($stats['pct_baja'] > 0)
                <div class="bar-segment bar-baja" style="width: {{ $stats['pct_baja'] }}%;"></div>
            @endif
        </div>
        <div class="legend">
            <span class="legend-item"><span class="dot bar-alta"></span> Alta (Crítica)</span>
            <span class="legend-item"><span class="dot bar-media"></span> Media (Importante)</span>
            <span class="legend-item"><span class="dot bar-baja"></span> Baja (Normal)</span>
        </div>
    </div>

    <div class="section-title">Detalle Chronológico de Actividades</div>
    <table>
        <thead>
            <tr>
                <th style="width: 5%">#</th>
                <th style="width: 35%">Descripción de la Tarea</th>
                <th style="width: 25%">Proyecto de Origen</th>
                <th style="width: 10%">Prioridad</th>
                <th style="width: 15%">Estado</th>
                <th style="width: 10%">Límite</th>
            </tr>
        </thead>
        <tbody>
            @foreach($tasks as $index => $task)
            <tr>
                <td>{{ $index + 1 }}</td>
                <td>
                    <div style="font-weight: bold;">{{ $task->title }}</div>
                    <small style="color: #666">Asignado: {{ $task->assignedUser ? $task->assignedUser->name : 'N/A' }}</small>
                </td>
                <td>{{ $task->project->title }}</td>
                <td class="priority-{{ $task->priority }}">{{ ucfirst($task->priority) }}</td>
                <td>
                    <span class="badge status-{{ $task->status }}">
                        {{ $task->status }}
                    </span>
                </td>
                <td style="text-align: center;">{{ $task->due_date->format('d/m') }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>

    <div class="footer">
        Control de Cumplimiento - Sistema de Gestión Docente - Pagina 1 de 1
    </div>
</body>
</html>
