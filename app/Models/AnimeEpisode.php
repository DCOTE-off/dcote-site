<?php

namespace App\Models;

use App\Models\Concerns\GuardsNaturalKeyUniqueness;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Support\Carbon;
use Illuminate\Support\CarbonInterface;

class AnimeEpisode extends Model
{
    use GuardsNaturalKeyUniqueness;

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

    // Blade получает готовый Carbon-объект и не должен самостоятельно разбирать дату из БД.
    protected $casts = [
        'completed' => 'boolean',
        'appear_in' => 'datetime',
    ];

    protected static function booted(): void
    {
        static::saving(fn (AnimeEpisode $episode) => $episode->ensureUniqueNaturalKey(
            ['season_id', 'episode_number'],
            'episode_number',
            'Серия с таким номером уже существует в выбранном сезоне.',
        ));
    }

    /**
     * Ставит галочку «Вышел» всем сериям, дата выхода которых уже наступила.
     *
     * Метод идемпотентен: повторный запуск не изменяет уже выпущенные серии.
     */
    public static function releaseDue(?CarbonInterface $now = null): int
    {
        return static::query()
            ->where('completed', false)
            ->whereNotNull('appear_in')
            ->where('appear_in', '<=', $now ?? Carbon::now())
            ->update(['completed' => true]);
    }

    public function season()
    {
        return $this->belongsTo(AnimeSeason::class, 'season_id', 'id');
    }

    public function ratings(): MorphMany
    {
        return $this->morphMany(Rating::class, 'rateable');
    }

    public function comments(): MorphMany
    {
        return $this->morphMany(Comment::class, 'commentable');
    }
}
