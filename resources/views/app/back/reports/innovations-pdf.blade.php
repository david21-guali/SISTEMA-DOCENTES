<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Reporte de Innovación Pedagógica</title>
    <style>
        @page { margin: 1.5cm; }
        body { font-family: 'Helvetica', 'Arial', sans-serif; font-size: 10pt; color: #333; line-height: 1.4; }
        .header { text-align: center; margin-bottom: 25px; border-bottom: 3px solid #06B6D4; padding-bottom: 10px; }
        .header h1 { margin: 0; color: #0E7490; font-size: 20pt; text-transform: uppercase; }
        .header p { margin: 5px 0; color: #6B7280; font-style: italic; }
        
        .stats-container { margin-bottom: 20px; width: 100%; border-spacing: 10px; border-collapse: separate; }
        .stat-card { background: #ECFEFF; padding: 12px; border-radius: 8px; text-align: center; border: 1px solid #CFFAFE; width: 33.33%; }
        .stat-card .label { font-size: 8pt; color: #0E7490; text-transform: uppercase; margin-bottom: 3px; font-weight: bold; }
        .stat-card .value { font-size: 14pt; color: #0891B2; font-weight: bold; }
        
        /* VISUAL CHART (Pure CSS) */
        .chart-box { margin-bottom: 30px; background: #FFF; padding: 15px; border: 1px solid #CFFAFE; border-radius: 10px; }
        .chart-title { font-size: 9pt; font-weight: bold; color: #0E7490; margin-bottom: 10px; text-transform: uppercase; text-align: center; }
        .progress-bar-distributed { height: 25px; width: 100%; background: #E5E7EB; border-radius: 12px; overflow: hidden; display: table; border-collapse: collapse; }
        .bar-segment { display: table-cell; height: 25px; }
        .bar-low { background-color: #94A3B8; } /* Slate */
        .bar-mid { background-color: #06B6D4; } /* Cyan */
        .bar-high { background-color: #059669; } /* Green */
        
        .legend { margin-top: 10px; text-align: center; font-size: 8pt; }
        .legend-item { display: inline-block; margin: 0 10px; }
        .dot { height: 8px; width: 8px; border-radius: 50%; display: inline-block; margin-right: 5px; }

        .section-title { font-size: 13pt; color: #111827; border-left: 4px solid #06B6D4; padding-left: 10px; margin: 20px 0 12px 0; font-weight: bold; }
        
        table { width: 100%; border-collapse: collapse; margin-top: 5px; }
        th { background-color: #06B6D4; color: white; padding: 10px; text-align: left; font-size: 9pt; text-transform: uppercase; }
        td { padding: 9px; border-bottom: 1px solid #E5E7EB; font-size: 9pt; }
        tr:nth-child(even) { background-color: #F0FDFA; }
        
        .impact-high { border-left: 4px solid #059669; padding-left: 5px; font-weight: bold; }
        
        .footer { position: fixed; bottom: 0; width: 100%; text-align: center; font-size: 8pt; color: #9CA3AF; border-top: 1px solid #E5E7EB; padding-top: 10px; }
    </style>
</head>
<body>
    <div class="header">
        <h1>Reporte de Innovación Pedagógica</h1>
        <p>Iniciativas y Mejores Prácticas - Generado el {{ date('d/m/Y') }}</p>
    </div>

    <div class="section-title">Indicadores de Desempeño Académico</div>
    <table class="stats-container">
        <tr>
            <td class="stat-card">
                <div class="label">Total Iniciativas</div>
                <div class="value">{{ $stats['total'] }}</div>
            </td>
            <td class="stat-card">
                <div class="label">Impacto Promedio</div>
                <div class="value">{{ number_format($stats['avg_impact'], 1) }}/10</div>
            </td>
            <td class="stat-card">
                <div class="label">Innovaciones Top</div>
                <div class="value">{{ $innovations->where('impact_score', '>=', 8)->count() }} Altas</div>
            </td>
        </tr>
    </table>

    <!-- VISUAL CHART: Distribución por Nivel de Impacto -->
    <div class="chart-box">
        <div class="chart-title">Espectro de Impacto Pedagógico</div>
        <div class="progress-bar-distributed">
            @if($stats['pct_low'] > 0)
                <div class="bar-segment bar-low" style="width: {{ $stats['pct_low'] }}%;"></div>
            @endif
            @if($stats['pct_mid'] > 0)
                <div class="bar-segment bar-mid" style="width: {{ $stats['pct_mid'] }}%;"></div>
            @endif
            @if($stats['pct_high'] > 0)
                <div class="bar-segment bar-high" style="width: {{ $stats['pct_high'] }}%;"></div>
            @endif
        </div>
        <div class="legend">
            <span class="legend-item"><span class="dot bar-low"></span> Impacto Bajo (0-4)</span>
            <span class="legend-item"><span class="dot bar-mid"></span> Impacto Medio (5-7)</span>
            <span class="legend-item"><span class="dot bar-high"></span> Alto Impacto (8-10)</span>
        </div>
    </div>

    <div class="section-title">Registro de Iniciativas Docentes</div>
    <table>
        <thead>
            <tr>
                <th style="width: 5%">#</th>
                <th style="width: 40%">Título de la Innovación</th>
                <th style="width: 25%">Autor / Responsable</th>
                <th style="width: 20%">Categoría / Tipo</th>
                <th style="width: 10%">Impacto</th>
            </tr>
        </thead>
        <tbody>
            @foreach($innovations as $index => $innovation)
            <tr>
                <td>{{ $index + 1 }}</td>
                <td><strong>{{ $innovation->title }}</strong></td>
                <td>{{ $innovation->profile->user->name }}</td>
                <td>{{ $innovation->innovationType->name }}</td>
                <td style="text-align: center" class="{{ $innovation->impact_score >= 8 ? 'impact-high' : '' }}">
                    {{ $innovation->impact_score ? $innovation->impact_score . '/10' : '-' }}
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>

    <div class="footer">
        Repositorio de Innovación - Sistema de Gestión Docente - Pagina 1 de 1
    </div>
</body>
</html>
