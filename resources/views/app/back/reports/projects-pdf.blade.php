<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Reporte Ejecutivo de Proyectos</title>
    <style>
        @page { margin: 1.5cm; }
        body { font-family: 'Helvetica', 'Arial', sans-serif; font-size: 10pt; color: #333; line-height: 1.4; }
        
        .header { text-align: center; margin-bottom: 25px; border-bottom: 3px solid #3B82F6; padding-bottom: 10px; }
        .header h1 { margin: 0; color: #1E40AF; font-size: 20pt; text-transform: uppercase; }
        .header p { margin: 5px 0; color: #6B7280; font-style: italic; }
        
        /* Stats Table */
        .stats-container { margin-bottom: 20px; width: 100%; border-spacing: 10px; border-collapse: separate; }
        .stat-card { background: #F3F4F6; padding: 12px; border-radius: 8px; text-align: center; border: 1px solid #E5E7EB; width: 33.33%; }
        .stat-card .label { font-size: 8pt; color: #4B5563; text-transform: uppercase; margin-bottom: 3px; font-weight: bold; }
        .stat-card .value { font-size: 14pt; color: #1E40AF; font-weight: bold; }
        
        /* VISUAL CHART (Pure CSS) */
        .chart-box { margin-bottom: 30px; background: #FFF; padding: 15px; border: 1px solid #E5E7EB; border-radius: 10px; }
        .chart-title { font-size: 9pt; font-weight: bold; color: #374151; margin-bottom: 10px; text-transform: uppercase; text-align: center; }
        .progress-bar-distributed { height: 25px; width: 100%; background: #E5E7EB; border-radius: 12px; overflow: hidden; display: table; border-collapse: collapse; }
        .bar-segment { display: table-cell; height: 25px; transition: width 0.3s; }
        .bar-pending { background-color: #FBBF24; } /* Amber */
        .bar-active { background-color: #3B82F6; }  /* Blue */
        .bar-finished { background-color: #10B981; } /* Green */
        
        .legend { margin-top: 10px; text-align: center; font-size: 8pt; }
        .legend-item { display: inline-block; margin: 0 10px; }
        .dot { height: 8px; width: 8px; border-radius: 50%; display: inline-block; margin-right: 5px; }

        .section-title { font-size: 13pt; color: #111827; border-left: 4px solid #3B82F6; padding-left: 10px; margin: 20px 0 12px 0; font-weight: bold; }
        
        table { width: 100%; border-collapse: collapse; margin-top: 5px; }
        th { background-color: #3B82F6; color: white; padding: 10px; text-align: left; font-size: 9pt; text-transform: uppercase; }
        td { padding: 9px; border-bottom: 1px solid #E5E7EB; font-size: 9pt; }
        tr:nth-child(even) { background-color: #F9FAFB; }
        
        .badge { padding: 3px 8px; border-radius: 12px; font-size: 7.5pt; font-weight: bold; text-transform: uppercase; }
        .status-en-progreso { background: #DBEAFE; color: #1E40AF; }
        .status-completado { background: #D1FAE5; color: #065F46; }
        .status-planificacion { background: #FEF3C7; color: #92400E; }
        
        .footer { position: fixed; bottom: 0; width: 100%; text-align: center; font-size: 8pt; color: #9CA3AF; border-top: 1px solid #E5E7EB; padding-top: 10px; }
    </style>
</head>
<body>
    <div class="header">
        <h1>Reporte Ejecutivo de Proyectos</h1>
        <p>Sistema de Gestión Institucional - {{ date('d/m/Y') }}</p>
    </div>

    <!-- Executive Summary Cards -->
    <table class="stats-container">
        <tr>
            <td class="stat-card">
                <div class="label">Proyectos Totales</div>
                <div class="value">{{ $stats['total'] }}</div>
            </td>
            <td class="stat-card">
                <div class="label">Avance Promedio</div>
                <div class="value">{{ number_format($stats['avg_completion'], 1) }}%</div>
            </td>
            <td class="stat-card">
                <div class="label">Carga de Trabajo</div>
                <div class="value" style="color: #059669;">{{ $stats['finished'] }}/{{ $stats['total'] }} Listos</div>
            </td>
        </tr>
    </table>

    <!-- VISUAL CHART: Distribución por Estado -->
    <div class="chart-box">
        <div class="chart-title">Distribución del Portafolio por Estado</div>
        <div class="progress-bar-distributed">
            @if($stats['pct_pending'] > 0)
                <div class="bar-segment bar-pending" style="width: {{ $stats['pct_pending'] }}%;"></div>
            @endif
            @if($stats['pct_active'] > 0)
                <div class="bar-segment bar-active" style="width: {{ $stats['pct_active'] }}%;"></div>
            @endif
            @if($stats['pct_finished'] > 0)
                <div class="bar-segment bar-finished" style="width: {{ $stats['pct_finished'] }}%;"></div>
            @endif
        </div>
        <div class="legend">
            <span class="legend-item"><span class="dot bar-pending"></span> Planificación ({{ $stats['pending'] }})</span>
            <span class="legend-item"><span class="dot bar-active"></span> En Ejecución ({{ $stats['active'] }})</span>
            <span class="legend-item"><span class="dot bar-finished"></span> Completados ({{ $stats['finished'] }})</span>
        </div>
    </div>

    <div class="section-title">Detalle del Portafolio</div>
    <table>
        <thead>
            <tr>
                <th style="width: 5%">#</th>
                <th style="width: 45%">Proyecto / Categoría</th>
                <th style="width: 20%">Responsable</th>
                <th style="width: 15%">Estado</th>
                <th style="width: 15%">Avance</th>
            </tr>
        </thead>
        <tbody>
            @foreach($projects as $index => $project)
            <tr>
                <td>{{ $index + 1 }}</td>
                <td>
                    <div style="font-weight: bold; color: #1E40AF;">{{ $project->title }}</div>
                    <small style="color: #6B7280;">{{ $project->category->name }}</small>
                </td>
                <td>{{ $project->profile->user->name }}</td>
                <td>
                    <span class="badge status-{{ str_replace('_', '-', $project->status) }}">
                        {{ str_replace('_', ' ', $project->status) }}
                    </span>
                </td>
                <td style="text-align: center;">
                    <div style="font-size: 8pt; color: #666; margin-bottom: 2px;">{{ $project->completion_percentage }}%</div>
                    <div style="width: 100%; background: #EEE; height: 4px; border-radius: 2px;">
                        <div style="width: {{ $project->completion_percentage }}%; background: #3B82F6; height: 4px; border-radius: 2px;"></div>
                    </div>
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>

    <div class="footer">
        Documento Generado Automáticamente - Pagina 1 de 1
    </div>
</body>
</html>
