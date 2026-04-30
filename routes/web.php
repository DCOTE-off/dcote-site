<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AnimeController;

Route::get('/', function () {
    return view('pages.dcote_main');
})->name('home');

Route::get('/login', function () {
    return view('pages.login');
})->name('login');

Route::prefix('anime')->group(function () {
    Route::get('/', [AnimeController::class, 'index'])->name('anime.index');
    Route::get('/{season}', [AnimeController::class, 'showSeason'])->name('anime.season');
    Route::get('/{season}/{episode}', [AnimeController::class, 'showEpisode'])->name('anime.episode');
});