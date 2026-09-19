<?php

namespace Tests\Feature\Comments;

use App\Models\AnimeSeason;
use App\Models\Comment;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

trait InteractsWithComments
{
    /**
     * Роли нужны, потому что users.role_id — внешний ключ, а в тестовой БД
     * таблица ролей пустая (миграции данные не сеют).
     *
     * id задаём явно: MySQL не откатывает AUTO_INCREMENT транзакцией, поэтому
     * без явных id во втором тесте роли получили бы id 5–8, а role_id=1
     * перестал бы существовать.
     */
    protected function seedRoles(): void
    {
        $roles = [
            1 => 'Обычный пользователь',
            2 => 'Модератор',
            3 => 'Редактор',
            4 => 'Разработчик',
        ];

        foreach ($roles as $id => $name) {
            DB::table('roles')->insertOrIgnore([
                'id' => $id,
                'name' => $name,
                'description' => 'Тестовая роль',
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
    }

    protected function userWithRole(int $roleId, string $username = 'tester'): User
    {
        return User::create([
            'username' => $username.'_'.$roleId.'_'.Str::random(8),
            'nickname' => 'Пользователь '.$roleId,
            'password' => 'password',
            'role_id' => $roleId,
        ]);
    }

    protected function actingAsSanctum(User $user): void
    {
        $this->actingAs($user, 'sanctum');
    }

    protected function makeSeason(int $number = 1): AnimeSeason
    {
        return AnimeSeason::create([
            'season_number' => $number,
            'number_of_episodes' => 1,
        ]);
    }

    protected function makeComment(
        AnimeSeason $season,
        User $user,
        string $content,
        ?Comment $parent = null,
    ): Comment {
        $comment = Comment::create([
            'commentable_type' => 'anime_season',
            'commentable_id' => $season->id,
            'user_id' => $user->id,
            'parent_id' => $parent?->id,
            'root_id' => $parent?->root_id ?? $parent?->id,
            'content' => $content,
        ]);

        // Как и контроллер, корню проставляем root_id без обновления updated_at.
        if ($parent === null) {
            DB::table('comments')->where('id', $comment->id)->update(['root_id' => $comment->id]);
            $comment->root_id = $comment->id;
        }

        return $comment;
    }

    protected function commentsUrl(AnimeSeason $season): string
    {
        return "/api/comments?commentable_type=anime_season&commentable_id={$season->id}";
    }
}
