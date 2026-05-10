<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;


class UpdateFeed extends Model
{
    protected $table = 'update_feed';

    const UPDATED_AT = null;

    protected $fillable = [
        'description',
        'link',
        'created_at',
    ];
}