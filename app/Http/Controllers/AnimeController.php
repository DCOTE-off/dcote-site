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
        return view('pages.anime.season', compact('season'));
    }

    public function showEpisode(int $season, int $episode)
    {
        $seasonModel = AnimeSeason::findOrFail($season);
        $episodeModel = AnimeEpisode::where('season_id', $season)
            ->where('episode_number', $episode)
            ->firstOrFail();
        return view('pages.anime.episode', compact('season', 'episode'));
    }
}