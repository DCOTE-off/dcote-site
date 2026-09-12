<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AnimeController;
use App\Http\Controllers\MainController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\RanobeController;
use App\Models\Role;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;

Route::get('/', [MainController::class, 'index'])->name('home');


Route::get('/sitemap.xml', function () {
    return response()
        ->file(public_path('sitemap.xml'), [
            'Content-Type' => 'application/xml'
        ]);
});




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
    $departmentUsers = Role::query()
        ->whereIn('name', ['Редактор', 'Модератор'])
        ->with(['users' => fn ($query) => $query
            ->select(['id', 'nickname', 'avatar', 'role_id'])
            ->orderBy('nickname')])
        ->get(['id', 'name'])
        ->keyBy('name');

    $members = fn ($users) => collect($users)
        ->map(fn ($user) => [
            'nickname' => $user->nickname,
            'avatar_url' => $user->avatar_url,
        ])
        ->values();

    return Inertia::render('AboutProject', [
        'editors' => $members($departmentUsers->get('Редактор')?->users),
        'moderators' => $members($departmentUsers->get('Модератор')?->users),
        'meta' => \App\Helpers\SeoMeta::make(
            'О проекте | Наша команда',
            'Познакомьтесь с командой DCOTE: разработчиками, дизайнерами и редакторами, которые создают проект о «Добро пожаловать в класс превосходства».',
        ),
    ]);
})->name('about-project');

Route::get('/about-school', function () {
    return Inertia::render('AboutSchool', [
        'meta' => \App\Helpers\SeoMeta::make(
            'О школе Кодо Икусэй',
            'Познакомьтесь со школой Кодо Икусэй произведения «Добро пожаловать в класс превосходства». Правила для учеников, школьная униформа, магазины, общежития и места для досуга. Узнайте об этом на сайте DCOTE',
        ),
    ]);
})->name('about-school');

Route::get('/rules', function () {
    return Inertia::render('Rules', [
        'meta' => \App\Helpers\SeoMeta::make(
            'Правила сайта',
            'Ознакомьтесь с правилами сайта DCOTE.',
        ),
    ]);
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
    return Inertia::render('PrivacyPolicy', [
        'meta' => \App\Helpers\SeoMeta::make(
            'Политика конфиденциальности',
            'Политика конфиденциальности сайта DCOTE.',
        ),
    ]);
})->name('privacy_policy');

Route::get('/components', function () {
    return view('pages.components');
})->name('pr');

Route::get('/settings', function () {
    return view('errors.404');
})->name('settings');


Route::get('/favorite', function () {
    return view('pages.favorite');
})->name('favorite');

Route::get('/account', function () {
    return view('pages.account');
})->name('account');

Route::get('/comments', function () {
    return Inertia::render('Comments', [
        'meta' => \App\Helpers\SeoMeta::make(
            'Комментарии',
            null,
            null,
            ['robots' => 'noindex, nofollow'],
        ),
    ]);
})->name('comments');

Route::fallback(function () {
    return Inertia::render('Errors/Error', [
        'status' => 404,
        'meta' => \App\Helpers\SeoMeta::make(
            'Ошибка 404',
            null,
            null,
            ['robots' => 'noindex, nofollow'],
        ),
    ])->toResponse(request())->setStatusCode(404);
});
