<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ClassesTop extends Model
{
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
}