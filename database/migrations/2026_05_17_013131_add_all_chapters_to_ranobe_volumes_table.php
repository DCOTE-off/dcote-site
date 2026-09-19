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
        Schema::table('ranobe_volumes', function (Blueprint $table) {
            $table->integer('all_chapters')->nullable()->after('isbn');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('ranobe_volumes', function (Blueprint $table) {
            $table->dropColumn('all_chapters');
        });
    }
};
