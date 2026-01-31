@php
    $isRtl = is_rtl();
    $fontCollection = $fonts ?? collect();
    $googleFonts = $fontCollection->where('source_type', 'google');
    $uploadFonts = $fontCollection->where('source_type', 'upload');
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
    <body class="font-sans text-gray-900 antialiased">
        <div class="min-h-screen flex flex-col sm:justify-center items-center pt-6 sm:pt-0 bg-white">
            <div>
                <a href="/">
                    <x-application-logo class="w-20 h-20 fill-current text-gray-500" />
                </a>
            </div>

            <div class="w-full sm:max-w-md mt-6 px-6 py-4 bg-white shadow-md overflow-hidden sm:rounded-lg ring-1 ring-gray-200/70">
                {{ $slot }}
            </div>
        </div>
    </body>
</html>
