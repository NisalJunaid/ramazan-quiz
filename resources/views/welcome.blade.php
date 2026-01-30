@extends('layouts.app')

@section('content')
    <div class="mx-auto flex w-full max-w-4xl flex-col gap-6 px-4 sm:px-6 lg:px-8">
        <section class="rounded-2xl bg-white p-8 shadow-sm ring-1 ring-gray-200">
            <p class="text-xs uppercase tracking-[0.4em] text-amber-500">Ramazan Daily Quiz Portal</p>
            <h1 class="mt-3 text-2xl font-semibold text-emerald-700">Welcome</h1>
            <p class="mt-2 text-sm text-gray-600">
                Sign in to participate in today’s quiz and review the community leaderboard.
            </p>
            <div class="mt-6 flex flex-wrap gap-3">
                @auth
                    <a class="inline-flex items-center justify-center rounded-full bg-emerald-600 px-5 py-2 text-sm font-semibold text-white shadow-sm hover:bg-emerald-700" href="{{ route('home') }}">
                        Go to Home
                    </a>
                    <a class="inline-flex items-center justify-center rounded-full border border-emerald-600 px-5 py-2 text-sm font-semibold text-emerald-600 hover:bg-emerald-50" href="{{ route('leaderboard') }}">
                        View Leaderboard
                    </a>
                @else
                    <a class="inline-flex items-center justify-center rounded-full bg-emerald-600 px-5 py-2 text-sm font-semibold text-white shadow-sm hover:bg-emerald-700" href="{{ route('login') }}">
                        Log In
                    </a>
                    <a class="inline-flex items-center justify-center rounded-full border border-emerald-600 px-5 py-2 text-sm font-semibold text-emerald-600 hover:bg-emerald-50" href="{{ route('register') }}">
                        Create Account
                    </a>
                @endauth
            </div>
        </section>
    </div>
@endsection
