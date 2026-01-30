@extends('layouts.app')

@section('content')
    <div class="mx-auto flex w-full max-w-6xl flex-col gap-8 px-4 sm:px-6 lg:px-8">
        <section class="rounded-3xl bg-white p-8 shadow-sm ring-1 ring-emerald-100">
            @if (session('status'))
                <div class="mb-6 rounded-2xl border border-emerald-100 bg-emerald-50 px-4 py-3 text-sm text-emerald-700">
                    {{ session('status') }}
                </div>
            @endif
            <div class="max-w-2xl">
                <p class="text-xs uppercase tracking-[0.4em] text-amber-500">Ramazan Daily Quiz Portal</p>
                <h1 class="mt-4 text-3xl font-semibold text-emerald-700 sm:text-4xl">Welcome to today's learning journey.</h1>
                <p class="mt-3 text-sm text-gray-600 sm:text-base">
                    Join the daily quiz to test your knowledge and see how you rank alongside the community.
                </p>
                <div class="mt-6 flex flex-wrap gap-3">
                    @auth
                        <a class="inline-flex items-center justify-center rounded-full bg-emerald-600 px-5 py-2 text-sm font-semibold text-white shadow-sm hover:bg-emerald-700" href="{{ route('quiz.today') }}">
                            Start Today's Quiz
                        </a>
                        @if ($canViewLeaderboard)
                            <a class="inline-flex items-center justify-center rounded-full border border-emerald-600 px-5 py-2 text-sm font-semibold text-emerald-600 hover:bg-emerald-50" href="{{ route('leaderboard') }}">
                                View Leaderboard
                            </a>
                        @endif
                    @else
                        <a class="inline-flex items-center justify-center rounded-full bg-emerald-600 px-5 py-2 text-sm font-semibold text-white shadow-sm hover:bg-emerald-700" href="{{ route('login') }}">
                            Login to Start
                        </a>
                        <a class="inline-flex items-center justify-center rounded-full border border-emerald-600 px-5 py-2 text-sm font-semibold text-emerald-600 hover:bg-emerald-50" href="{{ route('register') }}">
                            Create an Account
                        </a>
                    @endauth
                </div>
            </div>
        </section>

        <section class="grid gap-6 lg:grid-cols-2">
            <div class="rounded-2xl bg-white p-6 shadow-sm ring-1 ring-gray-200">
                <h2 class="text-lg font-semibold text-gray-900">Today's Quiz Status</h2>
                @if ($quizDay)
                    <p class="mt-2 text-sm text-gray-600">
                        {{ $quizDay->title }} · {{ $quizDay->quiz_date }}
                    </p>
                    <p class="mt-2 text-sm {{ $isActive ? 'text-emerald-700' : 'text-gray-600' }}">
                        {{ $isActive ? 'Quiz is open now.' : 'Quiz is scheduled for today.' }}
                    </p>
                @else
                    <p class="mt-2 text-sm text-gray-600">
                        No published quiz scheduled for today. Please check back later.
                    </p>
                @endif
                <div class="mt-4 flex flex-wrap gap-3">
                    @auth
                        <a class="inline-flex items-center gap-2 text-sm font-semibold text-emerald-700 hover:text-emerald-800" href="{{ route('quiz.today') }}">
                            Go to Quiz
                            <span aria-hidden="true">→</span>
                        </a>
                    @else
                        <a class="inline-flex items-center gap-2 text-sm font-semibold text-emerald-700 hover:text-emerald-800" href="{{ route('login') }}">
                            Sign in
                            <span aria-hidden="true">→</span>
                        </a>
                    @endauth
                </div>
            </div>

            <div class="rounded-2xl bg-white p-6 shadow-sm ring-1 ring-gray-200">
                <h2 class="text-lg font-semibold text-gray-900">Community Leaderboard</h2>
                <p class="mt-2 text-sm text-gray-600">
                    See the fastest and highest scoring submissions for the latest quiz day.
                </p>
                @if ($canViewLeaderboard)
                    <div class="mt-4">
                        <a class="inline-flex items-center gap-2 text-sm font-semibold text-emerald-700 hover:text-emerald-800" href="{{ route('leaderboard') }}">
                            View Rankings
                            <span aria-hidden="true">→</span>
                        </a>
                    </div>
                @endif
            </div>
        </section>
    </div>
@endsection
