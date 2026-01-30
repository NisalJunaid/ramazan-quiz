@extends('layouts.app')

@section('content')
    <div class="mx-auto flex w-full max-w-5xl flex-col gap-6 px-4 sm:px-6 lg:px-8">
        <header class="flex flex-wrap items-center justify-between gap-4">
            <div>
                <h1 class="text-2xl font-semibold text-emerald-700">Leaderboard</h1>
                <p class="mt-1 text-sm text-gray-600">Latest quiz rankings and submission order.</p>
            </div>
            <a class="text-sm font-semibold text-emerald-700 hover:text-emerald-800" href="{{ route('home') }}">Back to Home</a>
        </header>

        @if (! $quizDay)
            <div class="rounded-2xl border border-dashed border-gray-300 bg-white p-6 text-center text-sm text-gray-600">
                No quiz available yet.
            </div>
        @else
            <section class="rounded-2xl bg-white p-6 shadow-sm ring-1 ring-gray-200">
                <div class="flex flex-wrap items-center justify-between gap-3">
                    <h2 class="text-lg font-semibold text-gray-900">{{ $quizDay->title }}</h2>
                    <span class="rounded-full bg-emerald-50 px-3 py-1 text-xs font-semibold text-emerald-700">{{ $quizDay->quiz_date }}</span>
                </div>

                @if ($attempts->isEmpty())
                    <p class="mt-4 text-sm text-gray-600">No submissions yet.</p>
                @else
                    <div class="mt-6 overflow-x-auto rounded-2xl border border-gray-200">
                        <table class="min-w-full text-sm">
                            <thead class="bg-emerald-50 text-left text-xs uppercase tracking-widest text-emerald-700">
                                <tr>
                                    <th class="px-4 py-3">Rank</th>
                                    <th class="px-4 py-3">Name</th>
                                    <th class="px-4 py-3">Score</th>
                                    <th class="px-4 py-3">Submitted At</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-100">
                                @foreach ($attempts as $index => $attempt)
                                    @php
                                        $isTop = $index < 3;
                                    @endphp
                                    <tr class="{{ $isTop ? 'bg-emerald-50/60' : 'bg-white' }}">
                                        <td class="px-4 py-3 font-semibold text-gray-900">
                                            {{ $index + 1 }}
                                        </td>
                                        <td class="px-4 py-3 {{ $isTop ? 'font-semibold text-emerald-700' : 'text-gray-700' }}">
                                            {{ $attempt->user->name }}
                                        </td>
                                        <td class="px-4 py-3 font-semibold text-gray-900">
                                            {{ $attempt->score }}
                                        </td>
                                        <td class="px-4 py-3 text-gray-600">
                                            {{ \Illuminate\Support\Carbon::parse($attempt->submitted_at)->format('Y-m-d H:i') }}
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @endif
            </section>
        @endif
    </div>
@endsection

@push('live-reload')
    @if ($quizDay)
        <script>
            window.liveReloadChannels = window.liveReloadChannels || [];
            window.liveReloadChannels.push({ name: 'leaderboard.{{ $quizDay->id }}', event: 'LeaderboardChanged' });
        </script>
    @endif
@endpush
