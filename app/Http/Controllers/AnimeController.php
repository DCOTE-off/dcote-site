<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class AnimeController extends Controller
{
    public function index()
    {
        return view('pages.anime.index');
    }

    public function showSeason(int $season)
    {
        return view('pages.anime.season', compact('season'));
    }

    public function showEpisode(int $season, int $episode)
    {
        return view('pages.anime.episode', compact('season', 'episode'));
    }
}