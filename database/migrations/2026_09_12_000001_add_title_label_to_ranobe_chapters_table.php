<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('ranobe_chapters', function (Blueprint $table) {
            $table->string('title_label')->default('')->after('title');
        });

        DB::table('ranobe_chapters')
            ->select('id', 'title')
            ->orderBy('id')
            ->chunkById(100, function ($chapters) {
                foreach ($chapters as $chapter) {
                    if (preg_match('/^\*\*(.+?)\*\*\s*(.*)$/su', $chapter->title, $matches)) {
                        $label = trim($matches[1]);
                        $text = trim($matches[2]);
                    } else {
                        $label = '';
                        $text = trim($chapter->title);
                    }

                    DB::table('ranobe_chapters')->where('id', $chapter->id)->update([
                        'title_label' => $label,
                        'title' => $text,
                    ]);
                }
            });
    }

    public function down(): void
    {
        DB::table('ranobe_chapters')
            ->select('id', 'title', 'title_label')
            ->orderBy('id')
            ->chunkById(100, function ($chapters) {
                foreach ($chapters as $chapter) {
                    if ($chapter->title_label !== '') {
                        $title = trim("**{$chapter->title_label}** {$chapter->title}");
                    } else {
                        $title = $chapter->title;
                    }

                    DB::table('ranobe_chapters')->where('id', $chapter->id)->update([
                        'title' => $title,
                    ]);
                }
            });

        Schema::table('ranobe_chapters', function (Blueprint $table) {
            $table->dropColumn('title_label');
        });
    }
};
