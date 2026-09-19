<?php

use App\Helpers\SeoMeta;
use App\Http\Controllers\AnimeController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\MainController;
use App\Http\Controllers\RanobeController;
use App\Models\Role;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

Route::get('/', [MainController::class, 'index'])->name('home');

Route::get('/sitemap.xml', function () {
    $path = storage_path('app/sitemap.xml');

    abort_unless(is_file($path), 404);

    return response()->file($path, [
        'Content-Type' => 'application/xml',
        'Cache-Control' => 'public, max-age=3600',
    ]);
});

Route::prefix('auth')->group(function () {
    Route::get('/register', [AuthController::class, 'register'])->name('register');
    Route::get('/login', [AuthController::class, 'login'])->name('login');
    Route::post('/register', [AuthController::class, 'store']);
    Route::post('/login', [AuthController::class, 'authenticate']);
    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');
});

Route::prefix('anime')->middleware('canonical-numeric:season,episode')->group(function () {
    Route::get('/', [AnimeController::class, 'index'])->name('anime.index');
    Route::get('/{season}', [AnimeController::class, 'showSeason'])->where('season', '[0-9]+')->name('anime.season');
    Route::get('/{season}/{episode}', [AnimeController::class, 'showEpisode'])->where('season', '[0-9]+')->where('episode', '[0-9]+')->name('anime.episode');
});

Route::prefix('ranobe')->middleware('canonical-numeric:year,volume,chapter')->group(function () {
    Route::get('/', [RanobeController::class, 'index'])->name('ranobe.index');
    Route::get('/{year}', [RanobeController::class, 'showYear'])->where('year', '[0-9]+')->name('ranobe.year');
    Route::get('/{year}/{volume}', [RanobeController::class, 'showVolume'])->where('year', '[0-9]+')->where('volume', '[0-9]+(\.[0-9]+)?')->name('ranobe.volume');
    Route::get('/{year}/{volume}/{chapter}', [RanobeController::class, 'showChapter'])->where('year', '[0-9]+')->where('volume', '[0-9]+(\.[0-9]+)?')->where('chapter', '[0-9]+(\.[0-9]+)?')->name('ranobe.chapter');
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
        'meta' => SeoMeta::make(
            'О проекте | Наша команда',
            'Познакомьтесь с командой DCOTE: разработчиками, дизайнерами и редакторами, которые создают проект о «Добро пожаловать в класс превосходства».',
        ),
    ]);
})->name('about-project');

Route::get('/about-school', function () {
    return Inertia::render('AboutSchool', [
        'meta' => SeoMeta::make(
            'О школе Кодо Икусэй',
            'Познакомьтесь со школой Кодо Икусэй произведения «Добро пожаловать в класс превосходства». Правила для учеников, школьная униформа, магазины, общежития и места для досуга. Узнайте об этом на сайте DCOTE',
        ),
    ]);
})->name('about-school');

Route::get('/rules', function () {
    return Inertia::render('Rules', [
        'meta' => SeoMeta::make(
            'Правила сайта',
            'Ознакомьтесь с правилами сайта DCOTE.',
        ),
    ]);
})->name('rules');

Route::get('/privacy_policy', function () {
    return Inertia::render('PrivacyPolicy', [
        'meta' => SeoMeta::make(
            'Политика конфиденциальности',
            'Политика конфиденциальности сайта DCOTE.',
        ),
    ]);
})->name('privacy_policy');

// Разделы ещё не готовы, но на них ведут ссылки из меню аккаунта и футера.
// До реализации отдаём заглушку, чтобы не было 500.
Route::get('/account', function () {
    return Inertia::render('ComingSoon', [
        'title' => 'ПРОФИЛЬ',
        'meta' => SeoMeta::make(
            'Профиль',
            null,
            null,
            ['robots' => 'noindex, nofollow'],
        ),
    ]);
})->name('account');

Route::get('/favorite', function () {
    return Inertia::render('ComingSoon', [
        'title' => 'ИЗБРАННОЕ',
        'meta' => SeoMeta::make(
            'Избранное',
            null,
            null,
            ['robots' => 'noindex, nofollow'],
        ),
    ]);
})->name('favorite');

Route::get('/notifications', function () {
    return Inertia::render('ComingSoon', [
        'title' => 'ОПОВЕЩЕНИЯ',
        'meta' => SeoMeta::make(
            'Оповещения',
            null,
            null,
            ['robots' => 'noindex, nofollow'],
        ),
    ]);
})->name('notifications');

Route::get('/settings', function () {
    return Inertia::render('ComingSoon', [
        'title' => 'НАСТРОЙКИ',
        'meta' => SeoMeta::make(
            'Настройки',
            null,
            null,
            ['robots' => 'noindex, nofollow'],
        ),
    ]);
})->name('settings');

Route::fallback(function () {
    return Inertia::render('Errors/Error', [
        'status' => 404,
        'meta' => SeoMeta::make(
            'Ошибка 404',
            null,
            null,
            ['robots' => 'noindex, nofollow'],
        ),
    ])->toResponse(request())->setStatusCode(404);
});
