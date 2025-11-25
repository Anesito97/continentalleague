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
        Schema::table('game_scores', function (Blueprint $table) {
            $table->string('game_type')->default('keepy_uppy')->after('user_id');
            // Add index for performance
            $table->index(['game_type', 'score']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('game_scores', function (Blueprint $table) {
            $table->dropIndex(['game_type', 'score']);
            $table->dropColumn('game_type');
        });
    }
};
