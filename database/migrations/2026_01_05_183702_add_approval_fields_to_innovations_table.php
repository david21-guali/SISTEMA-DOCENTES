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
            $table->unsignedBigInteger('reviewed_by')->nullable()->after('status');
            $table->text('review_notes')->nullable()->after('reviewed_by');
            $table->timestamp('reviewed_at')->nullable()->after('review_notes');
            
            $table->foreign('reviewed_by')->references('id')->on('users')->onDelete('set null');
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
