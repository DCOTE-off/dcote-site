<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('comment_reactions', function (Blueprint $table) {
            $table->foreignId('comment_id')->constrained()->cascadeOnDelete();
            $table->foreignId('user_id')->constrained()->restrictOnDelete();

            // 1 — upvote, -1 — downvote.
            $table->tinyInteger('vote');

            $table->timestamp('created_at')->useCurrent();

            // Одна реакция на пользователя на комментарий.
            $table->primary(['comment_id', 'user_id']);
        });

        // MySQL 8.0.16+ проверяет CHECK-констрейнты.
        DB::statement('ALTER TABLE comment_reactions ADD CONSTRAINT comment_reactions_vote_check CHECK (vote IN (-1, 1))');
    }

    public function down(): void
    {
        Schema::dropIfExists('comment_reactions');
    }
};
