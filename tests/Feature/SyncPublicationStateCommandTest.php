<?php

namespace Tests\Feature;

use App\Models\AnimeEpisode;
use App\Models\AnimeSeason;
use App\Models\RanobeChapter;
use App\Models\RanobeVolume;
use App\Models\RanobeYear;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Carbon;
use Tests\TestCase;

class SyncPublicationStateCommandTest extends TestCase
{
    use RefreshDatabase;

    protected function tearDown(): void
    {
        Carbon::setTestNow();

        parent::tearDown();
    }

    public function test_it_releases_only_due_episodes(): void
    {
        Carbon::setTestNow('2030-01-15 12:00:00');

        $season = AnimeSeason::create([
            'season_number' => 1,
            'status' => AnimeSeason::STATUS_ONGOING,
            'number_of_episodes' => 2,
        ]);

        $dueEpisode = $this->createEpisode($season, 1, now()->subMinute());
        $futureEpisode = $this->createEpisode($season, 2, now()->addMinute());

        $this->artisan('publication:sync')->assertSuccessful();

        $this->assertTrue($dueEpisode->fresh()->completed);
        $this->assertFalse($futureEpisode->fresh()->completed);
        $this->assertSame(AnimeSeason::STATUS_ONGOING, $season->fresh()->status);
    }

    public function test_it_closes_season_and_volume_when_all_parts_have_come_out(): void
    {
        Carbon::setTestNow('2030-01-15 12:00:00');

        $season = AnimeSeason::create([
            'season_number' => 1,
            'status' => AnimeSeason::STATUS_ONGOING,
            'number_of_episodes' => 1,
        ]);
        $this->createEpisode($season, 1, now()->subMinute());

        $year = RanobeYear::create([
            'year_number' => 1,
            'year_readable' => 'Test year',
            'words_quantity' => 0,
            'hours_of_reading' => '0',
            'status' => 'Test',
        ]);
        $volume = RanobeVolume::create([
            'volume_number' => 100.0,
            'general_number' => 1000,
            'cover_image' => 'ranobe/test-cover.webp',
            'cover_image_mobile' => 'ranobe/test-cover-mobile.webp',
            'status' => RanobeVolume::STATUS_ONGOING,
            'release_date_book' => now(),
            'all_chapters' => 1,
            'release_date_digital' => now(),
            'ranobe_year_id' => $year->id,
            'pages_quantity' => 100,
            'isbn' => 'test-isbn-1000',
            'volume_description' => 'Sync command test',
        ]);
        RanobeChapter::create([
            'ranobe_volume_id' => $volume->id,
            'ranobe_year_id' => $year->id,
            'title' => 'Chapter 1',
            'chapter_number' => 1,
            'chapter_content' => 'Sync command test',
        ]);

        $this->artisan('publication:sync')->assertSuccessful();

        $this->assertSame(AnimeSeason::STATUS_RELEASED, $season->fresh()->status);
        $this->assertSame(RanobeVolume::STATUS_RELEASED, $volume->fresh()->status);
    }

    private function createEpisode(AnimeSeason $season, int $number, Carbon $appearIn): AnimeEpisode
    {
        return AnimeEpisode::create([
            'season_id' => $season->id,
            'episode_number' => $number,
            'episode_name' => 'Sync command test',
            'completed' => false,
            'opening_start' => -1,
            'appear_in' => $appearIn,
        ]);
    }
}
