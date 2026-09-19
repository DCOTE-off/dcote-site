<?php

namespace App\Http\Controllers;

use App\Helpers\DescriptionTextHelper;
use App\Helpers\FilesCollectionHelper;
use App\Helpers\MarkdownRanobeHelper;
use App\Helpers\SeoMeta;
use App\Models\RanobeChapter;
use App\Models\RanobeVolume;
use App\Models\RanobeYear;
use Illuminate\Support\Facades\Storage;
use Inertia\Inertia;

class RanobeController extends Controller
{
    public function index()
    {
        $years_list = RanobeYear::orderBy('year_number', 'asc')
            ->withCount(['volumes', 'chapters'])
            ->withSum('volumes as total_pages', 'pages_quantity')
            ->get();

        return Inertia::render('Ranobe/Years', [
            'years_list' => $years_list,
            'meta' => SeoMeta::make(
                'Читать ранобэ «Класс превосходства» | Все года',
                'Список всех годов ранобэ «Добро пожаловать в класс превосходства». Выбирайте год и приступайте к чтению с хорошим переводом на DCOTE.',
            ),
        ]);
    }

    public function showYear(int $year)
    {
        $yearModel = RanobeYear::where('year_number', $year)->firstOrFail();
        $userId = auth()->id();

        $volumes = $yearModel->volumes()
            ->orderBy('general_number', 'desc')
            ->withCount('chapters')
            ->addSelect(['volume_avg_rating' => function ($query) {
                $query->selectRaw('COALESCE(AVG(rating), 0)')
                    ->from('ratings')
                    ->where('rateable_type', 'ranobe_volume')
                    ->whereColumn('rateable_id', 'ranobe_volumes.id');
            }])
            ->addSelect(['volume_ratings_count' => function ($query) {
                $query->selectRaw('COUNT(*)')
                    ->from('ratings')
                    ->where('rateable_type', 'ranobe_volume')
                    ->whereColumn('rateable_id', 'ranobe_volumes.id');
            }])
            ->addSelect(['volume_user_rating' => function ($query) use ($userId) {
                $query->selectRaw('COALESCE(AVG(rating), 0)')
                    ->from('ratings')
                    ->where('rateable_type', 'ranobe_volume')
                    ->where('user_id', $userId)
                    ->whereColumn('rateable_id', 'ranobe_volumes.id');
            }])
            ->get()
            ->map(fn (RanobeVolume $volume) => [
                'id' => $volume->id,
                'volume_number' => floatval($volume->volume_number),
                'all_chapters' => (int) $volume->all_chapters,
                'chapters_count' => (int) $volume->chapters_count,
                'volume_avg_rating' => round((float) ($volume->volume_avg_rating ?? 0), 1),
                'volume_ratings_count' => (int) ($volume->volume_ratings_count ?? 0),
                'volume_user_rating' => (int) ($volume->volume_user_rating ?? 0),
                'color' => $volume->color,
                'status' => $volume->status,
                'general_number' => $volume->general_number,
                'release_date_book' => russian_date($volume->release_date_book),
                'release_date_digital' => russian_date($volume->release_date_digital),
                'isbn' => $volume->isbn,
                'cover_image' => Storage::url($volume->cover_image),
                'cover_image_mobile' => Storage::url($volume->cover_image_mobile),
            ])
            ->values();

        return Inertia::render('Ranobe/Year', [
            'year' => $year,
            'volumes' => $volumes,
            'meta' => SeoMeta::make(
                "Читать «Класс превосходства» | {$year} год",
                "Список всех томов {$year} года новеллы «Добро пожаловать в класс превосходства». Выбирайте год и приступайте к чтению с высоким качеством перевода на DCOTE.",
            ),
        ]);
    }

    public function showVolume(int $year, float $volume)
    {
        $userId = auth()->id();

        $volumeModel = RanobeVolume::query()
            ->select('id', 'volume_description', 'volume_number', 'cover_image', 'cover_image_mobile', 'promo_link')
            ->addSelect(['volume_avg_rating' => function ($query) {
                $query->selectRaw('COALESCE(AVG(rating), 0)')
                    ->from('ratings')
                    ->where('rateable_type', 'ranobe_volume')
                    ->whereColumn('rateable_id', 'ranobe_volumes.id');
            }])
            ->addSelect(['volume_ratings_count' => function ($query) {
                $query->selectRaw('COUNT(*)')
                    ->from('ratings')
                    ->where('rateable_type', 'ranobe_volume')
                    ->whereColumn('rateable_id', 'ranobe_volumes.id');
            }])
            ->addSelect(['volume_user_rating' => function ($query) use ($userId) {
                $query->selectRaw('COALESCE(AVG(rating), 0)')
                    ->from('ratings')
                    ->where('rateable_type', 'ranobe_volume')
                    ->where('user_id', $userId)
                    ->whereColumn('rateable_id', 'ranobe_volumes.id');
            }])
            ->where('volume_number', $volume)
            ->with(['chapters' => function ($query) {
                $query
                    ->select('id', 'ranobe_volume_id', 'title', 'title_label', 'chapter_number')
                    ->orderBy('chapter_number', 'asc');
            }])
            ->whereHas('year', function ($query) use ($year) {
                $query->where('year_number', $year);
            })
            ->firstOrFail();
        $chapters = $volumeModel->chapters;
        $volume_number_rounded = floatval($volumeModel->volume_number);
        $path = "ranobe/year-$year/volume-$volume_number_rounded/images/";

        $color_images = FilesCollectionHelper::findFiles($path, '-color', 'public');
        $bw_images = FilesCollectionHelper::findFiles($path, '-bw', 'public');

        return Inertia::render('Ranobe/Volume', [
            'year' => $year,
            'volume_number_rounded' => $volume_number_rounded,
            'meta' => SeoMeta::make(
                "Читать ранобэ «Класс превосходства» {$year} год {$volume_number_rounded} том",
                "Читать {$year} год {$volume_number_rounded} том ранобэ «Добро пожаловать в класс превосходства» онлайн. Описание тома, список глав и даты выхода на сайте DCOTE.",
                Storage::url($volumeModel->cover_image),
            ),
            'volume' => [
                'id' => $volumeModel->id,
                'description' => DescriptionTextHelper::normalize(
                    $volumeModel->volume_description,
                ),
                'cover_image' => Storage::url($volumeModel->cover_image),
                'cover_image_mobile' => Storage::url($volumeModel->cover_image_mobile),
                'promo_link' => $volumeModel->promo_link,
                'volume_avg_rating' => round((float) ($volumeModel->volume_avg_rating ?? 0), 1),
                'volume_ratings_count' => (int) ($volumeModel->volume_ratings_count ?? 0),
                'volume_user_rating' => (int) ($volumeModel->volume_user_rating ?? 0),
            ],
            'chapters' => $chapters->map(fn (RanobeChapter $chapter) => [
                'id' => $chapter->id,
                'title' => $chapter->title,
                'title_label' => $chapter->title_label,
                'chapter_number' => floatval($chapter->chapter_number),
            ])->values(),
            'color_images' => $color_images->values(),
            'bw_images' => $bw_images->values(),
        ]);
    }

    public function showChapter(int $year, float $volume, float $chapter)
    {
        $chapterModel = RanobeChapter::query()
            ->where('chapter_number', $chapter)
            ->whereHas('year', fn ($q) => $q->where('year_number', $year))
            ->whereHas('volume', fn ($q) => $q->where('volume_number', $volume))
            ->with('volume')
            ->firstOrFail();

        $prev_chapter = $this->getPreviousChapter($chapterModel);
        $prev_link = $prev_chapter ? route('ranobe.chapter', [
            'year' => $prev_chapter->year->year_number,
            'volume' => floatval($prev_chapter->volume->volume_number),
            'chapter' => floatval($prev_chapter->chapter_number),
        ]) : null;
        $next_chapter = $this->getNextChapter($chapterModel);
        $next_link = $next_chapter ? route('ranobe.chapter', [
            'year' => $next_chapter->year->year_number,
            'volume' => floatval($next_chapter->volume->volume_number),
            'chapter' => floatval($next_chapter->chapter_number),
        ]) : null;
        $content = $chapterModel->chapter_content;
        $htmlContent = MarkdownRanobeHelper::parse($content, $year, $volume);
        $volume_number_rounded = floatval($volume);

        return Inertia::render('Ranobe/Chapter', [
            'year' => $year,
            'volume' => $volume_number_rounded,
            'chapter' => floatval($chapter),
            'titleLabel' => $chapterModel->title_label,
            'title' => $chapterModel->title,
            'contentHtml' => $htmlContent,
            'prevLink' => $prev_link,
            'nextLink' => $next_link,
            'chapterId' => $chapterModel->id,
            'meta' => SeoMeta::make(
                "Читать «Класс превосходства» | {$year} год {$volume_number_rounded} том {$chapter} глава",
                trim("Читать {$chapterModel->title_label} {$chapterModel->title} {$volume_number_rounded} тома новеллы «Добро пожаловать в класс превосходства». Читайте с высоким качеством перевода на DCOTE."),
                Storage::url($chapterModel->volume->cover_image),
            ),
        ]);
    }

    private function getPreviousChapter(RanobeChapter $chapterModel): ?RanobeChapter
    {
        $prev = RanobeChapter::where('ranobe_volume_id', $chapterModel->ranobe_volume_id)
            ->where('chapter_number', '<', $chapterModel->chapter_number)
            ->orderBy('chapter_number', 'desc')
            ->first();

        if ($prev) {
            return $prev;
        }
        $prevVolume = RanobeVolume::where('ranobe_year_id', $chapterModel->ranobe_year_id)
            ->where('general_number', '<', $chapterModel->volume->general_number)
            ->orderBy('general_number', 'desc')
            ->first();

        if ($prevVolume) {
            $prev = RanobeChapter::where('ranobe_volume_id', $prevVolume->id)
                ->orderBy('chapter_number', 'desc')
                ->first();

            if ($prev) {
                return $prev;
            }
        }

        $prevYear = RanobeYear::where('year_number', '<', $chapterModel->year->year_number)
            ->orderBy('year_number', 'desc')
            ->first();

        if ($prevYear) {
            $lastVolumeOfPrevYear = RanobeVolume::where('ranobe_year_id', $prevYear->id)
                ->orderBy('general_number', 'desc')
                ->first();

            if ($lastVolumeOfPrevYear) {
                return RanobeChapter::where('ranobe_volume_id', $lastVolumeOfPrevYear->id)
                    ->orderBy('chapter_number', 'desc')
                    ->first();
            }
        }

        return null;
    }

    private function getNextChapter(RanobeChapter $chapterModel): ?RanobeChapter
    {
        $next = RanobeChapter::where('ranobe_volume_id', $chapterModel->ranobe_volume_id)
            ->where('chapter_number', '>', $chapterModel->chapter_number)
            ->orderBy('chapter_number', 'asc')
            ->first();

        if ($next) {
            return $next;
        }
        $nextVolume = RanobeVolume::where('ranobe_year_id', $chapterModel->ranobe_year_id)
            ->where('general_number', '>', $chapterModel->volume->general_number)
            ->orderBy('general_number', 'asc')
            ->first();

        if ($nextVolume) {
            $next = RanobeChapter::where('ranobe_volume_id', $nextVolume->id)
                ->orderBy('chapter_number', 'asc')
                ->first();

            if ($next) {
                return $next;
            }
        }

        $nextYear = RanobeYear::where('year_number', '>', $chapterModel->year->year_number)
            ->orderBy('year_number', 'asc')
            ->first();

        if ($nextYear) {
            $firstVolumeOfNextYear = RanobeVolume::where('ranobe_year_id', $nextYear->id)
                ->orderBy('general_number', 'asc')
                ->first();

            if ($firstVolumeOfNextYear) {
                return RanobeChapter::where('ranobe_volume_id', $firstVolumeOfNextYear->id)
                    ->orderBy('chapter_number', 'asc')
                    ->first();
            }
        }

        return null;
    }
}
