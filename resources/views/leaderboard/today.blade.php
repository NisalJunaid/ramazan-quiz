@extends('layouts.app')

@section('content')
    <h1 class="text-2xl font-semibold">Leaderboard</h1>
    <p class="mt-2"><a class="text-blue-600 hover:underline" href="{{ route('home') }}">Back to Home</a></p>

    @if (! $quizDay)
        <p class="mt-4 text-gray-600">No quiz available yet.</p>
    @else
        <h2 class="mt-4 text-lg font-semibold">{{ $quizDay->title }} ({{ $quizDay->quiz_date }})</h2>

        @if ($attempts->isEmpty())
            <p class="mt-2 text-gray-600">No submissions yet.</p>
        @else
            <div class="mt-4 overflow-x-auto">
                <table class="min-w-full border border-gray-200 text-sm">
                    <thead class="bg-gray-50 text-left">
                        <tr>
                            <th class="border border-gray-200 px-3 py-2">Rank</th>
                            <th class="border border-gray-200 px-3 py-2">Name</th>
                            <th class="border border-gray-200 px-3 py-2">Score</th>
                            <th class="border border-gray-200 px-3 py-2">Submitted At</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($attempts as $index => $attempt)
                            <tr>
                                <td class="border border-gray-200 px-3 py-2">{{ $index + 1 }}</td>
                                <td class="border border-gray-200 px-3 py-2">{{ $attempt->user->name }}</td>
                                <td class="border border-gray-200 px-3 py-2">{{ $attempt->score }}</td>
                                <td class="border border-gray-200 px-3 py-2">
                                    {{ \Illuminate\Support\Carbon::parse($attempt->submitted_at)->format('Y-m-d H:i') }}
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @endif
    @endif
@endsection
