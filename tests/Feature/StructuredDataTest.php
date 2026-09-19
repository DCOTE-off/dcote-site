<?php

namespace Tests\Feature;

use App\Models\AnimeEpisode;
use App\Models\AnimeSeason;
use App\Models\RanobeChapter;
use App\Models\RanobeVolume;
use App\Models\RanobeYear;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class StructuredDataTest extends TestCase
{
    use RefreshDatabase;

    public function test_home_page_has_site_wide_schema(): void
    {
        $this->get(route('home'))
            ->assertOk()
            ->assertSee('"@type":"Organization"', false)
            ->assertSee('"@type":"WebSite"', false);
    }

    public function test_episode_page_has_video_object_schema(): void
    {
        $season = AnimeSeason::create([
            'season_number' => 1,
            'status' => AnimeSeason::STATUS_RELEASED,
            'number_of_episodes' => 1,
        ]);
        AnimeEpisode::create([
            'season_id' => $season->id,
            'episode_number' => 1,
            'episode_name' => 'Structured data test',
            'completed' => true,
            'opening_start' => -1,
            'appear_in' => now()->subDay(),
        ]);

        $this->get(route('anime.episode', ['season' => 1, 'episode' => 1]))
            ->assertOk()
            ->assertSee('"@type":"VideoObject"', false)
            ->assertSee('"uploadDate"', false);
    }

    public function test_chapter_page_has_article_schema(): void
    {
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
            'status' => RanobeVolume::STATUS_RELEASED,
            'release_date_book' => now(),
            'all_chapters' => 1,
            'release_date_digital' => now(),
            'ranobe_year_id' => $year->id,
            'pages_quantity' => 100,
            'isbn' => 'test-isbn-1000',
            'volume_description' => 'Structured data test',
        ]);
        RanobeChapter::create([
            'ranobe_volume_id' => $volume->id,
            'ranobe_year_id' => $year->id,
            'title' => 'Chapter 1',
            'title_label' => 'Глава 1',
            'chapter_number' => 1,
            'chapter_content' => 'Текст главы.',
        ]);

        $this->get(route('ranobe.chapter', ['year' => 1, 'volume' => 100, 'chapter' => 1]))
            ->assertOk()
            ->assertSee('"@type":"Article"', false);
    }
}
