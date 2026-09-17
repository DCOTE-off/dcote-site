<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        // Переходим на канонические морф-алиасы:
        // 'anime' -> 'anime_season', 'ranobe' -> 'ranobe_volume'.
        DB::table('populars')
            ->where('target_type', 'anime')
            ->update(['target_type' => 'anime_season']);

        DB::table('populars')
            ->where('target_type', 'ranobe')
            ->update(['target_type' => 'ranobe_volume']);

        // Таблица ratings уже хранит канонические типы; страхуемся на случай старых записей.
        DB::table('ratings')
            ->where('rateable_type', 'anime')
            ->update(['rateable_type' => 'anime_season']);

        DB::table('ratings')
            ->where('rateable_type', 'ranobe')
            ->update(['rateable_type' => 'ranobe_volume']);
    }

    public function down(): void
    {
        DB::table('populars')
            ->where('target_type', 'anime_season')
            ->update(['target_type' => 'anime']);

        DB::table('populars')
            ->where('target_type', 'ranobe_volume')
            ->update(['target_type' => 'ranobe']);
    }
};
