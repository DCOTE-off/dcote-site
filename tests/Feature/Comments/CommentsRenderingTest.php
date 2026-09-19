<?php

namespace Tests\Feature\Comments;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CommentsRenderingTest extends TestCase
{
    use InteractsWithComments;
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        $this->seedRoles();
    }

    public function test_markdown_formatting_is_rendered(): void
    {
        $season = $this->makeSeason();
        $author = $this->userWithRole(1);
        $this->makeComment($season, $author, '**жирный** *курсив* ~~зачёркнутый~~');

        $html = $this->getJson($this->commentsUrl($season))->json('data.0.html');

        $this->assertStringContainsString('<strong>жирный</strong>', $html);
        $this->assertStringContainsString('<em>курсив</em>', $html);
        $this->assertStringContainsString('<del>зачёркнутый</del>', $html);
    }

    public function test_spoiler_is_wrapped_into_a_toggle_element(): void
    {
        $season = $this->makeSeason();
        $author = $this->userWithRole(1);
        $this->makeComment($season, $author, 'Смотри ||секретный текст|| дальше');

        $html = $this->getJson($this->commentsUrl($season))->json('data.0.html');

        $this->assertStringContainsString('class="comment__spoiler"', $html);
        $this->assertStringContainsString('class="comment__spoiler-content">секретный текст<', $html);
    }

    public function test_raw_html_from_users_is_stripped(): void
    {
        $season = $this->makeSeason();
        $author = $this->userWithRole(1);
        $this->makeComment($season, $author, '<script>alert(1)</script><b onclick="x">bold</b>');

        $html = $this->getJson($this->commentsUrl($season))->json('data.0.html');

        $this->assertStringNotContainsString('<script', $html);
        $this->assertStringNotContainsString('<b', $html);
        $this->assertStringNotContainsString('onclick', $html);
    }
}
