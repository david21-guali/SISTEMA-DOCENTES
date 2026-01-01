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
        Schema::create('innovations', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->text('description');
            $table->foreignId('profile_id')->constrained('profiles')->onDelete('cascade'); // Docente responsable
            $table->foreignId('innovation_type_id')->constrained()->onDelete('cascade');
            $table->text('methodology')->nullable();
            $table->text('expected_results')->nullable();
            $table->text('actual_results')->nullable();
            $table->enum('status', ['propuesta', 'en_implementacion', 'completada'])->default('propuesta');
            $table->integer('impact_score')->nullable(); // 1-10
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('innovations');
    }
};
