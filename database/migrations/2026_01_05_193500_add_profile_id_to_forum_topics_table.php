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
        if (!Schema::hasColumn('forum_topics', 'profile_id')) {
            Schema::table('forum_topics', function (Blueprint $table) {
                $table->foreignId('profile_id')->nullable()->after('id')->constrained('profiles')->onDelete('cascade');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('forum_topics', function (Blueprint $table) {
             if (Schema::hasColumn('forum_topics', 'profile_id')) {
                $table->dropForeign(['profile_id']);
                $table->dropColumn('profile_id');
             }
        });
    }
};
