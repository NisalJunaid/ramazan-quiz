<?php

use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;

if (! function_exists('text')) {
    function text(string $key, string $fallback = ''): string
    {
        return Cache::remember(
            'app_text:' . app()->getLocale() . ':' . $key,
            60,
            function () use ($key, $fallback) {
                $row = DB::table('app_texts')
                    ->where('key', $key)
                    ->where(function ($q) {
                        $q->where('locale', app()->getLocale())
                            ->orWhereNull('locale');
                    })
                    ->orderByRaw('locale is null')
                    ->first();

                return $row?->value ?? $fallback;
            }
        );
    }
}
