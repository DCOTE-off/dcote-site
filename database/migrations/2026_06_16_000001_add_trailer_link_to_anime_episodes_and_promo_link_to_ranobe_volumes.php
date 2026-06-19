<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('anime_episodes', function (Blueprint $table) {
            $table->text('trailer_link')->nullable()->after('episode_name');
        });

        Schema::table('ranobe_volumes', function (Blueprint $table) {
            $table->text('promo_link')->nullable()->after('volume_description');
        });
    }

    public function down(): void
    {
        Schema::table('anime_episodes', function (Blueprint $table) {
            $table->dropColumn('trailer_link');
        });

        Schema::table('ranobe_volumes', function (Blueprint $table) {
            $table->dropColumn('promo_link');
        });
    }
};
