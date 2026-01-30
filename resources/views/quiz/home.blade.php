@extends('layouts.app')

@section('content')
    <div class="mx-auto flex w-full max-w-6xl flex-col gap-8 px-4 sm:px-6 lg:px-8">
        <section class="rounded-3xl bg-gradient-to-br from-indigo-600 via-indigo-500 to-blue-500 p-8 text-white shadow-lg">
            <div class="max-w-2xl">
                <p class="text-sm uppercase tracking-[0.3em] text-indigo-100">Ramazan Daily Quiz Portal</p>
                <h1 class="mt-4 text-3xl font-semibold sm:text-4xl">Welcome to today's learning journey.</h1>
                <p class="mt-3 text-sm text-indigo-100 sm:text-base">
                    Join the daily quiz to test your knowledge and see how you rank alongside the community.
                </p>
                <div class="mt-6 flex flex-wrap gap-3">
                    @auth
                        <a class="inline-flex items-center justify-center rounded-full bg-white px-5 py-2 text-sm font-semibold text-indigo-700 shadow-sm hover:bg-indigo-50" href="{{ route('quiz.today') }}">
                            Start Today's Quiz
                        </a>
                        <a class="inline-flex items-center justify-center rounded-full border border-white/40 px-5 py-2 text-sm font-semibold text-white hover:bg-white/10" href="{{ route('leaderboard') }}">
                            View Leaderboard
                        </a>
                    @else
                        <a class="inline-flex items-center justify-center rounded-full bg-white px-5 py-2 text-sm font-semibold text-indigo-700 shadow-sm hover:bg-indigo-50" href="{{ route('login') }}">
                            Login to Start
                        </a>
                        <a class="inline-flex items-center justify-center rounded-full border border-white/40 px-5 py-2 text-sm font-semibold text-white hover:bg-white/10" href="{{ route('register') }}">
                            Create an Account
                        </a>
                    @endauth
                </div>
            </div>
        </section>

        <section class="grid gap-6 lg:grid-cols-2">
            <div class="rounded-2xl bg-white p-6 shadow-sm ring-1 ring-gray-200">
                <h2 class="text-lg font-semibold text-gray-900">Today's Quiz Status</h2>
                <p class="mt-2 text-sm text-gray-600">
                    The quiz opens daily and is available for a limited time window. Sign in early so you can begin the attempt once it opens.
                </p>
                <div class="mt-4 flex flex-wrap gap-3">
                    @auth
                        <a class="inline-flex items-center gap-2 text-sm font-semibold text-indigo-600 hover:text-indigo-700" href="{{ route('quiz.today') }}">
                            Go to Quiz
                            <span aria-hidden="true">→</span>
                        </a>
                    @else
                        <a class="inline-flex items-center gap-2 text-sm font-semibold text-indigo-600 hover:text-indigo-700" href="{{ route('login') }}">
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
                <div class="mt-4">
                    <a class="inline-flex items-center gap-2 text-sm font-semibold text-indigo-600 hover:text-indigo-700" href="{{ route('leaderboard') }}">
                        View Rankings
                        <span aria-hidden="true">→</span>
                    </a>
                </div>
            </div>
        </section>
    </div>
@endsection
