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
        // Verifica si la columna 'jornada' ya existe antes de intentar añadirla
        if (!Schema::hasColumn('partidos', 'jornada')) {
            Schema::table('partidos', function (Blueprint $table) {
                // Añadir la columna 'jornada' después de 'equipo_visitante_id'
                $table->integer('jornada')->default(1)->after('equipo_visitante_id');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('partidos', function (Blueprint $table) {
            // Eliminar la columna 'jornada' al revertir
            $table->dropColumn('jornada');
        });
    }
};