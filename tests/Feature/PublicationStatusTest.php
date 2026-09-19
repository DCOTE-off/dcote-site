<?php

namespace Tests\Feature;

use App\Models\AnimeEpisode;
use App\Models\AnimeSeason;
use App\Models\RanobeChapter;
use App\Models\RanobeVolume;
use App\Models\RanobeYear;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PublicationStatusTest extends TestCase
{
    use RefreshDatabase;

    public function test_anime_index_marks_ongoing_season_as_released_when_release_progress_is_full(): void
    {
        $finishedSeason = AnimeSeason::create([
            'season_number' => 1,
            'status' => AnimeSeason::STATUS_ONGOING,
            'number_of_episodes' => 2,
        ]);
        $ongoingSeason = AnimeSeason::create([
            'season_number' => 2,
            'status' => AnimeSeason::STATUS_ONGOING,
            'number_of_episodes' => 2,
        ]);

        $this->createEpisode($finishedSeason, 1, true);
        $this->createEpisode($finishedSeason, 2, true);
        $this->createEpisode($ongoingSeason, 1, true);
        $this->createEpisode($ongoingSeason, 2, false);

        // Синхронизация теперь по расписанию, а не при заходе на страницу.
        $this->artisan('publication:sync')->assertSuccessful();

        $response = $this->get(route('anime.index'));

        $response->assertOk();

        $seasons = collect($response->viewData('page')['props']['seasons_list']);

        $this->assertSame(AnimeSeason::STATUS_RELEASED, $finishedSeason->fresh()->status);
        $this->assertSame(
            AnimeSeason::STATUS_RELEASED,
            $seasons->firstWhere('season_number', $finishedSeason->season_number)['status'],
        );
        $this->assertSame(AnimeSeason::STATUS_ONGOING, $ongoingSeason->fresh()->status);
    }

    public function test_announced_anime_season_routes_are_not_available(): void
    {
        $season = AnimeSeason::create([
            'season_number' => 1,
            'status' => AnimeSeason::STATUS_ANNOUNCED,
            'number_of_episodes' => 12,
        ]);

        $this->get(route('anime.season', ['season' => $season->season_number]))
            ->assertNotFound();
        $this->get(route('anime.episode', ['season' => $season->season_number, 'episode' => 1]))
            ->assertNotFound();
    }

    public function test_ranobe_year_marks_ongoing_volume_as_released_when_translation_progress_is_full(): void
    {
        $year = RanobeYear::create([
            'year_number' => 1,
            'year_readable' => 'Test year',
            'words_quantity' => 0,
            'hours_of_reading' => '0',
            'status' => 'Test',
        ]);
        $finishedVolume = $this->createVolume($year, 100.0, 1000, 2);
        $ongoingVolume = $this->createVolume($year, 101.0, 1001, 2);

        $this->createChapter($year, $finishedVolume, 1);
        $this->createChapter($year, $finishedVolume, 2);
        $this->createChapter($year, $ongoingVolume, 1);

        // Синхронизация теперь по расписанию, а не при заходе на страницу.
        $this->artisan('publication:sync')->assertSuccessful();

        $response = $this->get(route('ranobe.year', ['year' => $year->year_number]));

        $response->assertOk();

        $volumes = collect($response->viewData('page')['props']['volumes']);

        $this->assertSame(RanobeVolume::STATUS_RELEASED, $finishedVolume->fresh()->status);
        $this->assertSame(
            RanobeVolume::STATUS_RELEASED,
            $volumes->firstWhere('id', $finishedVolume->id)['status'],
        );
        $this->assertSame(RanobeVolume::STATUS_ONGOING, $ongoingVolume->fresh()->status);
    }

    private function createEpisode(AnimeSeason $season, int $episodeNumber, bool $completed): AnimeEpisode
    {
        return AnimeEpisode::create([
            'season_id' => $season->id,
            'episode_number' => $episodeNumber,
            'episode_name' => 'Status progress test',
            'completed' => $completed,
            'opening_start' => -1,
        ]);
    }

    private function createVolume(RanobeYear $year, float $volumeNumber, int $generalNumber, int $allChapters): RanobeVolume
    {
        return RanobeVolume::create([
            'volume_number' => $volumeNumber,
            'general_number' => $generalNumber,
            'cover_image' => 'ranobe/test-cover.webp',
            'cover_image_mobile' => 'ranobe/test-cover-mobile.webp',
            'status' => RanobeVolume::STATUS_ONGOING,
            'release_date_book' => now(),
            'all_chapters' => $allChapters,
            'release_date_digital' => now(),
            'ranobe_year_id' => $year->id,
            'pages_quantity' => 100,
            'isbn' => 'test-isbn-'.$generalNumber,
            'volume_description' => 'Status progress test',
        ]);
    }

    private function createChapter(RanobeYear $year, RanobeVolume $volume, int $chapterNumber): RanobeChapter
    {
        return RanobeChapter::create([
            'ranobe_volume_id' => $volume->id,
            'ranobe_year_id' => $year->id,
            'title' => 'Chapter '.$chapterNumber,
            'chapter_number' => $chapterNumber,
            'chapter_content' => 'Status progress test',
        ]);
    }
}
