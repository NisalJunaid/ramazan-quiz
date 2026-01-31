<?php

use App\Models\AppText;
use App\Models\Setting;
use Illuminate\Support\Facades\Cache;

if (! function_exists('text')) {
    function text(string $key, string $fallback = '', ?string $locale = null): string
    {
        $locale = $locale ?: app()->getLocale();

        $localizedValue = Cache::rememberForever("app_texts.{$locale}.{$key}", function () use ($key, $locale) {
            return AppText::query()
                ->where('key', $key)
                ->where('locale', $locale)
                ->value('value');
        });

        if ($localizedValue !== null) {
            return $localizedValue;
        }

        $defaultValue = Cache::rememberForever("app_texts.default.{$key}", function () use ($key) {
            return AppText::query()
                ->where('key', $key)
                ->whereNull('locale')
                ->value('value');
        });

        return $defaultValue ?? $fallback;
    }
}

if (! function_exists('is_rtl')) {
    function is_rtl(): bool
    {
        $mode = config('localization.rtl.mode', 'setting');
        $locale = app()->getLocale();
        $rtlLocales = config('localization.rtl.locales', []);
        $setting = Setting::current();

        // When mode=auto, locale-based RTL is enabled. Set mode=setting to allow admin overrides only.
        if ($mode === 'auto') {
            return in_array($locale, $rtlLocales, true) || (bool) $setting->is_rtl;
        }

        return (bool) $setting->is_rtl;
    }
}
