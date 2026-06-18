<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphMany;

class AnimeEpisode extends Model
{
    protected $table = 'anime_episodes';

    public $timestamps = false;

    protected $fillable = [
        'season_id',
        'episode_number',
        'episode_name',
        'trailer_link',
        'completed',
        'opening_start',
        'appear_in',
    ];

    public function season()
    {
        return $this->belongsTo(AnimeSeason::class, 'season_id', 'id');
    }

    public function ratings(): MorphMany
    {
        return $this->morphMany(Rating::class, 'rateable');
    }
}