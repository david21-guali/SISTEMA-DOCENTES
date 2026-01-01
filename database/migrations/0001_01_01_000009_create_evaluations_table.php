<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('evaluations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('project_id')->constrained()->onDelete('cascade');
            $table->foreignId('evaluator_id')->constrained('profiles')->onDelete('cascade');
            
            // Criterios de evaluación con rúbrica (1-5)
            $table->integer('innovation_score')->nullable()->comment('Nivel de innovación (1-5)');
            $table->integer('relevance_score')->nullable()->comment('Pertinencia del proyecto (1-5)');
            $table->integer('results_score')->nullable()->comment('Calidad de resultados (1-5)');
            $table->integer('impact_score')->nullable()->comment('Impacto en aprendizaje (1-5)');
            $table->integer('methodology_score')->nullable()->comment('Metodología aplicada (1-5)');
            
            // Calificación global (1-10)
            $table->decimal('final_score', 3, 1)->nullable()->comment('Calificación final (1-10)');
            
            // Comentarios y observaciones
            $table->text('strengths')->nullable()->comment('Fortalezas del proyecto');
            $table->text('weaknesses')->nullable()->comment('Debilidades identificadas');
            $table->text('recommendations')->nullable()->comment('Recomendaciones para mejora');
            $table->text('general_comments')->nullable()->comment('Comentarios generales');
            
            // Archivo de informe final (opcional)
            $table->string('report_file')->nullable()->comment('PDF del informe final');
            
            // Estado de la evaluación
            $table->enum('status', ['borrador', 'finalizada'])->default('borrador');
            
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('evaluations');
    }
};
