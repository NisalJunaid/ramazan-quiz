@extends('layouts.app')

@section('content')
    <div class="mx-auto flex w-full max-w-6xl flex-col gap-8 px-4 sm:px-6 lg:px-8">
        @php
            $heroBackgroundImage = $themeSettings?->home_hero_background_image;
            $heroBackgroundUrl = $heroBackgroundImage
                ? \Illuminate\Support\Facades\Storage::disk('public')->url($heroBackgroundImage)
                : null;
            $heroBackgroundOpacity = $themeSettings?->home_hero_background_opacity ?? 0.15;
            $heroBackgroundFit = $themeSettings?->home_hero_background_fit ?? 'cover';
            $heroBackgroundPosition = $themeSettings?->home_hero_background_position ?? 'center';
            $heroBackgroundRepeat = $themeSettings?->home_hero_background_repeat ?? 'no-repeat';
            $heroBackgroundUsesRepeat = $heroBackgroundUrl && $heroBackgroundRepeat !== 'no-repeat';
            $heroBackgroundSizeMap = [
                'cover' => 'cover',
                'contain' => 'contain',
                'fill' => '100% 100%',
                'none' => 'auto',
                'scale-down' => 'contain',
            ];
            $heroBackgroundSize = $heroBackgroundSizeMap[$heroBackgroundFit] ?? 'cover';
        @endphp

        <section class="card relative overflow-hidden p-8 shadow-sm">
            @if ($heroBackgroundUrl)
                <div class="absolute inset-0 pointer-events-none">
                    @if ($heroBackgroundUsesRepeat)
                        <div
                            class="h-full w-full"
                            style="background-image: url('{{ $heroBackgroundUrl }}'); background-repeat: {{ $heroBackgroundRepeat }}; background-size: {{ $heroBackgroundSize }}; background-position: {{ $heroBackgroundPosition }}; opacity: {{ $heroBackgroundOpacity }};"
                        ></div>
                    @else
                        <img
                            class="h-full w-full"
                            src="{{ $heroBackgroundUrl }}"
                            alt=""
                            style="object-fit: {{ $heroBackgroundFit }}; object-position: {{ $heroBackgroundPosition }}; opacity: {{ $heroBackgroundOpacity }};"
                        >
                    @endif
                </div>
            @endif
            <div class="relative z-10">
                @if (session('status'))
                    <div class="mb-6 rounded-2xl border px-4 py-3 text-sm" style="border-color: var(--color-ring); background: var(--color-surface-tint); color: var(--color-primary);">
                        {{ session('status') }}
                    </div>
                @endif
                <div class="flex flex-col gap-6 md:flex-row md:[dir=rtl]:flex-row-reverse md:items-center md:justify-between">
                    <div class="flex shrink-0 justify-end">
                        <x-application-logo :home="true" />
                    </div>
                    <div class="max-w-2xl">
                        <p class="text-xs uppercase tracking-[0.4em] text-amber-500">{{ text('home.overline', 'Ramazan Daily Quiz Portal') }}</p>
                        <h1 class="mt-4 text-3xl font-semibold text-theme sm:text-4xl">{{ text('home.title', "Welcome to today's learning journey.") }}</h1>
                        <p class="mt-3 text-sm text-muted sm:text-base">
                            {{ text('home.subtitle', 'Join the daily quiz to test your knowledge and see how you rank alongside the community.') }}
                        </p>
                        <div class="mt-6 flex flex-wrap gap-3">
                            @auth
                                <a class="btn-primary inline-flex items-center justify-center px-5 py-2 text-sm font-semibold shadow-sm" href="{{ route('quiz.today') }}">
                                    {{ text('home.cta.start_quiz', "Start Today's Quiz") }}
                                </a>
                                @if ($canViewLeaderboard)
                                    <a class="btn-outline inline-flex items-center justify-center px-5 py-2 text-sm font-semibold" href="{{ route('leaderboard') }}">
                                        {{ text('home.cta.view_leaderboard', 'View Leaderboard') }}
                                    </a>
                                @endif
                            @else
                                <a class="btn-primary inline-flex items-center justify-center px-5 py-2 text-sm font-semibold shadow-sm" href="{{ route('login') }}">
                                    {{ text('home.cta.login', 'Login to Start') }}
                                </a>
                                <a class="btn-outline inline-flex items-center justify-center px-5 py-2 text-sm font-semibold" href="{{ route('register') }}">
                                    {{ text('home.cta.register', 'Create an Account') }}
                                </a>
                            @endauth
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <section class="grid gap-6 lg:grid-cols-2">
            <div class="card p-6 shadow-sm">
                <h2 class="text-lg font-semibold text-theme">{{ text('home.status.title', "Today's Quiz Status") }}</h2>
                @if ($quizDay)
                    <p class="mt-2 text-sm text-muted">
                        {{ $quizDay->title }} · {{ $quizDay->quiz_date }}
                    </p>
                    <p class="mt-2 text-sm {{ $isActive ? 'text-theme' : 'text-muted' }}">
                        {{ $isActive ? text('home.status.open', 'Quiz is open now.') : text('home.status.scheduled', 'Quiz is scheduled for today.') }}
                    </p>
                @else
                    <p class="mt-2 text-sm text-muted">
                        {{ text('home.status.none', 'No published quiz scheduled for today. Please check back later.') }}
                    </p>
                @endif
                <div class="mt-4 flex flex-wrap gap-3">
                    @auth
                        <a class="inline-flex items-center gap-2 text-sm font-semibold text-theme" href="{{ route('quiz.today') }}">
                            {{ text('home.status.link', 'Go to Quiz') }}
                            <span aria-hidden="true">→</span>
                        </a>
                    @else
                        <a class="inline-flex items-center gap-2 text-sm font-semibold text-theme" href="{{ route('login') }}">
                            {{ text('home.status.signin', 'Sign in') }}
                            <span aria-hidden="true">→</span>
                        </a>
                    @endauth
                </div>
            </div>

            <div class="card p-6 shadow-sm">
                <h2 class="text-lg font-semibold text-theme">{{ text('home.leaderboard.title', 'Community Leaderboard') }}</h2>
                <p class="mt-2 text-sm text-muted">
                    {{ text('home.leaderboard.subtitle', 'See the fastest and highest scoring submissions for the latest quiz day.') }}
                </p>
                @if ($canViewLeaderboard)
                    <div class="mt-4">
                        <a class="inline-flex items-center gap-2 text-sm font-semibold text-theme" href="{{ route('leaderboard') }}">
                            {{ text('home.leaderboard.link', 'View Rankings') }}
                            <span aria-hidden="true">→</span>
                        </a>
                    </div>
                @endif
            </div>
        </section>
    </div>
@endsection
