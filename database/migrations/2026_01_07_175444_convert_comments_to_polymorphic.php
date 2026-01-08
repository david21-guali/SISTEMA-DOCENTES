<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('comments', function (Blueprint $table) {
            // Añadimos las columnas polimórficas
            $table->string('commentable_type')->after('id')->nullable();
            $table->unsignedBigInteger('commentable_id')->after('commentable_type')->nullable();
            $table->index(['commentable_id', 'commentable_type']);
        });

        // Migramos los datos existentes de project_id a la nueva estructura
        DB::table('comments')->update([
            'commentable_type' => 'App\Models\Project',
            'commentable_id' => DB::raw('project_id')
        ]);

        Schema::table('comments', function (Blueprint $table) {
            // Hacemos obligatorias las columnas después de migrar datos
            $table->string('commentable_type')->nullable(false)->change();
            $table->unsignedBigInteger('commentable_id')->nullable(false)->change();
            
            // Eliminamos la columna antigua
            $table->dropForeign(['project_id']);
            $table->dropColumn('project_id');
        });
    }

    public function down(): void
    {
        Schema::table('comments', function (Blueprint $table) {
            $table->foreignId('project_id')->nullable()->constrained()->onDelete('cascade');
        });

        DB::table('comments')->where('commentable_type', 'App\Models\Project')->update([
            'project_id' => DB::raw('commentable_id')
        ]);

        Schema::table('comments', function (Blueprint $table) {
            $table->dropColumn(['commentable_type', 'commentable_id']);
        });
    }
};