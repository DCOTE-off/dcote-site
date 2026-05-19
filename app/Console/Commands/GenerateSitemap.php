<?php
namespace App\Console\Commands;

use Illuminate\Console\Command;
use Spatie\Sitemap\Sitemap;
use Spatie\Sitemap\Tags\Url;

use App\Models\AnimeSeason;
use App\Models\AnimeEpisode;
use App\Models\RanobeYear;
use App\Models\RanobeVolume;
use App\Models\RanobeChapter;

class GenerateSitemap extends Command
{
    protected $signature = 'sitemap:generate';
    protected $description = 'Генерация XML-карты сайта на основе актуальных роутов';

    public function handle()
    {
        $sitemap = Sitemap::create();

        $sitemap->add(Url::create(route('home'))
            ->setPriority(1.0)
            ->setChangeFrequency(Url::CHANGE_FREQUENCY_DAILY));


        $sitemap->add(Url::create(route('about-project'))
            ->setPriority(0.5)
            ->setChangeFrequency(Url::CHANGE_FREQUENCY_MONTHLY));

        $sitemap->add(Url::create(route('rules'))
            ->setPriority(0.3)
            ->setChangeFrequency(Url::CHANGE_FREQUENCY_MONTHLY));


        $sitemap->add(Url::create(route('anime.index'))
            ->setPriority(0.9)
            ->setChangeFrequency(Url::CHANGE_FREQUENCY_DAILY));

        AnimeSeason::chunk(50, function ($seasons) use ($sitemap) {
            foreach ($seasons as $season) {
                $sitemap->add(Url::create(route('anime.season', ['season' => $season->season_number]))
                    ->setPriority(0.8)
                    ->setChangeFrequency(Url::CHANGE_FREQUENCY_WEEKLY));
            }
        });

        AnimeEpisode::with('season')->chunk(200, function ($episodes) use ($sitemap) {
            foreach ($episodes as $episode) {
                $sitemap->add(Url::create(route('anime.episode', [
                    'season' => $episode->season->season_number,
                    'episode' => $episode->episode_number
                ]))
                ->setPriority(0.7)
                ->setChangeFrequency(Url::CHANGE_FREQUENCY_NEVER));
            }
        });


        $sitemap->add(Url::create(route('ranobe.index'))
            ->setPriority(0.9)
            ->setChangeFrequency(Url::CHANGE_FREQUENCY_DAILY));

        RanobeYear::chunk(50, function ($years) use ($sitemap) {
            foreach ($years as $year) {
                $sitemap->add(Url::create(route('ranobe.year', ['year' => $year->year_number]))
                    ->setPriority(0.7)
                    ->setChangeFrequency(Url::CHANGE_FREQUENCY_WEEKLY));
            }
        });

        RanobeVolume::with('year')->chunk(100, function ($volumes) use ($sitemap) {
            foreach ($volumes as $volume) {
                $volumeNum = floatval($volume->volume_number);

                $sitemap->add(Url::create(route('ranobe.volume', [
                    'year' => $volume->year->year_number,
                    'volume' => $volumeNum
                ]))
                ->setPriority(0.8)
                ->setChangeFrequency(Url::CHANGE_FREQUENCY_WEEKLY));
            }
        });

        RanobeChapter::with(['volume', 'year'])->chunk(500, function ($chapters) use ($sitemap) {
            foreach ($chapters as $chapter) {
                $volumeNum = floatval($chapter->volume->volume_number);
                $chapterNum = floatval($chapter->chapter_number);

                $sitemap->add(Url::create(route('ranobe.chapter', [
                    'year' => $chapter->year->year_number,
                    'volume' => $volumeNum,
                    'chapter' => $chapterNum
                ]))
                ->setPriority(0.6)
                ->setChangeFrequency(Url::CHANGE_FREQUENCY_NEVER));
            }
        });


        $sitemap->writeToFile(public_path('sitemap.xml'));

        $this->info('Карта сайта sitemap.xml успешно обновлена для Аниме и Ранобэ!');
    }
}