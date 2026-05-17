<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RanobeChapter extends Model
{
    use HasFactory;

    protected $fillable = [
        'ranobe_volume_id',
        'title',
        'chapter_number',
        'chapter_content',
        'ranobe_year_id',
    ];

    public function volume()
    {
        return $this->belongsTo(RanobeVolume::class, 'ranobe_volume_id');
    }

    public function year()
    {
        return $this->belongsTo(RanobeYear::class, 'ranobe_year_id');
    }
}
