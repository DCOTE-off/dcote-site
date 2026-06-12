<?php

namespace App\Http\Controllers;
use App\Models\AnimeSeason;
use App\Models\AnimeEpisode;
use Illuminate\Support\Facades\DB;

class AnimeController extends Controller
{
    public function index()
    {
        $seasons_list = AnimeSeason::orderBy('id','desc')->get();
        $season_realesed = AnimeEpisode::select('season_id', DB::raw('COUNT(*) as episode_count'))
            ->whereIn('season_id', [1, 2, 3, 4])
            ->groupBy('season_id')
            ->orderBy('season_id', 'desc')
            ->get();
            
        return view('pages.anime.index',compact('seasons_list','season_realesed'));
    }

    public function showSeason(int $season)
    {
        $seasonModel = AnimeSeason::findOrFail($season);
        $about_season = (object) [
        'season_description' => $seasonModel->season_description,
        'trailer_link'=> $seasonModel->trailer_link
        ];
        $episodes = AnimeEpisode::where('season_id',$season)->orderBy('episode_number','desc')->get();
        return view('pages.anime.season', compact('season','about_season','episodes'));
    }

    public function showEpisode(int $season, int $episode)
    {
        $seasonModel = AnimeSeason::withCount('episodes')->findOrFail($season);
        $total_episodes = $seasonModel->episodes_count ?? 0;

        $episodeModel = AnimeEpisode::where('season_id', $season)
            ->where('episode_number', $episode)
            ->firstOrFail();
        $player_url = "https://video.dcote.net/metrics-api/videoplayer";
        $episodeNumBeaty = str_pad((string) $episode, 2, '0', STR_PAD_LEFT);
        $videoBaseUrl = "https://video.dcote.net/season-0{$season}/episode-{$episodeNumBeaty}";
        $episodeUrl = $player_url . '?' . http_build_query([
            'src' => "{$videoBaseUrl}/master.m3u8",
            'poster' => "https://video.dcote.net/season-0{$season}/banner.webp",
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




        return view('pages.anime.episode', compact('season', 'episode','episodeUrl','total_episodes','completed','next_link','prev_link'));
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
