<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RanobeYear extends Model
{
    use HasFactory;

    public $timestamps = false;

    protected $fillable = [
        'year_number',
        'year_readable',
        'words_quantity',
        'hours_of_reading',
        'status',
    ];

    protected $casts = [
        'year_number' => 'integer',
        'words_quantity' => 'integer',
    ];

    public function volumes()
    {
        return $this->hasMany(RanobeVolume::class, 'ranobe_year_id');
    }

    public function chapters()
    {
        return $this->hasManyThrough(RanobeChapter::class, RanobeVolume::class, 'ranobe_year_id', 'ranobe_volume_id');
    }
}
