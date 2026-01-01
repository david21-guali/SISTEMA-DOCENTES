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
        Schema::create('meetings', function (Blueprint $table) {
            $table->id();
            $table->foreignId('project_id')->nullable()->constrained()->onDelete('cascade');
            $table->string('title');
            $table->text('description')->nullable();
            $table->dateTime('meeting_date');
            $table->string('location')->nullable(); // Ubicación física o enlace virtual
            $table->foreignId('created_by')->constrained('profiles')->onDelete('cascade');
            $table->enum('status', ['pendiente', 'completada', 'cancelada'])->default('pendiente');
            $table->text('notes')->nullable(); // Notas post-reunión
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('meetings');
    }
};
