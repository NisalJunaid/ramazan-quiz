<?php

use App\Models\Setting;

// The text() helper now lives in app/Helpers/text.php for global autoloading.

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
