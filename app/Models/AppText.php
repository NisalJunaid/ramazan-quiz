<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AppText extends Model
{
    protected $fillable = [
        'key',
        'value',
        'locale',
        'font_id',
        'font_size',
        'text_color',
    ];

    public function font()
    {
        return $this->belongsTo(Font::class);
    }
}
