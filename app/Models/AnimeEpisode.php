<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AnimeEpisode extends Model
{
    protected $table = 'anime_episodes';


    public $timestamps = false;

    protected $fillable = [
        'season_id',
        'episode_number',
        'episode_name',
        'has_dub',
        'has_sub',
        'has_anilibria',
        'opening_start',
    ];


    public function season()
    {
        return $this->belongsTo(AnimeSeason::class, 'season_id', 'id');
    }
}