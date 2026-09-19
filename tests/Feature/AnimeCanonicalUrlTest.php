<?php

namespace Tests\Feature;

use App\Models\AnimeEpisode;
use App\Models\AnimeSeason;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AnimeCanonicalUrlTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        $season = AnimeSeason::create([
            'season_number' => 1,
            'status' => AnimeSeason::STATUS_RELEASED,
            'number_of_episodes' => 1,
        ]);
        AnimeEpisode::create([
            'season_id' => $season->id,
            'episode_number' => 1,
            'episode_name' => 'Canonical url test',
            'completed' => true,
            'opening_start' => -1,
        ]);
    }

    public function test_leading_zeros_redirect_to_canonical(): void
    {
        $this->get('/anime/01')->assertRedirect('/anime/1');
        $this->get('/anime/01/01')->assertRedirect('/anime/1/1');
    }

    public function test_canonical_urls_return_ok(): void
    {
        $this->get('/anime/1')->assertOk();
        $this->get('/anime/1/1')->assertOk();
    }
}
