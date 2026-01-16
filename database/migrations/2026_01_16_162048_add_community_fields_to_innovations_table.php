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
            $table->decimal('community_score', 5, 2)->nullable()->after('impact_score');
            $table->integer('total_votes')->default(0)->after('community_score');
            $table->timestamp('review_deadline')->nullable()->after('total_votes');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('innovations', function (Blueprint $table) {
            $table->dropColumn(['community_score', 'total_votes', 'review_deadline']);
        });
    }
};
