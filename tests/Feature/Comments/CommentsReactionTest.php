<?php

namespace Tests\Feature\Comments;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CommentsReactionTest extends TestCase
{
    use InteractsWithComments;
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        $this->seedRoles();
    }

    public function test_vote_toggles_on_and_off(): void
    {
        $season = $this->makeSeason();
        $user = $this->userWithRole(1);
        $comment = $this->makeComment($season, $user, 'Комментарий');
        $this->actingAsSanctum($user);

        $this->postJson("/api/comments/{$comment->id}/reaction", ['vote' => 1])
            ->assertOk()
            ->assertJsonPath('rating', 1)
            ->assertJsonPath('user_vote', 1);

        $this->postJson("/api/comments/{$comment->id}/reaction", ['vote' => 1])
            ->assertOk()
            ->assertJsonPath('rating', 0)
            ->assertJsonPath('user_vote', 0);
    }

    public function test_switching_vote_updates_rating_by_the_difference(): void
    {
        $season = $this->makeSeason();
        $user = $this->userWithRole(1);
        $comment = $this->makeComment($season, $user, 'Комментарий');
        $this->actingAsSanctum($user);

        $this->postJson("/api/comments/{$comment->id}/reaction", ['vote' => -1])
            ->assertOk()
            ->assertJsonPath('rating', -1);

        // Смена -1 на +1: дельта +2.
        $this->postJson("/api/comments/{$comment->id}/reaction", ['vote' => 1])
            ->assertOk()
            ->assertJsonPath('rating', 1)
            ->assertJsonPath('user_vote', 1);
    }

    public function test_vote_must_be_one_or_minus_one(): void
    {
        $season = $this->makeSeason();
        $user = $this->userWithRole(1);
        $comment = $this->makeComment($season, $user, 'Комментарий');
        $this->actingAsSanctum($user);

        $this->postJson("/api/comments/{$comment->id}/reaction", ['vote' => 0])
            ->assertStatus(422)
            ->assertJsonValidationErrors('vote');
    }
}
