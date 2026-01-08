<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Reporte Comparativo de Gestión</title>
    <style>
        @page { margin: 2cm; }
        body { font-family: 'Helvetica', 'Arial', sans-serif; font-size: 11pt; color: #333; line-height: 1.5; }
        .header { text-align: center; margin-bottom: 30px; border-bottom: 2px solid #3B82F6; padding-bottom: 10px; }
        .header h1 { margin: 0; color: #1E40AF; font-size: 22pt; }
        .header p { margin: 5px 0; color: #666; }
        
        .summary-box { background: #F3F4F6; padding: 15px; border-radius: 8px; margin-bottom: 25px; }
        .summary-box h3 { margin-top: 0; color: #1F2937; border-bottom: 1px solid #DDD; padding-bottom: 5px; }
        
        table { width: 100%; border-collapse: collapse; margin-top: 10px; }
        th { background-color: #E5E7EB; color: #374151; padding: 10px; text-align: left; font-weight: bold; border: 1px solid #D1D5DB; }
        td { padding: 10px; border: 1px solid #D1D5DB; }
        
        .section-title { background-color: #F9FAFB; font-weight: bold; color: #111827; }
        .metric-name { width: 35%; }
        .period-val { width: 25%; text-align: center; }
        .change-val { width: 15%; text-align: center; font-weight: bold; }
        
        .positive { color: #059669; }
        .negative { color: #DC2626; }
        .neutral { color: #6B7280; }
        
        .footer { position: fixed; bottom: 0; width: 100%; text-align: center; font-size: 9pt; color: #9CA3AF; border-top: 1px solid #E5E7EB; padding-top: 5px; }
    </style>
</head>
<body>
    <div class="header">
        <h1>Reporte Comparativo de Gestión</h1>
        <p>Sistema Institucional de Gestión de Proyectos e Innovación</p>
    </div>

    <div class="summary-box">
        <h3>Períodos de Análisis</h3>
        <p><strong>Período 1:</strong> {{ \Carbon\Carbon::parse($period1Start)->format('d/m/Y') }} al {{ \Carbon\Carbon::parse($period1End)->format('d/m/Y') }}</p>
        <p><strong>Período 2:</strong> {{ \Carbon\Carbon::parse($period2Start)->format('d/m/Y') }} al {{ \Carbon\Carbon::parse($period2End)->format('d/m/Y') }}</p>
        <p><em>Este reporte muestra el rendimiento y las variaciones porcentuales entre ambos lapsos de tiempo.</em></p>
    </div>

    <table>
        <thead>
            <tr>
                <th class="metric-name">Indicador de Gestión</th>
                <th class="period-val">Período 1</th>
                <th class="period-val">Período 2</th>
                <th class="change-val">Variación</th>
            </tr>
        </thead>
        <tbody>
            <!-- Proyectos -->
            <tr class="section-title"><td colspan="4">GESTIÓN DE PROYECTOS</td></tr>
            <tr>
                <td>Proyectos Creados</td>
                <td class="period-val">{{ $period1Stats['projects_created'] }}</td>
                <td class="period-val">{{ $period2Stats['projects_created'] }}</td>
                <td class="change-val {{ $changes['projects_created'] > 0 ? 'positive' : ($changes['projects_created'] < 0 ? 'negative' : 'neutral') }}">
                    {{ $changes['projects_created'] > 0 ? '+' : '' }}{{ $changes['projects_created'] }}%
                </td>
            </tr>
            <tr>
                <td>Proyectos Finalizados</td>
                <td class="period-val">{{ $period1Stats['projects_finished'] }}</td>
                <td class="period-val">{{ $period2Stats['projects_finished'] }}</td>
                <td class="change-val {{ $changes['projects_finished'] >= 0 ? 'positive' : 'negative' }}">
                    {{ $changes['projects_finished'] > 0 ? '+' : '' }}{{ $changes['projects_finished'] }}%
                </td>
            </tr>

            <!-- Tareas -->
            <tr class="section-title"><td colspan="4">CUMPLIMIENTO DE TAREAS</td></tr>
            <tr>
                <td>Tareas Creadas</td>
                <td class="period-val">{{ $period1Stats['tasks_created'] }}</td>
                <td class="period-val">{{ $period2Stats['tasks_created'] }}</td>
                <td class="change-val neutral">{{ $changes['tasks_created'] > 0 ? '+' : '' }}{{ $changes['tasks_created'] }}%</td>
            </tr>
            <tr>
                <td>Tareas Completadas</td>
                <td class="period-val">{{ $period1Stats['tasks_completed'] }}</td>
                <td class="period-val">{{ $period2Stats['tasks_completed'] }}</td>
                <td class="change-val {{ $changes['tasks_completed'] >= 0 ? 'positive' : 'negative' }}">
                    {{ $changes['tasks_completed'] > 0 ? '+' : '' }}{{ $changes['tasks_completed'] }}%
                </td>
            </tr>

            <!-- Innovación -->
            <tr class="section-title"><td colspan="4">INNOVACIÓN PEDAGÓGICA</td></tr>
            <tr>
                <td>Nuevas Innovaciones</td>
                <td class="period-val">{{ $period1Stats['innovations_created'] }}</td>
                <td class="period-val">{{ $period2Stats['innovations_created'] }}</td>
                <td class="change-val positive">{{ $changes['innovations_created'] > 0 ? '+' : '' }}{{ $changes['innovations_created'] }}%</td>
            </tr>
            <tr>
                <td>Puntaje de Impacto Promedio</td>
                <td class="period-val">{{ number_format($period1Stats['avg_impact_score'], 1) }}</td>
                <td class="period-val">{{ number_format($period2Stats['avg_impact_score'], 1) }}</td>
                <td class="change-val {{ $changes['avg_impact_score'] >= 0 ? 'positive' : 'negative' }}">
                    {{ $changes['avg_impact_score'] > 0 ? '+' : '' }}{{ $changes['avg_impact_score'] }}%
                </td>
            </tr>
        </tbody>
    </table>

    <div class="footer">
        Documento generado automáticamente por el Sistema de Gestión Docente - {{ date('d/m/Y H:i') }}
    </div>
</body>
</html>
