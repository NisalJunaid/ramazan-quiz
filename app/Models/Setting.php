<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Setting extends Model
{
    protected $fillable = [
        'is_rtl',
    ];

    public static function current(): self
    {
        return self::query()->first() ?? self::query()->create(['is_rtl' => false]);
    }
}
