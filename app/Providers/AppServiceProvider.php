<?php

namespace App\Providers;

use App\Models\AnimeSeason;
use App\Models\Popular;
use App\Models\RanobeVolume;
use Illuminate\Database\Eloquent\Relations\Relation;
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
        Relation::enforceMorphMap([
            Popular::TYPE_ANIME => AnimeSeason::class,
            Popular::TYPE_RANOBE => RanobeVolume::class,
        ]);
    }
}
