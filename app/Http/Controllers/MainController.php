<?php

namespace App\Http\Controllers;

use App\Helpers\SeoMeta;
use App\Models\AnimeSeason;
use App\Models\ClassesTop;
use App\Models\Popular;
use App\Models\RanobeVolume;
use App\Models\UpdateFeed;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Storage;
use Inertia\Inertia;

class MainController extends Controller
{
    public function index()
    {
        $classes_list_default = ClassesTop::where('spoilers', 0)
            ->orderBy('class_points', 'desc')
            ->get();

        $max_points_default = $classes_list_default->first()?->class_points ?? 0;

        $classes_list_default->transform(function ($class) use ($max_points_default) {
            $class->percent = ($max_points_default > 0)
                ? min(100, round(($class->class_points / $max_points_default) * 100, 2))
                : 0;

            return $class;
        });

        $classes_list_spoilers = ClassesTop::where('spoilers', 1)
            ->orderBy('class_points', 'desc')
            ->get();

        $max_points_spoilers = $classes_list_spoilers->first()?->class_points ?? 0;

        $classes_list_spoilers->transform(function ($class) use ($max_points_spoilers) {
            $class->percent = ($max_points_spoilers > 0)
                ? min(100, round(($class->class_points / $max_points_spoilers) * 100, 2))
                : 0;

            return $class;
        });

        $popularTargets = Popular::query()
            ->where('is_active', true)
            ->orderBy('sort_order')
            ->orderBy('id')
            ->with(['target' => function (MorphTo $target) {
                $target
                    ->morphWith([
                        RanobeVolume::class => ['year'],
                    ])
                    ->morphWithCount([
                        AnimeSeason::class => ['releasedEpisodes'],
                        RanobeVolume::class => ['chapters'],
                    ]);
            }])
            ->get()
            ->pluck('target')
            ->reject(fn ($target) => $target instanceof AnimeSeason && $target->isAnnounced())
            ->filter();

        if ($popularTargets->isEmpty()) {
            $fallbackSeason = AnimeSeason::where('season_number', 4)->first();
            $popularTargets = $fallbackSeason ? collect([$fallbackSeason]) : collect();
        }

        $popularCards = $this->makePopularCards($popularTargets);

        $feed = UpdateFeed::orderBy('created_at', 'desc')
            ->limit(15)
            ->get();

        return Inertia::render('Home', [
            'meta' => SeoMeta::make(
                'Вики, новости и контент по «Классу Превосходства»',
            ),
            'classes_list_default' => $classes_list_default,
            'max_points_default' => $max_points_default,
            'classes_list_spoilers' => $classes_list_spoilers,
            'max_points_spoilers' => $max_points_spoilers,
            'popularCards' => $popularCards,
            'feed' => $feed,
        ]);
    }

    private function makePopularCards(Collection $targets): Collection
    {
        return $targets
            ->map(function (AnimeSeason|RanobeVolume $target): array {
                return $target instanceof AnimeSeason
                    ? $this->makeAnimePopularCard($target)
                    : $this->makeRanobePopularCard($target);
            })
            ->values();
    }

    private function makeAnimePopularCard(AnimeSeason $season): array
    {
        $released = (int) $season->released_episodes_count;
        $total = (int) $season->number_of_episodes;
        $mobileImage = "/images/anime/anime-banner-season-{$season->season_number}-mobile.webp";

        return [
            'category' => 'АНИМЕ',
            'title' => "{$season->season_number} СЕЗОН",
            'image' => file_exists(public_path($mobileImage))
                ? $mobileImage
                : ($season->img_src ?: "/images/anime/anime-banner-season-{$season->season_number}.webp"),
            'alt' => "Обложка аниме, {$season->season_number} сезон",
            'info' => [
                ['label' => 'Статус сериала:', 'value' => $season->status, 'class' => $season->color],
                ['label' => 'Сезон выпуска:', 'value' => $season->season_time],
                ['label' => 'День релиза:', 'value' => $season->release_time],
                ['label' => 'Экранизируемые тома:', 'value' => trim("{$season->adapt_volumes} {$season->adapt_volumes_brackets}")],
            ],
            'progress_label' => 'Выпущено',
            'progress_current' => $released,
            'progress_total' => $total,
            'progress_unit' => 'серий',
            'progress_percent' => $this->calculateProgress($released, $total),
            'url' => route('anime.season', ['season' => $season->season_number]),
            'button_label' => 'СТРАНИЦА СЕЗОНА',
        ];
    }

    private function makeRanobePopularCard(RanobeVolume $volume): array
    {
        $released = (int) $volume->chapters_count;
        $total = (int) $volume->all_chapters;
        $year = (int) $volume->year?->year_number;
        $volumeNumber = (float) $volume->volume_number;

        return [
            'category' => 'РАНОБЭ',
            'title' => "{$year} ГОД, {$volumeNumber} ТОМ",
            'image' => $volume->cover_image_mobile
                ? Storage::url($volume->cover_image_mobile)
                : ($volume->cover_image ? Storage::url($volume->cover_image) : '/images/default-cover.webp'),
            'alt' => "Обложка {$volumeNumber} тома {$year} года",
            'info' => [
                ['label' => 'Статус издания:', 'value' => $volume->status, 'class' => $volume->color],
                ['label' => 'Общая нумерация:', 'value' => $volume->general_number],
                ['label' => 'Дата выхода (книга):', 'value' => russian_date((string) $volume->release_date_book)],
                ['label' => 'Дата выхода (цифра):', 'value' => russian_date((string) $volume->release_date_digital)],
            ],
            'progress_label' => 'Переведено',
            'progress_current' => $released,
            'progress_total' => $total,
            'progress_unit' => 'глав',
            'progress_percent' => $this->calculateProgress($released, $total),
            'url' => route('ranobe.volume', ['year' => $year, 'volume' => $volumeNumber]),
            'button_label' => 'СТРАНИЦА ТОМА',
        ];
    }

    private function calculateProgress(int $current, int $total): float
    {
        return $total > 0 ? min(100, round(($current / $total) * 100, 2)) : 0;
    }
}
