<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Reporte de Innovaciones</title>
    <style>
        body { font-family: Arial, sans-serif; font-size: 12px; }
        h1 { color: #333; text-align: center; }
        table { width: 100%; border-collapse: collapse; margin-top: 20px; }
        th { background-color: #06B6D4; color: white; padding: 8px; text-align: left; }
        td { padding: 8px; border-bottom: 1px solid #ddd; }
        tr:nth-child(even) { background-color: #f9f9f9; }
        .header { text-align: center; margin-bottom: 20px; }
        .footer { text-align: center; margin-top: 30px; font-size: 10px; color: #666; }
    </style>
</head>
<body>
    <div class="header">
        <h1>💡 Reporte de Innovaciones Pedagógicas</h1>
        <p>Generado el: {{ date('d/m/Y H:i') }}</p>
        <p>Total de innovaciones: {{ $innovations->count() }}</p>
    </div>

    <table>
        <thead>
            <tr>
                <th>ID</th>
                <th>Título</th>
                <th>Tipo</th>
                <th>Responsable</th>
                <th>Estado</th>
                <th>Impacto</th>
                <th>Archivos</th>
            </tr>
        </thead>
        <tbody>
            @foreach($innovations as $innovation)
            <tr>
                <td>{{ $innovation->id }}</td>
                <td>{{ $innovation->title }}</td>
                <td>{{ $innovation->innovationType->name }}</td>
                <td>{{ $innovation->profile->user->name }}</td>
                <td>{{ ucfirst(str_replace('_', ' ', $innovation->status)) }}</td>
                <td>{{ $innovation->impact_score ? $innovation->impact_score . '/10' : '-' }}</td>
                <td>{{ $innovation->evidence_files ? count($innovation->evidence_files) : 0 }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>

    <div class="footer">
        <p>Sistema de Gestión de Proyectos e Innovación Docente - {{ date('Y') }}</p>
    </div>
</body>
</html>
