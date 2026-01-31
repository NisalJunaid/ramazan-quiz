<?php

return [
    'rtl' => [
        // mode=setting: use settings.is_rtl only. mode=auto: use locale list and allow settings to force RTL on.
        'mode' => env('RTL_MODE', 'setting'),
        'locales' => ['ar', 'dv', 'fa', 'he', 'ur'],
    ],
];
