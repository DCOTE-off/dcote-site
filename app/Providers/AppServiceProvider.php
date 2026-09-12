<?php

namespace App\Providers;

use App\Models\AnimeEpisode;
use App\Models\AnimeSeason;
use App\Models\Popular;
use App\Models\RanobeChapter;
use App\Models\RanobeVolume;
use App\Models\User;
use Database\Seeders\TestAccountsSeeder;
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

        if (app()->environment('local')) {
            app(TestAccountsSeeder::class)->run();
        }
        
        Relation::enforceMorphMap([
            Popular::TYPE_ANIME => AnimeSeason::class,
            Popular::TYPE_RANOBE => RanobeVolume::class,
            'anime_episode' => AnimeEpisode::class,
            'ranobe_volume' => RanobeVolume::class,
        ]);
    }
}
