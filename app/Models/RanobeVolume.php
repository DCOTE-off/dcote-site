<?php

namespace App\Models;

use App\Models\Concerns\GuardsNaturalKeyUniqueness;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphMany;

class RanobeVolume extends Model
{
    use HasFactory;
    use GuardsNaturalKeyUniqueness;

    public const STATUS_RELEASED = 'Вышел';

    public const STATUS_ONGOING = 'Онгоинг';

    protected $fillable = [
        'volume_number',
        'general_number',
        'cover_image',
        'cover_image_mobile',
        'status',
        'release_date_book',
        'all_chapters',
        'release_date_digital',
        'ranobe_year_id',
        'pages_quantity',
        'isbn',
        'volume_description',
        'promo_link',
        'volume_images',
    ];

    protected $casts = [
        'volume_number' => 'decimal:1',
        'general_number' => 'integer',
        'release_date_book' => 'datetime',
        'release_date_digital' => 'datetime',
        'pages_quantity' => 'integer',
        'volume_images' => 'array',
    ];

    protected static function booted(): void
    {
        static::saving(fn (RanobeVolume $volume) => $volume->ensureUniqueNaturalKey(
            ['ranobe_year_id', 'volume_number'],
            'volume_number',
            'Том с таким номером уже существует в выбранном году.',
        ));
        static::deleting(fn (RanobeVolume $volume) => $volume->popularItems()->delete());
    }

    public function year()
    {
        return $this->belongsTo(RanobeYear::class, 'ranobe_year_id');
    }

    public function chapters()
    {
        return $this->hasMany(RanobeChapter::class, 'ranobe_volume_id');
    }

    public function popularItems(): MorphMany
    {
        return $this->morphMany(Popular::class, 'target');
    }

    public function ratings(): MorphMany
    {
        return $this->morphMany(Rating::class, 'rateable');
    }

    public function comments(): MorphMany
    {
        return $this->morphMany(Comment::class, 'commentable');
    }

    public static function syncFinishedStatuses(): int
    {
        return static::query()
            ->where('status', self::STATUS_ONGOING)
            ->where('all_chapters', '>', 0)
            ->whereRaw(
                '(select count(*) from ranobe_chapters where ranobe_chapters.ranobe_volume_id = ranobe_volumes.id) >= ranobe_volumes.all_chapters'
            )
            ->update(['status' => self::STATUS_RELEASED]);
    }

    public function getColorAttribute(): string
    {
        $colors = [
            self::STATUS_RELEASED => 'green',
            self::STATUS_ONGOING => 'purple',
        ];

        return $colors[$this->status] ?? 'yellow';
    }
}
