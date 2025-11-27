<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // 1. Definir la URL antigua (el placeholder)
        $oldUrl = 'https://placehold.co/100x100/1f2937/FFFFFF?text=JUGADOR';

        // 2. Construir la nueva URL usando la variable de entorno APP_URL
        // Usamos rtrim para asegurarnos de que no haya doble barra //
        $baseUrl = rtrim(config('app.url'), '/');
        $newUrl = $baseUrl . '/player.png';

        // 3. Ejecutar la actualización masiva
        // Asumiendo que tu tabla se llama 'players'. Si es 'jugadores', cámbialo aquí.
        DB::table('jugadores')
            ->where('foto_url', $oldUrl)
            ->update(['foto_url' => $newUrl]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Lógica para revertir (opcional, pero recomendada)
        $placeholder = 'https://placehold.co/100x100/1f2937/FFFFFF?text=JUGADOR';
        
        $baseUrl = rtrim(config('app.url'), '/');
        $currentUrl = $baseUrl . '/player.png';

        DB::table('jugadores')
            ->where('foto_url', $currentUrl)
            ->update(['foto_url' => $placeholder]);
    }
};
