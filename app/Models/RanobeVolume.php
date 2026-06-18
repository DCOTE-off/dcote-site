<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphMany;

class RanobeVolume extends Model
{
    use HasFactory;

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

    public function getColorAttribute()
    {
        $colors = [
            'Вышел' => 'green',
            'Онгоинг' => 'purple',
        ];

        return $colors[$this->status] ?? 'yellow';
    }
}
