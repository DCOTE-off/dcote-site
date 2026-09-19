<?php

namespace Tests\Feature\Comments;

use App\Models\Comment;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CommentsApiTest extends TestCase
{
    use InteractsWithComments;
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        $this->seedRoles();
    }

    public function test_guest_cannot_create_a_comment(): void
    {
        $season = $this->makeSeason();

        $this->postJson('/api/comments', [
            'commentable_type' => 'anime_season',
            'commentable_id' => $season->id,
            'content' => 'Привет',
        ])->assertUnauthorized();
    }

    public function test_store_creates_a_root_comment(): void
    {
        $season = $this->makeSeason();
        $user = $this->userWithRole(1);
        $this->actingAsSanctum($user);

        $this->postJson('/api/comments', [
            'commentable_type' => 'anime_season',
            'commentable_id' => $season->id,
            'content' => 'Первый комментарий',
        ])
            ->assertOk()
            ->assertJsonPath('comment.content', 'Первый комментарий')
            ->assertJsonPath('comment.author.id', $user->id)
            ->assertJsonPath('total', 1);

        $comment = Comment::firstOrFail();
        $this->assertNull($comment->parent_id);
        $this->assertSame($comment->id, $comment->root_id);
    }

    public function test_store_rejects_empty_content(): void
    {
        $season = $this->makeSeason();
        $this->actingAsSanctum($this->userWithRole(1));

        $this->postJson('/api/comments', [
            'commentable_type' => 'anime_season',
            'commentable_id' => $season->id,
            'content' => '   ',
        ])->assertStatus(422)->assertJsonValidationErrors('content');
    }

    public function test_store_rejects_unknown_material(): void
    {
        $this->actingAsSanctum($this->userWithRole(1));

        $this->postJson('/api/comments', [
            'commentable_type' => 'anime_season',
            'commentable_id' => 999999,
            'content' => 'Привет',
        ])->assertStatus(422)->assertJsonValidationErrors('commentable_id');
    }

    public function test_reply_keeps_the_root_of_its_parent(): void
    {
        $season = $this->makeSeason();
        $author = $this->userWithRole(1);
        $root = $this->makeComment($season, $author, 'Корень');
        $this->actingAsSanctum($author);

        $this->postJson('/api/comments', [
            'commentable_type' => 'anime_season',
            'commentable_id' => $season->id,
            'parent_id' => $root->id,
            'content' => 'Ответ',
        ])->assertOk()->assertJsonPath('total', 2);

        $reply = Comment::where('content', 'Ответ')->firstOrFail();
        $this->assertSame($root->id, $reply->parent_id);
        $this->assertSame($root->id, $reply->root_id);
    }

    public function test_reply_rejects_parent_from_another_material(): void
    {
        $seasonA = $this->makeSeason(1);
        $seasonB = $this->makeSeason(2);
        $user = $this->userWithRole(1);
        $foreign = $this->makeComment($seasonB, $user, 'Чужой комментарий');
        $this->actingAsSanctum($user);

        $this->postJson('/api/comments', [
            'commentable_type' => 'anime_season',
            'commentable_id' => $seasonA->id,
            'parent_id' => $foreign->id,
            'content' => 'Ответ',
        ])->assertStatus(422)->assertJsonValidationErrors('parent_id');
    }

    public function test_only_author_can_update(): void
    {
        $season = $this->makeSeason();
        $author = $this->userWithRole(1);
        $stranger = $this->userWithRole(1);
        $comment = $this->makeComment($season, $author, 'Старый текст');

        $this->actingAsSanctum($stranger);
        $this->patchJson("/api/comments/{$comment->id}", ['content' => 'Взлом'])
            ->assertForbidden();

        $this->actingAsSanctum($author);
        $this->patchJson("/api/comments/{$comment->id}", ['content' => 'Новый текст'])
            ->assertOk()
            ->assertJsonPath('content', 'Новый текст');

        $this->assertSame('Новый текст', $comment->fresh()->content);
    }

    public function test_delete_removes_the_whole_subtree(): void
    {
        $season = $this->makeSeason();
        $author = $this->userWithRole(1);
        $root = $this->makeComment($season, $author, 'Корень');
        $reply = $this->makeComment($season, $author, 'Ответ', $root);

        $this->actingAsSanctum($author);
        $this->deleteJson("/api/comments/{$root->id}")
            ->assertOk()
            ->assertJsonPath('total', 0);

        $this->assertDatabaseMissing('comments', ['id' => $root->id]);
        $this->assertDatabaseMissing('comments', ['id' => $reply->id]);
    }

    public function test_moderator_can_delete_someone_elses_comment(): void
    {
        $season = $this->makeSeason();
        $author = $this->userWithRole(1);
        $moderator = $this->userWithRole(2);
        $comment = $this->makeComment($season, $author, 'Комментарий');

        $this->actingAsSanctum($moderator);
        $this->deleteJson("/api/comments/{$comment->id}")->assertOk();

        $this->assertDatabaseMissing('comments', ['id' => $comment->id]);
    }

    public function test_regular_user_cannot_delete_someone_elses_comment(): void
    {
        $season = $this->makeSeason();
        $author = $this->userWithRole(1);
        $stranger = $this->userWithRole(1);
        $comment = $this->makeComment($season, $author, 'Комментарий');

        $this->actingAsSanctum($stranger);
        $this->deleteJson("/api/comments/{$comment->id}")->assertForbidden();

        $this->assertDatabaseHas('comments', ['id' => $comment->id]);
    }
}
