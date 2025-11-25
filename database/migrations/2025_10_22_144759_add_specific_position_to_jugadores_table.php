<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema; // <-- ¡No olvides importar esto!

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Paso 1: Añadir las nuevas columnas. Las hacemos "nullable" temporalmente
        // para evitar errores al crear la columna en tablas con datos.
        Schema::table('jugadores', function (Blueprint $table) {
            $table->string('posicion_general')->after('numero')->nullable();
            $table->string('posicion_especifica', 10)->after('posicion_general')->nullable();
        });

        // Paso 2: Copiar los datos de la columna antigua a la nueva.
        // Esto ejecuta una consulta SQL: UPDATE jugadores SET posicion_general = posicion;
        DB::table('jugadores')->update(['posicion_general' => DB::raw('posicion')]);

        // Paso 3: Ahora que los datos están a salvo, eliminamos la columna antigua.
        Schema::table('jugadores', function (Blueprint $table) {
            $table->dropColumn('posicion');
        });
    }

    /**
     * Reverse the migrations.
     * También lo mejoramos para que la reversión no pierda datos.
     */
    public function down(): void
    {
        // Paso 1: Recrear la columna antigua.
        Schema::table('jugadores', function (Blueprint $table) {
            $table->string('posicion')->after('numero')->nullable();
        });

        // Paso 2: Copiar los datos de vuelta a la columna antigua.
        DB::table('jugadores')->update(['posicion' => DB::raw('posicion_general')]);

        // Paso 3: Eliminar las nuevas columnas.
        Schema::table('jugadores', function (Blueprint $table) {
            $table->dropColumn(['posicion_general', 'posicion_especifica']);
        });
    }
};
