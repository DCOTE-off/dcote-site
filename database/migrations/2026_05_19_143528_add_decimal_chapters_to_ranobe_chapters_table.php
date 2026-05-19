<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('ranobe_chapters', function (Blueprint $table) {
            $table->decimal('new_chapter_number',8,1);
        });
        DB::table('ranobe_chapters')->update([
            'new_chapter_number' => DB::raw('chapter_number')
        ]);
        Schema::table('ranobe_chapters', function (Blueprint $table) {
            $table->dropColumn('chapter_number');
        });
        Schema::table('ranobe_chapters', function (Blueprint $table) {
            $table->renameColumn('new_chapter_number', 'chapter_number');
        });
    }

    public function down(): void
    {
    Schema::table('ranobe_chapters', function (Blueprint $table) {
        $table->integer('chapter_number_old')->nullable();
        DB::table('ranobe_chapters')->update([
            'chapter_number_old' => DB::raw('CAST(chapter_number AS UNSIGNED)')
        ]);
        $table->dropColumn('chapter_number');
        $table->renameColumn('chapter_number_old', 'chapter_number');
    });
    }
};
