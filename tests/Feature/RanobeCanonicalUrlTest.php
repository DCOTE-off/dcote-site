<?php

namespace Tests\Feature;

use App\Models\RanobeChapter;
use App\Models\RanobeVolume;
use App\Models\RanobeYear;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class RanobeCanonicalUrlTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        $year = RanobeYear::create([
            'year_number' => 1,
            'year_readable' => 'Test year',
            'words_quantity' => 0,
            'hours_of_reading' => '0',
            'status' => 'Test',
        ]);
        $volume = RanobeVolume::create([
            'volume_number' => 100.5,
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
            'volume_description' => 'Canonical url test',
        ]);
        RanobeChapter::create([
            'ranobe_volume_id' => $volume->id,
            'ranobe_year_id' => $year->id,
            'title' => 'Chapter 7',
            'title_label' => 'Глава 7',
            'chapter_number' => 7,
            'chapter_content' => 'Текст главы.',
        ]);
    }

    public function test_segments_with_trailing_zeros_redirect_to_canonical(): void
    {
        $this->get('/ranobe/1/100.50/7.0')->assertRedirect('/ranobe/1/100.5/7');
        $this->get('/ranobe/1/100.50')->assertRedirect('/ranobe/1/100.5');
    }

    public function test_leading_zeros_redirect_to_canonical(): void
    {
        $this->get('/ranobe/01/0100.5/07')->assertRedirect('/ranobe/1/100.5/7');
        $this->get('/ranobe/01/0100.5')->assertRedirect('/ranobe/1/100.5');
    }

    public function test_canonical_urls_return_ok(): void
    {
        $this->get('/ranobe/1/100.5/7')->assertOk();
        $this->get('/ranobe/1/100.5')->assertOk();
    }
}
