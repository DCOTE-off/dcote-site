<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RanobeChapter extends Model
{
    use HasFactory;

    protected $fillable = [
        'volume_id',
        'title',
        'chapter_number',
        'chapter_content',
    ];

    public function volume()
    {
        return $this->belongsTo(RanobeVolume::class, 'volume_id');
    }
}
