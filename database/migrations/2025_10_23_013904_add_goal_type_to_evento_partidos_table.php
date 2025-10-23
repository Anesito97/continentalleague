<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('eventos_partido', function (Blueprint $table) {
            $table->string('goal_type')->nullable()->after('tipo_evento');
        });
    }

    public function down(): void
    {
        Schema::table('eventos_partido', function (Blueprint $table) {
            $table->dropColumn('goal_type');
        });
    }
};