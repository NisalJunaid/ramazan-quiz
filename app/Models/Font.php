<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Font extends Model
{
    protected $fillable = [
        'name',
        'css_family',
        'css_class',
        'source_type',
        'source_path',
        'is_rtl_optimized',
    ];
}
