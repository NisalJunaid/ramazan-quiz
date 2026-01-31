<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Setting extends Model
{
    protected $fillable = [
        'is_rtl',
        'body_background_image',
        'body_background_fit',
        'body_background_overlay_opacity',
        'app_logo',
        'logo_width',
        'logo_width_unit',
        'logo_height',
        'logo_height_unit',
        'home_logo_width',
        'home_logo_width_unit',
        'home_logo_height',
        'home_logo_height_unit',
        'home_hero_background_image',
        'home_hero_background_opacity',
        'home_hero_background_fit',
        'home_hero_background_position',
        'home_hero_background_repeat',
        'primary_color',
        'primary_hover_color',
        'accent_color',
        'surface_color',
        'surface_tint',
        'text_color',
        'muted_text_color',
        'border_color',
        'ring_color',
        'button_radius',
        'card_radius',
        'focus_ring_color',
    ];

    protected $casts = [
        'is_rtl' => 'boolean',
        'logo_width' => 'float',
        'logo_height' => 'float',
        'home_logo_width' => 'float',
        'home_logo_height' => 'float',
        'body_background_overlay_opacity' => 'float',
        'home_hero_background_opacity' => 'float',
    ];

    public static function current(): self
    {
        return self::query()->first() ?? self::query()->create(['is_rtl' => false]);
    }
}
