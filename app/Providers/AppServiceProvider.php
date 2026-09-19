<?php

namespace App\Providers;

use App\Models\AnimeEpisode;
use App\Models\AnimeSeason;
use App\Models\RanobeChapter;
use App\Models\RanobeVolume;
use App\Models\User;
use Illuminate\Database\Eloquent\Relations\Relation;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        //
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        Gate::define('access-admin', function (User $user) {
            return in_array($user->role_id, [3, 4]);
        });

        Relation::enforceMorphMap([
            'anime_season' => AnimeSeason::class,
            'anime_episode' => AnimeEpisode::class,
            'ranobe_volume' => RanobeVolume::class,
            'ranobe_chapter' => RanobeChapter::class,
        ]);
    }
}
