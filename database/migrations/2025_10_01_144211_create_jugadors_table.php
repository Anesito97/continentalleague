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
        Schema::create('jugadores', function (Blueprint $table) {
            $table->id();
            $table->foreignId('equipo_id')->constrained('equipos')->onDelete('cascade');
            $table->string('nombre');
            $table->integer('numero');
            $table->string('posicion'); // portero, defensa, etc.
            $table->string('foto_url')->nullable();
            // Estadísticas individuales
            $table->integer('goles')->default(0);
            $table->integer('asistencias')->default(0);
            $table->integer('paradas')->default(0);
            $table->integer('amarillas')->default(0);
            $table->integer('rojas')->default(0);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('jugadores');
    }
};
