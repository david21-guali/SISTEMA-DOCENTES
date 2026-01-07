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
        if (!Schema::hasColumn('innovation_types', 'slug')) {
            Schema::table('innovation_types', function (Blueprint $table) {
                $table->string('slug')->unique()->after('name')->nullable();
            });
            
            // Generate slugs for existing records
            $types = \Illuminate\Support\Facades\DB::table('innovation_types')->get();
            foreach ($types as $type) {
                \Illuminate\Support\Facades\DB::table('innovation_types')
                    ->where('id', $type->id)
                    ->update(['slug' => \Illuminate\Support\Str::slug($type->name)]);
            }

            // Make it non-nullable after filling
            Schema::table('innovation_types', function (Blueprint $table) {
                $table->string('slug')->nullable(false)->change();
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('innovation_types', function (Blueprint $table) {
            $table->dropColumn('slug');
        });
    }
};
