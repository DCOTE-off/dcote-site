<?php

namespace Tests\Feature\Comments;

use App\Models\CommentReaction;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CommentsLoadTest extends TestCase
{
    use InteractsWithComments;
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        $this->seedRoles();
    }

    public function test_load_is_public_and_returns_total(): void
    {
        $season = $this->makeSeason();
        $author = $this->userWithRole(1);
        $this->makeComment($season, $author, 'Первый');
        $this->makeComment($season, $author, 'Второй');

        $this->getJson($this->commentsUrl($season))
            ->assertOk()
            ->assertJsonCount(2, 'data')
            ->assertJsonPath('total', 2)
            ->assertJsonPath('has_more', false);
    }

    public function test_load_paginates_with_cursor_without_gaps_or_duplicates(): void
    {
        $season = $this->makeSeason();
        $author = $this->userWithRole(1);

        for ($i = 1; $i <= 25; $i++) {
            $this->makeComment($season, $author, "Комментарий {$i}");
        }

        $firstPage = $this->getJson($this->commentsUrl($season))->assertOk();
        $firstPage->assertJsonCount(20, 'data')->assertJsonPath('has_more', true);

        $firstIds = collect($firstPage->json('data'))->pluck('id');
        $cursor = $firstPage->json('next_cursor');
        $this->assertNotNull($cursor);

        $secondPage = $this->getJson($this->commentsUrl($season).'&cursor='.urlencode($cursor))->assertOk();
        $secondPage->assertJsonCount(5, 'data')->assertJsonPath('has_more', false);

        $secondIds = collect($secondPage->json('data'))->pluck('id');

        // Все 25 уникальны и ни один не попал на обе страницы.
        $this->assertCount(25, $firstIds->merge($secondIds)->unique());
        $this->assertCount(0, $firstIds->intersect($secondIds));
    }

    public function test_load_returns_the_full_nested_tree_and_user_votes(): void
    {
        $season = $this->makeSeason();
        $author = $this->userWithRole(1);

        $root = $this->makeComment($season, $author, 'Корень');
        $reply = $this->makeComment($season, $author, 'Ответ', $root);
        $nested = $this->makeComment($season, $author, 'Ответ на ответ', $reply);

        $this->actingAsSanctum($author);
        CommentReaction::create([
            'comment_id' => $nested->id,
            'user_id' => $author->id,
            'vote' => 1,
        ]);

        $data = $this->getJson($this->commentsUrl($season))->assertOk()->json('data');

        $this->assertCount(1, $data);
        $this->assertCount(1, $data[0]['replies']);
        $this->assertCount(1, $data[0]['replies'][0]['replies']);

        $nestedFromApi = $data[0]['replies'][0]['replies'][0];
        $this->assertSame($nested->id, $nestedFromApi['id']);
        $this->assertSame(1, $nestedFromApi['user_vote']);
    }

    public function test_load_marks_other_users_votes_as_zero(): void
    {
        $season = $this->makeSeason();
        $author = $this->userWithRole(1);
        $viewer = $this->userWithRole(1);
        $comment = $this->makeComment($season, $author, 'Комментарий');

        $this->actingAsSanctum($author);
        $this->postJson("/api/comments/{$comment->id}/reaction", ['vote' => 1])->assertOk();

        $this->actingAsSanctum($viewer);

        $this->getJson($this->commentsUrl($season))
            ->assertOk()
            ->assertJsonPath('data.0.user_vote', 0)
            ->assertJsonPath('data.0.rating', 1);
    }
}
