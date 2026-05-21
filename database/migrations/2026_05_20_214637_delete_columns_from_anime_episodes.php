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
        Schema::table('anime_episodes', function (Blueprint $table) {
            $table->dropColumn('has_dub');
            $table->dropColumn('has_sub');
            $table->dropColumn('has_anilibria');
            $table->boolean('completed');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('anime_episodes', function (Blueprint $table) {
            //
        });
    }
};
