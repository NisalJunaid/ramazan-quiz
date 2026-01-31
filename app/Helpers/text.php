<?php

use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\HtmlString;

if (! function_exists('text')) {
    function text(string $key, string $fallback = ''): HtmlString
    {
        $locale = app()->getLocale();
        $localeCacheKey = "app_texts.{$locale}.{$key}";
        $defaultCacheKey = "app_texts.default.{$key}";
        $hasFonts = Schema::hasTable('fonts');

        $payload = Cache::get($localeCacheKey);

        if ($payload === null) {
            $localeQuery = DB::table('app_texts')
                ->select('app_texts.value', 'app_texts.font_size', 'app_texts.text_color')
                ->where('app_texts.key', $key)
                ->where('app_texts.locale', $locale);

            if ($hasFonts) {
                $localeQuery
                    ->leftJoin('fonts', 'app_texts.font_id', '=', 'fonts.id')
                    ->addSelect('fonts.css_class');
            }

            $localeRow = $localeQuery->first();

            if ($localeRow) {
                $payload = [
                    'value' => $localeRow->value,
                    'css_class' => $localeRow->css_class ?? null,
                    'font_size' => $localeRow->font_size ?? null,
                    'text_color' => $localeRow->text_color ?? null,
                ];

                Cache::put($localeCacheKey, $payload, 60);
            } else {
                $payload = Cache::remember($defaultCacheKey, 60, function () use ($key, $fallback) {
                    $defaultQuery = DB::table('app_texts')
                        ->select('app_texts.value', 'app_texts.font_size', 'app_texts.text_color')
                        ->where('app_texts.key', $key)
                        ->whereNull('app_texts.locale');

                    if (Schema::hasTable('fonts')) {
                        $defaultQuery
                            ->leftJoin('fonts', 'app_texts.font_id', '=', 'fonts.id')
                            ->addSelect('fonts.css_class');
                    }

                    $defaultRow = $defaultQuery->first();

                    return [
                        'value' => $defaultRow?->value ?? $fallback,
                        'css_class' => $defaultRow?->css_class ?? null,
                        'font_size' => $defaultRow?->font_size ?? null,
                        'text_color' => $defaultRow?->text_color ?? null,
                    ];
                });
            }
        }

        $value = $payload['value'] ?? $fallback;
        $cssClass = $payload['css_class'] ?? null;
        $fontSize = $payload['font_size'] ?? null;
        $textColor = $payload['text_color'] ?? null;
        $escapedValue = e($value);

        if ($cssClass || $fontSize || $textColor) {
            // Inline styles allow per-text overrides without requiring Tailwind rebuilds.
            $styleRules = [];

            if ($fontSize) {
                $styleRules[] = 'font-size:' . e($fontSize);
            }

            if ($textColor) {
                $styleRules[] = 'color:' . e($textColor);
            }

            $styleAttribute = $styleRules ? ' style="' . implode(';', $styleRules) . '"' : '';
            $classAttribute = $cssClass ? ' class="' . e($cssClass) . '"' : '';

            return new HtmlString('<span' . $classAttribute . $styleAttribute . '>' . $escapedValue . '</span>');
        }

        return new HtmlString($escapedValue);
    }
}
