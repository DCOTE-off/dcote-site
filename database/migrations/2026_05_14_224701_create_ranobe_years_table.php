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
        Schema::create('ranobe_years', function (Blueprint $table) {
            $table->id();
            $table->integer('year_number');
            $table->string('year_readable');
            $table->bigInteger('words_quantity');
            $table->string('hours_of_reading');
            $table->string('status');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('ranobe_years');
    }
};
