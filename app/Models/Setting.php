<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Setting extends Model
{
    protected $fillable = [
        'is_rtl',
        'body_background_image',
        'body_background_fit',
        'app_logo',
        'logo_width',
        'logo_width_unit',
        'logo_height',
        'logo_height_unit',
    ];

    protected $casts = [
        'is_rtl' => 'boolean',
        'logo_width' => 'float',
        'logo_height' => 'float',
    ];

    public static function current(): self
    {
        return self::query()->first() ?? self::query()->create(['is_rtl' => false]);
    }
}
