<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class Comment extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $fillable = [
        'commentable_type',
        'commentable_id',
        'user_id',
        'parent_id',
        'root_id',
        'content',
        'rating',
    ];

    protected $casts = [
        'rating' => 'integer',
    ];

    /**
     * Ресурс, к которому привязан комментарий (серия, глава, том, новость…).
     */
    public function commentable(): MorphTo
    {
        return $this->morphTo();
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function parent(): BelongsTo
    {
        return $this->belongsTo(self::class, 'parent_id');
    }

    public function root(): BelongsTo
    {
        return $this->belongsTo(self::class, 'root_id');
    }

    public function replies(): HasMany
    {
        return $this->hasMany(self::class, 'parent_id');
    }

    public function reactions(): HasMany
    {
        return $this->hasMany(CommentReaction::class);
    }

    /**
     * Комментарии конкретного ресурса.
     */
    public function scopeFor(Builder $query, Model $commentable): Builder
    {
        return $query
            ->where('commentable_type', $commentable->getMorphClass())
            ->where('commentable_id', $commentable->getKey());
    }

    /**
     * Только корневые комментарии (начала веток).
     */
    public function scopeRoots(Builder $query): Builder
    {
        return $query->whereNull('parent_id');
    }
}
