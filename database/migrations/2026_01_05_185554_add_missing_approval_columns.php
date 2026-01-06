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
        Schema::table('innovations', function (Blueprint $table) {
            // Verificamos si la columna no existe antes de agregarla para evitar errores
            if (!Schema::hasColumn('innovations', 'reviewed_by')) {
                $table->unsignedBigInteger('reviewed_by')->nullable()->after('status');
                $table->foreign('reviewed_by')->references('id')->on('users')->onDelete('set null');
            }
            
            if (!Schema::hasColumn('innovations', 'review_notes')) {
                $table->text('review_notes')->nullable()->after('reviewed_by');
            }
            
            if (!Schema::hasColumn('innovations', 'reviewed_at')) {
                $table->timestamp('reviewed_at')->nullable()->after('review_notes');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('innovations', function (Blueprint $table) {
            $table->dropForeign(['reviewed_by']);
            $table->dropColumn(['reviewed_by', 'review_notes', 'reviewed_at']);
        });
    }
};
