<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        // 1. Таблица anime_seasons
        Schema::create('anime_seasons', function (Blueprint $table) {
            $table->id();
            $table->string('status')->default('Вышел')->nullable();
            $table->string('season_time')->nullable();
            $table->string('release_time')->nullable();
            $table->string('studio')->nullable();
            $table->integer('number_of_episodes')->nullable();
            $table->string('last_update')->nullable();
            $table->string('img_src')->nullable();
            $table->integer('season_number')->nullable();
            $table->text('season_description')->nullable();
            $table->text('trailer_link')->nullable();
            $table->text('adapt_volumes')->nullable();
            $table->text('adapt_volumes_brackets')->nullable();
        });

        // 2. Таблица anime_episodes
        Schema::create('anime_episodes', function (Blueprint $table) {
            $table->id();
            $table->integer('episode_number')->nullable();
            $table->string('episode_name')->nullable();
            $table->boolean('has_dub')->default(false)->nullable();
            $table->boolean('has_sub')->default(false)->nullable();
            $table->boolean('has_anilibria')->default(false)->nullable();
            $table->integer('opening_start')->default(-1)->nullable();
            $table->timestamp('appear_in')->nullable();
            

            $table->foreignId('season_id')->nullable()->constrained('anime_seasons')->onDelete('cascade');
        });

        // 3. Таблица classes_top
        Schema::create('classes_top', function (Blueprint $table) {
            $table->id();
            $table->string('letter', 3)->nullable();
            $table->string('leader')->nullable();
            $table->integer('class_points')->nullable();
            $table->text('leader_img')->nullable();
            $table->boolean('spoilers')->nullable();
            $table->string('color', 32)->nullable();
        });

        // 4. Таблица update_feed
        Schema::create('update_feed', function (Blueprint $table) {
            $table->id();
            $table->text('description');
            $table->string('link');
            $table->dateTime('created_at')->nullable();
        });
    }


    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        // Очередность удаления ВАЖНА: сначала таблицы с внешними ключами, затем родительские
        Schema::dropIfExists('anime_episodes');
        Schema::dropIfExists('anime_seasons');
        Schema::dropIfExists('classes_top');
        Schema::dropIfExists('update_feed');
    }
};