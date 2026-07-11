<?php

namespace App\Models;

use App\Models\Concerns\GuardsNaturalKeyUniqueness;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphMany;

class AnimeSeason extends Model
{
    use GuardsNaturalKeyUniqueness;

    public const STATUS_RELEASED = 'Вышел';

    public const STATUS_ONGOING = 'Онгоинг';

    public const STATUS_ANNOUNCED = 'Анонс';

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
        static::saving(fn (AnimeSeason $season) => $season->ensureUniqueNaturalKey(
            ['season_number'],
            'season_number',
            'Сезон с таким номером уже существует.',
        ));
        static::deleting(fn (AnimeSeason $season) => $season->popularItems()->delete());
    }

    public function episodes(): HasMany
    {
        return $this->hasMany(AnimeEpisode::class, 'season_id', 'id');
    }

    public function releasedEpisodes(): HasMany
    {
        return $this->episodes()->where('completed', true);
    }

    public static function syncFinishedStatuses(): int
    {
        return static::query()
            ->where('status', self::STATUS_ONGOING)
            ->where('number_of_episodes', '>', 0)
            ->whereRaw(
                '(select count(*) from anime_episodes where anime_episodes.season_id = anime_seasons.id and anime_episodes.completed = 1) >= anime_seasons.number_of_episodes'
            )
            ->update(['status' => self::STATUS_RELEASED]);
    }

    public function popularItems(): MorphMany
    {
        return $this->morphMany(Popular::class, 'target');
    }

    public function isAnnounced(): bool
    {
        return $this->status === self::STATUS_ANNOUNCED;
    }

    public function isOngoing(): bool
    {
        return $this->status === self::STATUS_ONGOING;
    }

    public function scopeNotAnnounced(Builder $query): Builder
    {
        return $query->where('status', '!=', self::STATUS_ANNOUNCED);
    }

    public function getColorAttribute(): string
    {
        $colors = [
            self::STATUS_RELEASED => 'green',
            self::STATUS_ONGOING => 'purple',
            self::STATUS_ANNOUNCED => 'yellow',
        ];

        return $colors[$this->status] ?? 'yellow';
    }
}
