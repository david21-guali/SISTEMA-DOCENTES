<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        if (DB::getDriverName() !== 'sqlite') {
            // Usamos sentencia SQL directa para modificar el ENUM de manera segura
            DB::statement("ALTER TABLE innovations MODIFY COLUMN status ENUM('propuesta', 'en_implementacion', 'completada', 'en_revision', 'aprobada', 'rechazada') DEFAULT 'propuesta'");
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (DB::getDriverName() !== 'sqlite') {
            // Revertir a los valores originales (¡Cuidado! Esto podría truncar datos si hay registros con nuevos estados)
            DB::statement("ALTER TABLE innovations MODIFY COLUMN status ENUM('propuesta', 'en_implementacion', 'completada') DEFAULT 'propuesta'");
        }
    }
};
