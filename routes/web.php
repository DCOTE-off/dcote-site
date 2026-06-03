<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AnimeController;
use App\Http\Controllers\MainController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\RanobeController;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Auth;

Route::get('/', [MainController::class, 'index'])->name('home');

Route::prefix('auth')->group(function () {
    Route::get('/register', [AuthController::class, 'register'])->name('register');
    Route::get('/login', [AuthController::class, 'login'])->name('login');
    Route::post('/register', [AuthController::class, 'store']);
    Route::post('/login', [AuthController::class, 'authenticate']);
    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');
});


Route::prefix('anime')->group(function () {
    Route::get('/', [AnimeController::class, 'index'])->name('anime.index');
    Route::get('/{season}', [AnimeController::class, 'showSeason'])->where('season', '[0-9]+')->name('anime.season');
    Route::get('/{season}/{episode}', [AnimeController::class, 'showEpisode'])->where('season', '[0-9]+')->where('episode','[0-9]+')->name('anime.episode');
});

Route::prefix('ranobe')->group(function () {
    Route::get('/', [RanobeController::class, 'index'])->name('ranobe.index');
    Route::get('/{year}',[RanobeController::class, 'showYear'])->where('year','[0-9]+')->name('ranobe.year');
    Route::get('/{year}/{volume}',[RanobeController::class, 'showVolume'])->where('year','[0-9]+')->where('volume', '[0-9]+(\.[0-9]+)?')->name('ranobe.volume');
    Route::get('/{year}/{volume}/{chapter}',[RanobeController::class, 'showChapter'])->where('year','[0-9]+')->where('volume','[0-9]+(\.[0-9]+)?')->where('chapter','[0-9]+(\.[0-9]+)?')->name('ranobe.chapter');
});

Route::get('/about-project', function () {
    return view('pages.about-project');
})->name('about-project');

Route::get('/about-school', function () {
    return view('pages.about-school');
})->name('about-school');

Route::get('/rules', function () {
    return view('pages.rules');
})->name('rules');


Route::prefix('manga')->group(function () {
    Route::get('/', function () {
        return view('pages.manga.index');
    })->name('manga.index');
    Route::get('/{id}', function () {
        return view('pages.manga.show');
    })->name('manga.show');
});

Route::prefix('illustrations')->group(function () {
    Route::get('/', function () {
        return view('pages.illustrations.index');
    })->name('illustrations.index');
    Route::get('/{id}', function () {
        return view('pages.illustrations.show');
    })->name('illustrations.show');
});

Route::prefix('characters')->group(function () {
    Route::get('/', function () {
        return view('pages.characters.index');
    })->name('characters.index');
    Route::get('/{id}', function () {
        return view('pages.characters.show');
    })->name('characters.show');
});

Route::get('/news', function () {
    return view('pages.news.index');
})->name('news.index');


Route::get('/privacy_policy', function () {
    return view('pages.privacy');
})->name('privacy_policy');

Route::get('/settings', function () {
    return view('pages.settings');
})->name('settings');

Route::get('/logout', function () {
    return view('pages.logout');
})->name('logout');

Route::get('/favorite', function () {
    return view('pages.favorite');
})->name('favorite');

Route::get('/account', function () {
    return view('pages.account');
})->name('account');
