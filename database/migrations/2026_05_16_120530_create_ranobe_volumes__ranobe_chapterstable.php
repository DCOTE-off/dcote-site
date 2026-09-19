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
        Schema::create('ranobe_volumes', function (Blueprint $table) {
            $table->id();
            $table->decimal('volume_number');
            $table->integer('general_number');
            $table->string('status');
            $table->dateTime('release_date_book');
            $table->dateTime('release_date_digital');
            $table->foreignId('ranobe_year_id')->constrained()->cascadeOnDelete();
            $table->integer('pages_quantity');
            $table->string('isbn');
            $table->text('volume_description');
            $table->timestamps();
        });

        Schema::create('ranobe_chapters', function (Blueprint $table) {
            $table->id();
            $table->foreignId('ranobe_volume_id')->constrained()->cascadeOnDelete();
            $table->string('title');
            $table->integer('chapter_number');
            $table->mediumText('chapter_content');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('ranobe_chapters');
        Schema::dropIfExists('ranobe_volumes');
    }
};
