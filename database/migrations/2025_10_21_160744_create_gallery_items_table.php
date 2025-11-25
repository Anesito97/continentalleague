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
        Schema::create('gallery_items', function (Blueprint $table) {
            $table->id(); // Crea 'id' (BIGINT UNSIGNED NOT NULL AUTO_INCREMENT)

            $table->string('titulo')->nullable()->comment('Título opcional de la foto');
            $table->string('image_url');

            // Llave foránea a la tabla 'partidos'
            $table->foreignId('partido_id')
                ->nullable() // Permite que no esté asociado a un partido
                ->constrained('partidos')
                ->onDelete('SET NULL'); // Si el partido se borra, el campo se pone a NULL

            // Llave foránea al usuario/administrador que subió la foto
            // Nota: Asumimos que la tabla de usuarios se llama 'usuarios' o 'users'
            $table->foreignId('uploaded_by_user_id')
                ->nullable()
                ->constrained('usuarios') // Asumo que tu tabla de usuarios se llama 'usuarios'
                ->onDelete('SET NULL');

            $table->timestamps(); // Crea 'created_at' y 'updated_at'
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('gallery_items');
    }
};
