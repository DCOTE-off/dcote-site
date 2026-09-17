<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CommentReaction extends Model
{
    use HasFactory;

    /**
     * Реакция не редактируется по смыслу — храним только момент создания.
     */
    public const UPDATED_AT = null;

    protected $fillable = [
        'comment_id',
        'user_id',
        'vote',
    ];

    protected $casts = [
        'vote' => 'integer',
    ];

    public function comment(): BelongsTo
    {
        return $this->belongsTo(Comment::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
