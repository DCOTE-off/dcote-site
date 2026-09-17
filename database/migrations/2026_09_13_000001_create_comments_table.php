<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('comments', function (Blueprint $table) {
            $table->id();

            // Полиморфная привязка: серия, глава, том, новость, арт и т.д.
            // Тип хранится коротким алиасом из morph map (AppServiceProvider).
            $table->string('commentable_type', 32);
            $table->unsignedBigInteger('commentable_id');

            $table->foreignId('user_id')->constrained()->restrictOnDelete();

            // Ветка: parent_id — прямой родитель, root_id — корень ветки.
            $table->foreignId('parent_id')->nullable()->constrained('comments')->nullOnDelete();
            $table->foreignId('root_id')->nullable()->constrained('comments')->nullOnDelete();

            $table->text('content');
            $table->integer('rating')->default(0);

            $table->timestamps();
            $table->softDeletes();

            $table->index(['commentable_type', 'commentable_id', 'created_at'], 'comments_list_index');
            $table->index(['commentable_type', 'commentable_id', 'rating'], 'comments_rating_index');
            $table->index(['root_id', 'created_at'], 'comments_thread_index');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('comments');
    }
};
