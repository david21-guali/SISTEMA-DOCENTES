<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Reporte de Tareas</title>
    <style>
        body { font-family: Arial, sans-serif; font-size: 12px; }
        h1 { color: #333; text-align: center; }
        table { width: 100%; border-collapse: collapse; margin-top: 20px; }
        th { background-color: #F59E0B; color: white; padding: 8px; text-align: left; }
        td { padding: 8px; border-bottom: 1px solid #ddd; }
        tr:nth-child(even) { background-color: #f9f9f9; }
        .header { text-align: center; margin-bottom: 20px; }
        .footer { text-align: center; margin-top: 30px; font-size: 10px; color: #666; }
        .priority-alta { color: #EF4444; font-weight: bold; }
        .priority-media { color: #F59E0B; }
        .priority-baja { color: #10B981; }
    </style>
</head>
<body>
    <div class="header">
        <h1>✅ Reporte de Tareas</h1>
        <p>Generado el: {{ date('d/m/Y H:i') }}</p>
        <p>Total de tareas: {{ $tasks->count() }}</p>
    </div>

    <table>
        <thead>
            <tr>
                <th>ID</th>
                <th>Título</th>
                <th>Proyecto</th>
                <th>Asignado a</th>
                <th>Prioridad</th>
                <th>Estado</th>
                <th>Fecha Límite</th>
            </tr>
        </thead>
        <tbody>
            @foreach($tasks as $task)
            <tr>
                <td>{{ $task->id }}</td>
                <td>{{ $task->title }}</td>
                <td>{{ $task->project->title }}</td>
                <td>{{ $task->assignedUser ? $task->assignedUser->name : 'Sin asignar' }}</td>
                <td class="priority-{{ $task->priority }}">{{ ucfirst($task->priority) }}</td>
                <td>{{ ucfirst($task->status) }}</td>
                <td>{{ $task->due_date->format('d/m/Y') }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>

    <div class="footer">
        <p>Sistema de Gestión de Proyectos e Innovación Docente - {{ date('Y') }}</p>
    </div>
</body>
</html>
