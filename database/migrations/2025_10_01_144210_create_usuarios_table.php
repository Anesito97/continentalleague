<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('usuarios', function (Blueprint $table) {
            $table->id();
            $table->string('username')->unique();
            $table->string('password_hash'); // OJO: Usamos el nombre de columna de tu api.php
            $table->string('rol')->default('user'); // admin o user
            $table->timestamps();
        });
        // Seed inicial para Admin
        DB::table('usuarios')->insert([
            'username' => 'Anesito',
            'password_hash' => '@qwer1234', // Reemplazar con Hash::make('tu_pass') en producción
            'rol' => 'admin',
        ]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('usuarios');
    }
};
