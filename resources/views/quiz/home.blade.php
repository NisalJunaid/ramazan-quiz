@extends('layouts.app')

@section('content')
    <h1 class="text-2xl font-semibold">Ramazan Daily Quiz Portal</h1>
    <p class="mt-2 text-gray-600">Welcome to today's quiz portal.</p>

    @auth
        <nav class="mt-6 space-y-2">
            <a class="block text-blue-600 hover:underline" href="{{ route('quiz.today') }}">Today's Quiz</a>
            <a class="block text-blue-600 hover:underline" href="{{ route('leaderboard') }}">Leaderboard</a>
        </nav>
    @else
        <nav class="mt-6 space-y-2">
            <a class="block text-blue-600 hover:underline" href="{{ route('login') }}">Login</a>
            <a class="block text-blue-600 hover:underline" href="{{ route('register') }}">Register</a>
        </nav>
    @endauth
@endsection
