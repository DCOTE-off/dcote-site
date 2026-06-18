<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class ClassesTop extends Model
{
    private const RANK_LETTERS = ['A', 'B', 'C', 'D'];

    protected $table = 'classes_top';

    public $timestamps = false;

    protected $fillable = [
        'letter',
        'leader',
        'class_points',
        'leader_img',
        'spoilers',
        'color',
    ];

    protected $casts = [
        'class_points' => 'integer',
        'spoilers' => 'boolean',
    ];

    protected static function booted(): void
    {
        static::saved(function (ClassesTop $class): void {
            $spoilerGroups = [(bool) $class->spoilers];

            if ($class->wasChanged('spoilers')) {
                $spoilerGroups[] = (bool) $class->getOriginal('spoilers');
            }

            foreach (array_unique($spoilerGroups) as $spoilers) {
                static::synchronizeLetters((bool) $spoilers);
            }
        });

        static::deleted(fn (ClassesTop $class) => static::synchronizeLetters((bool) $class->spoilers));
    }

    public static function synchronizeLetters(bool $spoilers): void
    {
        DB::transaction(function () use ($spoilers): void {
            $classes = static::query()
                ->where('spoilers', $spoilers)
                ->orderByDesc('class_points')
                ->orderBy('id')
                ->lockForUpdate()
                ->get(['id', 'letter']);

            foreach ($classes as $position => $class) {
                $letter = self::RANK_LETTERS[$position] ?? $class->letter;

                if ($class->letter !== $letter) {
                    static::query()
                        ->whereKey($class->getKey())
                        ->update(['letter' => $letter]);
                }
            }
        });
    }
}
