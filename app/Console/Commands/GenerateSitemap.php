<?php

namespace App\Console\Commands;

use App\Models\AnimeEpisode;
use App\Models\AnimeSeason;
use App\Models\RanobeChapter;
use App\Models\RanobeVolume;
use App\Models\RanobeYear;
use DateTimeInterface;
use Illuminate\Console\Command;
use Spatie\Sitemap\Sitemap;
use Spatie\Sitemap\Tags\Url;

class GenerateSitemap extends Command
{
    protected $signature = 'sitemap:generate';

    protected $description = 'Генерация XML-карты сайта на основе актуальных роутов';

    public function handle(): int
    {
        $sitemap = Sitemap::create();

        $this->addStaticPages($sitemap);

        AnimeSeason::notAnnounced()->chunk(50, function ($seasons) use ($sitemap) {
            foreach ($seasons as $season) {
                $this->add($sitemap, route('anime.season', ['season' => $season->season_number]));
            }
        });

        AnimeEpisode::with('season')
            ->whereHas('season', fn ($query) => $query->notAnnounced())
            ->chunk(200, function ($episodes) use ($sitemap) {
                foreach ($episodes as $episode) {
                    $this->add(
                        $sitemap,
                        route('anime.episode', [
                            'season' => $episode->season->season_number,
                            'episode' => $episode->episode_number,
                        ]),
                        $episode->appear_in,
                    );
                }
            });

        RanobeYear::chunk(50, function ($years) use ($sitemap) {
            foreach ($years as $year) {
                $this->add($sitemap, route('ranobe.year', ['year' => $year->year_number]));
            }
        });

        RanobeVolume::with('year')->chunk(100, function ($volumes) use ($sitemap) {
            foreach ($volumes as $volume) {
                $this->add(
                    $sitemap,
                    route('ranobe.volume', [
                        'year' => $volume->year->year_number,
                        'volume' => floatval($volume->volume_number),
                    ]),
                    $volume->updated_at,
                );
            }
        });

        RanobeChapter::with(['volume', 'year'])->chunk(500, function ($chapters) use ($sitemap) {
            foreach ($chapters as $chapter) {
                $this->add(
                    $sitemap,
                    route('ranobe.chapter', [
                        'year' => $chapter->year->year_number,
                        'volume' => floatval($chapter->volume->volume_number),
                        'chapter' => floatval($chapter->chapter_number),
                    ]),
                    $chapter->updated_at,
                );
            }
        });

        // Пишем в storage (общий volume), а не в public: public принадлежит root
        // и не переживает пересборку контейнера. Роут /sitemap.xml отдаёт файл.
        $sitemap->writeToFile(storage_path('app/sitemap.xml'));

        $this->info('Карта сайта sitemap.xml успешно обновлена.');

        return self::SUCCESS;
    }

    private function addStaticPages(Sitemap $sitemap): void
    {
        $this->add($sitemap, route('home'));
        $this->add($sitemap, route('about-project'));
        $this->add($sitemap, route('about-school'));
        $this->add($sitemap, route('rules'));
        $this->add($sitemap, route('privacy_policy'));
        $this->add($sitemap, route('anime.index'));
        $this->add($sitemap, route('ranobe.index'));
    }

    /**
     * Ссылки добавляем без priority/changefreq: поисковые системы их игнорируют,
     * а spatie/laravel-sitemap 7.x их и не рендерит. lastmod задаём только там,
     * где есть достоверная дата изменения — иначе поисковик перестанет ему верить.
     */
    private function add(Sitemap $sitemap, string $url, ?DateTimeInterface $lastModified = null): void
    {
        $tag = Url::create($url);

        if ($lastModified !== null) {
            $tag->setLastModificationDate($lastModified);
        }

        $sitemap->add($tag);
    }
}
