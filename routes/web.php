<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AnimeController;
use App\Http\Controllers\MainController;
use Illuminate\Support\Facades\Artisan;


Route::get('/run-setup/{secret_key}', function ($secret_key) {
    // 1. Защита: проверяем, совпадает ли ключ из URL с ключом в .env
    $expectedKey = env('SETUP_SECRET_KEY');
    
    if (!$expectedKey || $secret_key !== $expectedKey) {
        abort(403, 'Доступ запрещен. Укажите верный ключ.');
    }

    $output = [];

    try {
        // 2. Очистка кэша
        Artisan::call('optimize:clear');
        $output[] = '✓ Кэш и конфигурации успешно очищены.';

        // 3. Применение миграций (флаг --force обязателен для продакшена)
        Artisan::call('migrate', ['--force' => true]);
        $output[] = '✓ Миграции применены.';

    } catch (Exception $e) {
        return response()->json([
            'status' => 'error',
            'message' => $e->getMessage()
        ], 500);
    }

    return response()->json([
        'status' => 'success',
        'logs' => $output
    ]);
});


Route::get('/', [MainController::class, 'index'])->name('home');

Route::get('/login', function () {
    return view('pages.login');
})->name('login');

Route::prefix('anime')->group(function () {
    Route::get('/', [AnimeController::class, 'index'])->name('anime.index');
    Route::get('/{season}', [AnimeController::class, 'showSeason'])->name('anime.season');
    Route::get('/{season}/{episode}', [AnimeController::class, 'showEpisode'])->name('anime.episode');
});

Route::prefix('ranobe')->group(function () {
    Route::get('/', function () {
        return view('pages.ranobe.index');
    })->name('ranobe.index');
    Route::get('/{id}', function () {
        return view('pages.ranobe.show');
    })->name('ranobe.show');
});

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

Route::get('/about-project', function () {
    return view('pages.about-project');
})->name('about-project');

Route::get('/favorite', function () {
    return view('pages.favorite');
})->name('favorite');

Route::get('/account', function () {
    return view('pages.account');
})->name('account');

Route::get('/rules', function () {
    return view('pages.rules');
})->name('rules');

Route::get('/settings', function () {
    return view('pages.settings');
})->name('settings');

Route::get('/logout', function () {
    return view('pages.logout');
})->name('logout');

