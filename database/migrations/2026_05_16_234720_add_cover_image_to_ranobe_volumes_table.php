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
            $table->string('cover_image')->nullable()->after('general_number');
            $table->string('cover_image_mobile')->nullable()->after('cover_image');
        });
    }

    public function down(): void
    {
        Schema::table('ranobe_volumes', function (Blueprint $table) {
            $table->dropColumn('cover_image');
            $table->dropColumn('cover_image_mobile');
        });
    }
};
