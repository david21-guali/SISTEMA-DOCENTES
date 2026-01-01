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
        // Pivot table for Project <-> Profile (Team Members)
        Schema::create('project_profile', function (Blueprint $table) {
            $table->id();
            $table->foreignId('project_id')->constrained()->onDelete('cascade');
            $table->foreignId('profile_id')->constrained('profiles')->onDelete('cascade');
            $table->string('role')->nullable(); // Optional: role within the project (e.g., Lead, Member)
            $table->timestamps();
            
            $table->unique(['project_id', 'profile_id']); // Prevent duplicates
        });

        // Pivot table for Task <-> Profile (Assignees)
        Schema::create('task_profile', function (Blueprint $table) {
            $table->id();
            $table->foreignId('task_id')->constrained()->onDelete('cascade');
            $table->foreignId('profile_id')->constrained('profiles')->onDelete('cascade');
            $table->timestamps();

            $table->unique(['task_id', 'profile_id']); // Prevent duplicates
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('task_profile');
        Schema::dropIfExists('project_profile');
    }
};
