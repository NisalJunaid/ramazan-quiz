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

    <body class="font-sans antialiased text-gray-800 bg-white">
        <div class="min-h-screen bg-white">

            @include('layouts.navigation')

            <!-- Page Heading -->
            @hasSection('header')
                <header class="bg-white border-b border-gray-200/70">
                    <div class="max-w-7xl mx-auto py-8 px-4 sm:px-6 lg:px-8">
                        @yield('header')
                    </div>
                </header>
            @endif

            <!-- Page Content -->
            <main class="py-10">
                @yield('content')
            </main>
        </div>

        <script>
            window.liveReloadChannels = [
                { name: 'quiz-range', event: ['QuizRangeChanged', 'QuizDayChanged', 'LeaderboardChanged'] },
            ];
        </script>
        @stack('live-reload')
    </body>
</html>
