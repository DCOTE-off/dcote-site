<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RanobeVolume extends Model
{
    use HasFactory;

    protected $fillable = [
        'volume_number',
        'general_number',
        'status',
        'release_date_book',
        'release_date_digital',
        'ranobe_year_id',
        'pages_quantity',
        'isbn',
        'volume_description',
    ];

    protected $casts = [
        'volume_number' => 'decimal:2',
        'general_number' => 'integer',
        'release_date_book' => 'datetime',
        'release_date_digital' => 'datetime',
        'pages_quantity' => 'integer',
    ];

    public function year()
    {
        return $this->belongsTo(RanobeYear::class, 'ranobe_year_id');
    }

    public function chapters()
    {
        return $this->hasMany(RanobeChapter::class, 'volume_id');
    }
}
