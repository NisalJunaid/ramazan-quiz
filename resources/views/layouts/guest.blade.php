@php
    $isRtl = is_rtl();
    $fontCollection = $fonts ?? collect();
    $googleFonts = $fontCollection->where('source_type', 'google');
    $uploadFonts = $fontCollection->where('source_type', 'upload');
    $themeSettings = $themeSettings ?? null;
    $themeDefaults = [
        'primary_color' => '#059669',
        'primary_hover_color' => '#047857',
        'accent_color' => '#f59e0b',
        'surface_color' => '#ffffff',
        'surface_tint' => 'rgba(255,255,255,0.90)',
        'text_color' => '#111827',
        'muted_text_color' => '#4b5563',
        'border_color' => 'rgba(17,24,39,0.12)',
        'ring_color' => 'rgba(5,150,105,0.18)',
        'button_radius' => '9999px',
        'card_radius' => '24px',
        'focus_ring_color' => 'rgba(5,150,105,0.35)',
    ];
    $overlayOpacity = $themeSettings?->body_background_overlay_opacity ?? 0.90;
    $themeVars = [
        "--color-primary: " . ($themeSettings?->primary_color ?? $themeDefaults['primary_color']) . ';',
        "--color-primary-hover: " . ($themeSettings?->primary_hover_color ?? $themeDefaults['primary_hover_color']) . ';',
        "--color-accent: " . ($themeSettings?->accent_color ?? $themeDefaults['accent_color']) . ';',
        "--color-surface: " . ($themeSettings?->surface_color ?? $themeDefaults['surface_color']) . ';',
        "--color-surface-tint: " . ($themeSettings?->surface_tint ?? $themeDefaults['surface_tint']) . ';',
        "--color-text: " . ($themeSettings?->text_color ?? $themeDefaults['text_color']) . ';',
        "--color-muted: " . ($themeSettings?->muted_text_color ?? $themeDefaults['muted_text_color']) . ';',
        "--color-border: " . ($themeSettings?->border_color ?? $themeDefaults['border_color']) . ';',
        "--color-ring: " . ($themeSettings?->ring_color ?? $themeDefaults['ring_color']) . ';',
        "--radius-button: " . ($themeSettings?->button_radius ?? $themeDefaults['button_radius']) . ';',
        "--radius-card: " . ($themeSettings?->card_radius ?? $themeDefaults['card_radius']) . ';',
        "--color-focus-ring: " . ($themeSettings?->focus_ring_color ?? $themeDefaults['focus_ring_color']) . ';',
        "--bg-overlay-opacity: {$overlayOpacity};",
    ];
    $bodyBackgroundUrl = $themeSettings?->body_background_image
        ? asset('storage/' . $themeSettings->body_background_image)
        : null;
    $bodyBackgroundSize = match ($themeSettings?->body_background_fit) {
        'contain' => 'contain',
        'fill' => '100% 100%',
        default => 'cover',
    };
    $bodyStyle = trim(implode(' ', array_merge($themeVars, [
        'background-color: var(--color-surface);',
        $bodyBackgroundUrl
            ? "background-image: url('{$bodyBackgroundUrl}'); background-size: {$bodyBackgroundSize}; background-repeat: no-repeat; background-position: center center;"
            : '',
    ])));
    $bodyClass = $bodyBackgroundUrl
        ? 'font-sans text-theme antialiased'
        : 'font-sans text-theme antialiased';
    $containerClass = $bodyBackgroundUrl
        ? 'min-h-screen flex flex-col sm:justify-center items-center pt-6 sm:pt-0'
        : 'min-h-screen flex flex-col sm:justify-center items-center pt-6 sm:pt-0';
    $containerStyle = $bodyBackgroundUrl
        ? 'background-color: rgba(255, 255, 255, var(--bg-overlay-opacity));'
        : 'background: var(--color-surface);';
@endphp
<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" dir="{{ $isRtl ? 'rtl' : 'ltr' }}" class="{{ $isRtl ? 'rtl' : 'ltr' }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ text('app.name', config('app.name', 'Ramazan Quiz')) }}</title>

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />
        @foreach ($googleFonts as $font)
            <link rel="stylesheet" href="{{ $font->source_path }}">
        @endforeach

        @if ($fontCollection->isNotEmpty())
            <style>
                @foreach ($uploadFonts as $font)
                    @php
                        $extension = strtolower(pathinfo($font->source_path, PATHINFO_EXTENSION));
                        $format = match ($extension) {
                            'woff2' => 'woff2',
                            'woff' => 'woff',
                            'ttf' => 'truetype',
                            default => 'truetype',
                        };
                    @endphp
                    @font-face {
                        font-family: '{{ $font->name }}';
                        src: url('{{ asset('storage/' . $font->source_path) }}') format('{{ $format }}');
                        font-display: swap;
                    }
                @endforeach

                @foreach ($fontCollection as $font)
                    .{{ $font->css_class }} { font-family: {{ $font->css_family }}; }
                @endforeach
            </style>
        @endif

        <!-- Scripts -->
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body class="{{ $bodyClass }}" style="{{ $bodyStyle }}">
        <div class="{{ $containerClass }}" style="{{ $containerStyle }}">
            <div>
                <a href="/">
                    <x-application-logo class="w-20 h-20 fill-current" style="color: var(--color-primary);" :theme-settings="$themeSettings" />
                </a>
            </div>

            <div class="card w-full sm:max-w-md mt-6 px-6 py-4 shadow-md overflow-hidden">
                {{ $slot }}
            </div>
        </div>
    </body>
</html>
