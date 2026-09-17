<?php

namespace App\Models;

use App\Models\Concerns\GuardsNaturalKeyUniqueness;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Validation\ValidationException;

class RanobeChapter extends Model
{
    use HasFactory;
    use GuardsNaturalKeyUniqueness;

    protected $fillable = [
        'ranobe_volume_id',
        'title',
        'title_label',
        'chapter_number',
        'chapter_content',
        'ranobe_year_id',
    ];

    protected $casts = [
        'chapter_number' => 'decimal:1',
    ];

    protected static function booted(): void
    {
        static::saving(function (RanobeChapter $chapter): void {
            $volume = RanobeVolume::query()->find($chapter->ranobe_volume_id);
            if (!$volume) {
                throw ValidationException::withMessages([
                    'ranobe_volume_id' => ['Выбранный том не существует.'],
                ]);
            }

            // The volume is the source of truth; the redundant column cannot drift.
            $chapter->ranobe_year_id = $volume->ranobe_year_id;
            $chapter->ensureUniqueNaturalKey(
                ['ranobe_volume_id', 'chapter_number'],
                'chapter_number',
                'Глава с таким номером уже существует в выбранном томе.',
            );
        });
    }

    public function volume()
    {
        return $this->belongsTo(RanobeVolume::class, 'ranobe_volume_id');
    }

    public function year()
    {
        return $this->belongsTo(RanobeYear::class, 'ranobe_year_id');
    }

    public function comments(): MorphMany
    {
        return $this->morphMany(Comment::class, 'commentable');
    }
}
