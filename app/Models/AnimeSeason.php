<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphMany;

class AnimeSeason extends Model
{
    protected $table = 'anime_seasons';

    public $timestamps = false;

    protected $fillable = [
        'status',
        'season_time',
        'release_time',
        'studio',
        'number_of_episodes',
        'last_update',
        'img_src',
        'season_number',
        'season_description',
        'trailer_link',
        'adapt_volumes',
        'adapt_volumes_brackets',
    ];

    protected static function booted(): void
    {
        static::deleting(fn (AnimeSeason $season) => $season->popularItems()->delete());
    }

    public function episodes()
    {
        return $this->hasMany(AnimeEpisode::class, 'season_id', 'id');
    }

    public function popularItems(): MorphMany
    {
        return $this->morphMany(Popular::class, 'target');
    }

    public function getColorAttribute()
    {
        $colors = [
            'Вышел' => 'green',
            'Онгоинг' => 'purple',
        ];

        return $colors[$this->status] ?? 'yellow';
    }
}
