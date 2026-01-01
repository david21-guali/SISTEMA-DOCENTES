<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Reporte de Evaluación</title>
    <style>
        body { font-family: sans-serif; font-size: 14px; color: #333; }
        .header { text-align: center; margin-bottom: 30px; border-bottom: 2px solid #eee; padding-bottom: 10px; }
        .project-info { margin-bottom: 20px; padding: 10px; background: #f9f9f9; }
        .score-card { margin-bottom: 20px; }
        .score-row { display: block; margin-bottom: 10px; }
        .score-label { font-weight: bold; width: 200px; display: inline-block; }
        .score-value { color: #2c3e50; font-size: 16px; font-weight: bold; }
        .final-score { text-align: right; font-size: 24px; color: #27ae60; margin-top: 20px; }
        .section-title { background: #eee; padding: 5px 10px; margin-top: 20px; font-weight: bold; }
        .content { margin-top: 10px; line-height: 1.5; white-space: pre-wrap; }
    </style>
</head>
<body>
    <div class="header">
        <h1>Reporte de Evaluación del Proyecto</h1>
        <p>Fecha: {{ $evaluation->created_at->format('d/m/Y H:i') }}</p>
    </div>

    <div class="project-info">
        <p><strong>Proyecto:</strong> {{ $evaluation->project->title }}</p>
        <p><strong>Responsable:</strong> {{ $evaluation->project->profile->user->name }}</p>
        <p><strong>Evaluador:</strong> {{ $evaluation->evaluator->user->name }}</p>
    </div>

    <div class="score-card">
        <div class="section-title">Resultados de la Rúbrica</div>
        <div style="padding: 10px;">
            <p><strong>Innovación:</strong> {{ $evaluation->innovation_score }}/5</p>
            <p><strong>Pertinencia:</strong> {{ $evaluation->relevance_score }}/5</p>
            <p><strong>Resultados:</strong> {{ $evaluation->results_score }}/5</p>
            <p><strong>Impacto:</strong> {{ $evaluation->impact_score }}/5</p>
            <p><strong>Metodología:</strong> {{ $evaluation->methodology_score }}/5</p>
        </div>
        <div class="final-score">
            Calificación Final: {{ number_format($evaluation->final_score, 1) }}/10
        </div>
    </div>

    <div class="section-title">Fortalezas</div>
    <div class="content">{{ $evaluation->strengths ?: 'Sin observaciones.' }}</div>

    <div class="section-title">Debilidades</div>
    <div class="content">{{ $evaluation->weaknesses ?: 'Sin observaciones.' }}</div>

    <div class="section-title">Recomendaciones</div>
    <div class="content">{{ $evaluation->recommendations ?: 'Sin observaciones.' }}</div>

    <div class="section-title">Comentarios Generales</div>
    <div class="content">{{ $evaluation->general_comments ?: 'Sin observaciones.' }}</div>
</body>
</html>
