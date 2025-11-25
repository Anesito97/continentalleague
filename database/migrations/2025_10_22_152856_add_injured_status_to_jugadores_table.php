<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('jugadores', function (Blueprint $table) {
            // Añadimos la columna booleana, por defecto en 'false' (no lesionado)
            $table->boolean('esta_lesionado')->default(false)->after('posicion_especifica');
        });
    }

    public function down(): void
    {
        Schema::table('jugadores', function (Blueprint $table) {
            $table->dropColumn('esta_lesionado');
        });
    }
};
