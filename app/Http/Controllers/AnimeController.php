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
        $about_season = AnimeSeason::select('season_description','trailer_link')->where('season_number',$season)->first();
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
            $has_dub = $episodeModel->has_dub;
            $has_sub = $episodeModel->has_sub;

            $initial_type = $has_dub ? 'dub' : ($has_sub ? 'sub' : null);
            $voice = $episodeModel->has_anilibria === 1 ? 'AniLibria' : 'Anistar';

            $player_url = "https://video.dcote.net/metrics-api/videoplayer";
                             
            $poster_url = "poster=https://video.dcote.net/season-{$season}/episodes-banner-season{$season}.webp";
            $skip_start = "skip_start=" . ($episodeModel->opening_start ?? '-1');

            $dubUrl = null;
            if ($has_dub) {
                $dubUrl = "{$player_url}?src=https://video.dcote.net/season-{$season}/dub/episode-{$episode}/{$voice}/master.m3u8&{$poster_url}&{$skip_start}";
            }

            $subUrl = null;
            if ($has_sub) {
                $subUrl = "{$player_url}?src=https://video.dcote.net/season-{$season}/sub/episode-{$episode}/master.m3u8&{$poster_url}&{$skip_start}";
            }

        return view('pages.anime.episode', compact('season', 'episode','dubUrl','subUrl','initial_type','total_episodes','has_dub','has_sub'));
    }
}