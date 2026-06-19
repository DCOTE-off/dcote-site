<?php

namespace Tests\Feature;

use App\Models\AnimeEpisode;
use App\Models\AnimeSeason;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Support\Carbon;
use Tests\TestCase;

class ReleaseDueAnimeEpisodesTest extends TestCase
{
    use DatabaseTransactions;

    protected function tearDown(): void
    {
        Carbon::setTestNow();

        parent::tearDown();
    }

    public function test_release_due_marks_only_due_episodes_as_completed(): void
    {
        Carbon::setTestNow('2030-01-15 12:00:00');
        $season = AnimeSeason::query()->firstOrFail();
        $episodeNumber = (int) AnimeEpisode::query()->max('episode_number') + 100;

        $dueEpisode = $this->createEpisode($season, $episodeNumber, false, now()->subMinute());
        $futureEpisode = $this->createEpisode($season, $episodeNumber + 1, false, now()->addMinute());
        $completedEpisode = $this->createEpisode($season, $episodeNumber + 2, true, now()->subMinute());

        $this->assertSame(1, AnimeEpisode::releaseDue());

        $this->assertTrue($dueEpisode->fresh()->completed);
        $this->assertFalse($futureEpisode->fresh()->completed);
        $this->assertTrue($completedEpisode->fresh()->completed);

        $this->assertSame(0, AnimeEpisode::releaseDue());
    }

    private function createEpisode(
        AnimeSeason $season,
        int $episodeNumber,
        bool $completed,
        Carbon $appearIn
    ): AnimeEpisode {
        return AnimeEpisode::create([
            'season_id' => $season->id,
            'episode_number' => $episodeNumber,
            'episode_name' => 'Release scheduler test',
            'completed' => $completed,
            'opening_start' => -1,
            'appear_in' => $appearIn,
        ]);
    }
}
