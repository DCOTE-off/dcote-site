<?php

namespace App\Http\Controllers;
use App\Models\AnimeSeason;
use App\Models\AnimeEpisode;
use App\Models\Rating;

class AnimeController extends Controller
{
    public function index()
    {
        $seasons_list = AnimeSeason::orderBy('id', 'desc')
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
            ->get();

        return view('pages.anime.index', compact('seasons_list'));
    }

    public function showSeason(int $season)
    {
        // Таймер перезагружает страницу в момент выхода; этот запрос фиксирует completed в БД.
        AnimeEpisode::releaseDue();

        $seasonModel = AnimeSeason::findOrFail($season);
        $about_season = (object) [
            'season_description' => $seasonModel->season_description,
            'trailer_link' => $seasonModel->trailer_link,
        ];
        $episodes = AnimeEpisode::where('season_id', $season)
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

        return view('pages.anime.season', compact('season', 'about_season', 'episodes', 'userRatings'));
    }

    public function showEpisode(int $season, int $episode)
    {
        AnimeEpisode::releaseDue();

        $seasonModel = AnimeSeason::withCount('episodes')->findOrFail($season);
        $total_episodes = $seasonModel->episodes_count ?? 0;

        $episodeModel = AnimeEpisode::where('season_id', $season)
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
        $player_url = "https://video.dcote.net/metrics-api/videoplayer";
        $episodeNumBeaty = str_pad((string) $episode, 2, '0', STR_PAD_LEFT);
        $videoBaseUrl = "https://video.dcote.net/season-0{$season}/episode-{$episodeNumBeaty}";
        $episodeUrl = $player_url . '?' . http_build_query([
            'src' => "{$videoBaseUrl}/master.m3u8",
            'poster' => asset("images/anime/episodes-banner-season{$season}.webp"),
            'skip_start' => $episodeModel->opening_start ?? '-1',
            'ass' => "{$videoBaseUrl}/subtitles/ru.ass",
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




        return view('pages.anime.episode', compact(
            'season', 'episode', 'episodeUrl', 'total_episodes', 'completed',
            'next_link', 'prev_link', 'episodeModel', 'episodeAvgRating',
            'episodeRatingsCount', 'userRating'
        ));
    }

    private function getPreviousEpisode(AnimeEpisode $episodeModel): ?AnimeEpisode
    {
        $prev = AnimeEpisode::where('season_id', $episodeModel->season_id)
            ->where('episode_number', '<', $episodeModel->episode_number)
            ->orderBy('episode_number', 'desc')
            ->first();

        if ($prev) return $prev; 
        $prevSeason = AnimeSeason::where('season_number', '<', $episodeModel->season->season_number)
            ->orderBy('season_number', 'desc')
            ->first();

        if ($prevSeason) {
            $prev = AnimeEpisode::where('season_id', $prevSeason->id)
                ->orderBy('episode_number', 'desc')
                ->first();
            
            if ($prev) return $prev;
        }
        return null;
    }
    private function getNextEpisode(AnimeEpisode $episodeModel): ?AnimeEpisode
    {
        $next = AnimeEpisode::where('season_id', $episodeModel->season_id)
            ->where('episode_number', '>', $episodeModel->episode_number)
            ->orderBy('episode_number', 'asc')
            ->first();

        if ($next) return $next; 
        $nextSeason = AnimeSeason::where('season_number', '>', $episodeModel->season->season_number)
            ->orderBy('season_number', 'asc')
            ->first();

        if ($nextSeason) {
            $next = AnimeEpisode::where('season_id', $nextSeason->id)
                ->orderBy('episode_number', 'asc')
                ->first();
            
            if ($next) return $next;
        }
        return null;
    }

}
