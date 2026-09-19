<?php

namespace App\Http\Controllers;

use App\Helpers\DescriptionTextHelper;
use App\Helpers\SeoMeta;
use App\Models\AnimeEpisode;
use App\Models\AnimeSeason;
use App\Models\Comment;
use App\Models\Rating;
use Inertia\Inertia;

class AnimeController extends Controller
{
    public function index()
    {
        $seasons_list = AnimeSeason::orderBy('season_number', 'desc')
            ->withCount('releasedEpisodes')
            ->addSelect(['season_avg_rating' => function ($query) {
                $query->selectRaw('COALESCE(AVG(rating), 0)')
                    ->from('ratings')
                    ->where('rateable_type', 'anime_episode')
                    ->whereIn('rateable_id', function ($q) {
                        $q->select('id')
                            ->from('anime_episodes')
                            ->whereColumn('season_id', 'anime_seasons.id');
                    });
            }])
            ->addSelect(['season_ratings_count' => function ($query) {
                $query->selectRaw('COUNT(*)')
                    ->from('ratings')
                    ->where('rateable_type', 'anime_episode')
                    ->whereIn('rateable_id', function ($q) {
                        $q->select('id')
                            ->from('anime_episodes')
                            ->whereColumn('season_id', 'anime_seasons.id');
                    });
            }])
            ->get()
            ->map(fn (AnimeSeason $season) => [
                'season_number' => (int) $season->season_number,
                'number_of_episodes' => (int) $season->number_of_episodes,
                'released_episodes_count' => (int) $season->released_episodes_count,
                'season_avg_rating' => round((float) ($season->season_avg_rating ?? 0), 1),
                'season_ratings_count' => (int) ($season->season_ratings_count ?? 0),
                'color' => $season->color,
                'status' => $season->status,
                'season_time' => $season->season_time,
                'release_time' => $season->release_time,
                'studio' => $season->studio,
                'adapt_volumes' => $season->adapt_volumes,
                'adapt_volumes_brackets' => $season->adapt_volumes_brackets,
                'img_src' => $season->img_src,
                'is_announced' => $season->isAnnounced(),
            ])
            ->values();

        return Inertia::render('Anime/Seasons', [
            'seasons_list' => $seasons_list,
            'meta' => SeoMeta::make(
                'Смотреть аниме «Класс превосходства» | Все сезоны',
                'Список всех сезонов и серий аниме «Добро пожаловать в класс превосходства». Выбирайте сезон и приступайте к просмотру в высоком качестве на DCOTE.',
            ),
        ]);
    }

    public function showSeason(int $season)
    {
        $seasonModel = AnimeSeason::where('season_number', $season)->firstOrFail();
        abort_if($seasonModel->isAnnounced(), 404);

        $season = (int) $seasonModel->season_number;
        $showReleaseSchedule = $seasonModel->isOngoing();
        $about_season = (object) [
            'season_description' => $seasonModel->season_description,
            'trailer_link' => $seasonModel->trailer_link,
        ];
        $episodes = AnimeEpisode::where('season_id', $seasonModel->id)
            ->withAvg('ratings as avg_rating', 'rating')
            ->withCount('ratings as ratings_count')
            ->orderBy('episode_number', 'desc')
            ->get();

        $userRatings = collect();
        if (auth()->check()) {
            $userRatings = Rating::where('user_id', auth()->id())
                ->where('rateable_type', 'anime_episode')
                ->whereIn('rateable_id', $episodes->pluck('id'))
                ->get()
                ->keyBy('rateable_id');
        }

        $commentCounts = Comment::query()
            ->withTrashed()
            ->where('commentable_type', 'anime_episode')
            ->whereIn('commentable_id', $episodes->pluck('id'))
            ->selectRaw('commentable_id, COUNT(*) as total')
            ->groupBy('commentable_id')
            ->pluck('total', 'commentable_id');

        $episodes = $episodes->map(function ($episode) use ($userRatings, $commentCounts) {
            $isUpcoming = ! $episode->completed;
            $hasReleaseDate = $isUpcoming && $episode->appear_in?->isFuture();

            return [
                'id' => $episode->id,
                'episode_number' => (int) $episode->episode_number,
                'episode_name' => $episode->episode_name,
                'is_upcoming' => $isUpcoming,
                'appear_at' => $hasReleaseDate ? $episode->appear_in->toIso8601String() : null,
                'avg_rating' => $isUpcoming ? 0 : round((float) ($episode->avg_rating ?? 0), 1),
                'ratings_count' => $isUpcoming ? 0 : (int) ($episode->ratings_count ?? 0),
                'user_rating' => $isUpcoming ? 0 : (int) ($userRatings->get($episode->id)?->rating ?? 0),
                'comments_count' => (int) ($commentCounts[$episode->id] ?? 0),
                'trailer_link' => $episode->trailer_link,
            ];
        })->values();

        return Inertia::render('Anime/Season', [
            'season' => $season,
            'meta' => SeoMeta::make(
                "Аниме «Класс превосходства» {$season} сезон | Список серий",
                "Смотреть {$season} сезон «Добро пожаловать в класс превосходства» онлайн. Описание сезона, список серий и даты выхода на сайте DCOTE.",
                "images/anime/anime-banner-season-{$season}.webp",
            ),
            'about_season' => [
                'season_description' => DescriptionTextHelper::normalize(
                    $seasonModel->season_description,
                ),
                'trailer_link' => $seasonModel->trailer_link,
            ],
            'showReleaseSchedule' => $showReleaseSchedule,
            'server_now' => now()->toIso8601String(),
            'episodes' => $episodes,
            'season_id' => $seasonModel->id,
        ]);
    }

    public function showEpisode(int $season, int $episode)
    {
        $seasonModel = AnimeSeason::where('season_number', $season)
            ->withCount('episodes')
            ->firstOrFail();
        abort_if($seasonModel->isAnnounced(), 404);

        $season = (int) $seasonModel->season_number;
        $total_episodes = $seasonModel->episodes_count ?? 0;

        $episodeModel = AnimeEpisode::where('season_id', $seasonModel->id)
            ->where('episode_number', $episode)
            ->withAvg('ratings as episode_avg_rating', 'rating')
            ->withCount('ratings as episode_ratings_count')
            ->firstOrFail();

        $episodeAvgRating = $episodeModel->episode_avg_rating;
        $episodeRatingsCount = $episodeModel->episode_ratings_count;
        $userRating = auth()->check()
            ? Rating::where('user_id', auth()->id())
                ->where('rateable_type', 'anime_episode')
                ->where('rateable_id', $episodeModel->id)
                ->value('rating') ?? 0
            : 0;
        $videoBaseUrl = (string) config('services.dcote.video_base_url');
        $player_url = "{$videoBaseUrl}/videoplayer";
        $episodeNumBeaty = str_pad((string) $episode, 2, '0', STR_PAD_LEFT);
        $episodeBaseUrl = "{$videoBaseUrl}/season-0{$season}/episode-{$episodeNumBeaty}";
        $episodeUrl = $player_url.'?'.http_build_query([
            'src' => "{$episodeBaseUrl}/master.m3u8",
            'poster' => asset("images/anime/episodes-banner-season{$season}.webp"),
            'skip_start' => $episodeModel->opening_start ?? '-1',
            'ass' => "{$episodeBaseUrl}/subtitles/ru.ass",
            'ass_lang' => 'ru',
        ], '', '&', PHP_QUERY_RFC3986);
        $completed = $episodeModel->completed;

        $prev_episode = $this->getPreviousEpisode($episodeModel);
        $prev_link = $prev_episode ? route('anime.episode', [
            'season' => ($prev_episode->season->season_number),
            'episode' => ($prev_episode->episode_number),
        ]) : null;

        $next_episode = $this->getNextEpisode($episodeModel);
        $next_link = $next_episode ? route('anime.episode', [
            'season' => ($next_episode->season->season_number),
            'episode' => ($next_episode->episode_number),
        ]) : null;

        return Inertia::render('Anime/Episode', [
            'season' => $season,
            'episode' => $episode,
            'episodeId' => $episodeModel->id,
            'episodeUrl' => $episodeUrl,
            'totalEpisodes' => $total_episodes,
            'completed' => $completed,
            'prevLink' => $prev_link,
            'nextLink' => $next_link,
            'episodeAvgRating' => $episodeAvgRating,
            'episodeRatingsCount' => $episodeRatingsCount,
            'episodeUserRating' => $userRating,
            'meta' => SeoMeta::make(
                "«Класс превосходства» {$season} сезон {$episode} серия | Смотреть онлайн",
                "Смотреть онлайн {$episode} серию {$season} сезона аниме «Добро пожаловать в класс превосходства». Видео в хорошем качестве и обсуждение серии на DCOTE.",
                "images/anime/episodes-banner-season{$season}.webp",
            ),
        ]);
    }

    private function getPreviousEpisode(AnimeEpisode $episodeModel): ?AnimeEpisode
    {
        $prev = AnimeEpisode::where('season_id', $episodeModel->season_id)
            ->where('episode_number', '<', $episodeModel->episode_number)
            ->orderBy('episode_number', 'desc')
            ->first();

        if ($prev) {
            return $prev;
        }
        $prevSeason = AnimeSeason::notAnnounced()
            ->where('season_number', '<', $episodeModel->season->season_number)
            ->orderBy('season_number', 'desc')
            ->first();

        if ($prevSeason) {
            $prev = AnimeEpisode::where('season_id', $prevSeason->id)
                ->orderBy('episode_number', 'desc')
                ->first();

            if ($prev) {
                return $prev;
            }
        }

        return null;
    }

    private function getNextEpisode(AnimeEpisode $episodeModel): ?AnimeEpisode
    {
        $next = AnimeEpisode::where('season_id', $episodeModel->season_id)
            ->where('episode_number', '>', $episodeModel->episode_number)
            ->orderBy('episode_number', 'asc')
            ->first();

        if ($next) {
            return $next;
        }
        $nextSeason = AnimeSeason::notAnnounced()
            ->where('season_number', '>', $episodeModel->season->season_number)
            ->orderBy('season_number', 'asc')
            ->first();

        if ($nextSeason) {
            $next = AnimeEpisode::where('season_id', $nextSeason->id)
                ->orderBy('episode_number', 'asc')
                ->first();

            if ($next) {
                return $next;
            }
        }

        return null;
    }
}
