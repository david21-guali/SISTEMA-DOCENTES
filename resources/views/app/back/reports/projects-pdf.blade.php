<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Reporte de Proyectos</title>
    <style>
        body { font-family: Arial, sans-serif; font-size: 12px; }
        h1 { color: #333; text-align: center; }
        table { width: 100%; border-collapse: collapse; margin-top: 20px; }
        th { background-color: #3B82F6; color: white; padding: 8px; text-align: left; }
        td { padding: 8px; border-bottom: 1px solid #ddd; }
        tr:nth-child(even) { background-color: #f9f9f9; }
        .header { text-align: center; margin-bottom: 20px; }
        .footer { text-align: center; margin-top: 30px; font-size: 10px; color: #666; }
    </style>
</head>
<body>
    <div class="header">
        <h1>📊 Reporte de Proyectos</h1>
        <p>Generado el: {{ date('d/m/Y H:i') }}</p>
        <p>Total de proyectos: {{ $projects->count() }}</p>
    </div>

    <table>
        <thead>
            <tr>
                <th>ID</th>
                <th>Título</th>
                <th>Categoría</th>
                <th>Responsable</th>
                <th>Estado</th>
                <th>Avance</th>
                <th>Fecha Fin</th>
            </tr>
        </thead>
        <tbody>
            @foreach($projects as $project)
            <tr>
                <td>{{ $project->id }}</td>
                <td>{{ $project->title }}</td>
                <td>{{ $project->category->name }}</td>
                <td>{{ $project->profile->user->name }}</td>
                <td>{{ ucfirst(str_replace('_', ' ', $project->status)) }}</td>
                <td>{{ $project->completion_percentage }}%</td>
                <td>{{ $project->end_date->format('d/m/Y') }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>

    <div class="footer">
        <p>Sistema de Gestión de Proyectos e Innovación Docente - {{ date('Y') }}</p>
    </div>
</body>
</html>
