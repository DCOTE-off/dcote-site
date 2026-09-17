<?php

namespace App\Policies;

use App\Models\Comment;
use App\Models\User;

class CommentPolicy
{
    /**
     * Роли, которым разрешено модерировать (удалять) чужие комментарии.
     */
    private const MODERATION_ROLE_IDS = [2, 3, 4];

    /**
     * Редактировать может только автор.
     */
    public function update(User $user, Comment $comment): bool
    {
        return (int) $comment->user_id === (int) $user->id;
    }

    /**
     * Удалять (мягко) может автор или модератор/редактор/разработчик.
     */
    public function delete(User $user, Comment $comment): bool
    {
        return (int) $comment->user_id === (int) $user->id
            || in_array($user->role_id, self::MODERATION_ROLE_IDS, true);
    }
}
