<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AppText extends Model
{
    protected $fillable = [
        'key',
        'value',
        'locale',
    ];
}
