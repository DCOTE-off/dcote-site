<?php

namespace Tests\Feature;

use App\Models\AnimeEpisode;
use App\Models\AnimeSeason;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class GenerateSitemapCommandTest extends TestCase
{
    use RefreshDatabase;

    private string $path;

    protected function setUp(): void
    {
        parent::setUp();

        $this->path = storage_path('app/sitemap.xml');
    }

    protected function tearDown(): void
    {
        @unlink($this->path);

        parent::tearDown();
    }

    public function test_it_lists_indexable_pages_and_skips_announced_seasons(): void
    {
        $published = AnimeSeason::create([
            'season_number' => 1,
            'status' => AnimeSeason::STATUS_RELEASED,
            'number_of_episodes' => 1,
        ]);
        AnimeEpisode::create([
            'season_id' => $published->id,
            'episode_number' => 1,
            'episode_name' => 'Sitemap test',
            'completed' => true,
            'opening_start' => -1,
            'appear_in' => now()->subDay(),
        ]);

        $announced = AnimeSeason::create([
            'season_number' => 2,
            'status' => AnimeSeason::STATUS_ANNOUNCED,
            'number_of_episodes' => 1,
        ]);

        $this->artisan('sitemap:generate')->assertSuccessful();

        $this->assertFileExists($this->path);
        $xml = file_get_contents($this->path);

        $this->assertStringContainsString(route('home'), $xml);
        $this->assertStringContainsString(route('about-school'), $xml);
        $this->assertStringContainsString(route('privacy_policy'), $xml);
        $this->assertStringContainsString(route('anime.season', ['season' => 1]), $xml);
        $this->assertStringContainsString(route('anime.episode', ['season' => 1, 'episode' => 1]), $xml);

        $announcedUrl = route('anime.season', ['season' => $announced->season_number]);
        $this->assertStringNotContainsString('<loc>'.$announcedUrl.'</loc>', $xml);

        // lastmod проставляется там, где есть достоверная дата.
        $this->assertStringContainsString('<lastmod>', $xml);
    }
}
