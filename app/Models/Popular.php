<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphTo;

class Popular extends Model
{
    public const TYPE_ANIME = 'anime';

    public const TYPE_RANOBE = 'ranobe';

    protected $fillable = [
        'target_type',
        'target_id',
        'sort_order',
        'is_active',
    ];

    protected $casts = [
        'sort_order' => 'integer',
        'is_active' => 'boolean',
    ];

    public function target(): MorphTo
    {
        return $this->morphTo();
    }

    public function getTypeLabelAttribute(): string
    {
        return match ($this->target_type) {
            self::TYPE_ANIME => 'Аниме',
            self::TYPE_RANOBE => 'Ранобэ',
            default => 'Неизвестный тип',
        };
    }

    public function getTargetLabelAttribute(): string
    {
        return match (true) {
            $this->target instanceof AnimeSeason => "{$this->target->season_number} сезон",
            $this->target instanceof RanobeVolume => sprintf(
                '%s год — %s том',
                $this->target->year?->year_number ?? '?',
                (float) $this->target->volume_number
            ),
            default => 'Материал удалён',
        };
    }
}
