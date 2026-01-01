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
        Schema::create('projects', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->text('description');
            $table->text('objectives')->nullable();
            $table->foreignId('category_id')->constrained()->onDelete('cascade');
            $table->foreignId('profile_id')->constrained('profiles')->onDelete('cascade'); // Responsable
            $table->date('start_date');
            $table->date('end_date');
            $table->enum('status', ['planificacion', 'en_progreso', 'finalizado', 'en_riesgo'])->default('planificacion');
            $table->decimal('budget', 10, 2)->nullable();
            $table->text('impact_description')->nullable();
            $table->integer('completion_percentage')->default(0);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('projects');
    }
};
