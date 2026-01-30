@extends('layouts.app')

@section('content')
    <div class="mx-auto flex w-full max-w-5xl flex-col gap-6 px-4 sm:px-6 lg:px-8">
        <header class="flex flex-wrap items-center justify-between gap-4">
            <div>
                <h1 class="text-2xl font-semibold text-emerald-700">Today's Quiz</h1>
                <p class="mt-1 text-sm text-gray-600">Complete one attempt before the window closes.</p>
            </div>
            <a class="text-sm font-semibold text-emerald-700 hover:text-emerald-800" href="{{ route('leaderboard') }}">View leaderboard</a>
        </header>

        @if (session('status'))
            <div class="rounded-2xl border border-emerald-100 bg-emerald-50 px-4 py-3 text-sm text-emerald-700">
                {{ session('status') }}
            </div>
        @endif

        @if (!$quizDay)
            <div class="rounded-2xl border border-dashed border-gray-300 bg-white p-6 text-center text-sm text-gray-600">
                No active quiz right now. Please check back later.
            </div>
        @else
            <section class="rounded-2xl bg-white p-6 shadow-sm ring-1 ring-gray-200">
                <div class="flex flex-wrap items-start justify-between gap-4 border-b border-gray-100 pb-4">
                    <div>
                        <p class="text-xs font-semibold uppercase tracking-[0.2em] text-amber-500">Quiz Window</p>
                        <h2 class="mt-2 text-xl font-semibold text-gray-900">{{ $quizDay->title }}</h2>
                        <p class="mt-2 text-sm text-gray-600">{{ $quizDay->start_at }} → {{ $quizDay->end_at }}</p>
                    </div>
                    <div class="rounded-xl bg-emerald-50 px-4 py-3 text-sm text-emerald-700">
                        Duration: <span class="font-semibold">{{ $quizDay->duration_seconds }} sec</span>
                    </div>
                </div>

                @if (!$attempt)
                    <form class="mt-6" method="POST" action="{{ route('quiz.start', $quizDay) }}">
                        @csrf
                        <button class="inline-flex items-center justify-center rounded-full bg-emerald-600 px-6 py-2 text-sm font-semibold text-white shadow-sm hover:bg-emerald-700" type="submit">
                            Start Quiz
                        </button>
                    </form>
                @elseif ($attempt->status === 'in_progress' && $remainingSeconds !== null)
                    @php
                        $formattedRemaining = gmdate('i:s', $remainingSeconds);
                    @endphp
                    <div
                        class="mt-6 rounded-2xl border border-emerald-100 bg-emerald-50 px-4 py-4"
                        data-quiz-timer
                        data-duration-seconds="{{ $quizDay->duration_seconds }}"
                        data-remaining-seconds="{{ $remainingSeconds }}"
                    >
                        <div class="flex items-center justify-between text-sm font-semibold text-emerald-800">
                            <span>Time remaining</span>
                            <span data-quiz-timer-text class="tabular-nums">{{ $formattedRemaining }}</span>
                        </div>
                        <div class="mt-3 h-2 w-full overflow-hidden rounded-full bg-emerald-100">
                            <div
                                class="h-full w-full rounded-full bg-emerald-500 transition-[width] duration-500 ease-linear"
                                data-quiz-timer-bar
                            ></div>
                        </div>
                    </div>

                    @if (! $question)
                        <p class="mt-4 text-sm text-gray-600">No questions available.</p>
                    @else
                        <form id="quiz-attempt-form" class="mt-6 space-y-5" method="POST" action="{{ route('attempt.submit', $attempt) }}">
                            @csrf
                            <fieldset class="rounded-2xl border border-gray-200 bg-gray-50/40 p-5">
                                <legend class="text-sm font-semibold text-gray-800">
                                    {{ $question->order_index }}. {{ $question->question_text }}
                                </legend>
                                <div class="mt-4 space-y-3">
                                    @foreach ($question->choices as $choice)
                                        <label class="flex items-center gap-3 rounded-xl border border-gray-200 bg-white px-4 py-3 text-sm text-gray-700 shadow-sm">
                                            <input
                                                class="h-4 w-4 text-emerald-600"
                                                type="radio"
                                                name="answers[{{ $question->id }}]"
                                                value="{{ $choice->id }}"
                                            >
                                            <span>{{ $choice->choice_text }}</span>
                                        </label>
                                    @endforeach
                                </div>
                            </fieldset>
                            <button class="inline-flex items-center justify-center rounded-full bg-emerald-600 px-6 py-2 text-sm font-semibold text-white shadow-sm hover:bg-emerald-700" type="submit">
                                Submit Answers
                            </button>
                        </form>
                    @endif
                    <script>
                        document.addEventListener('DOMContentLoaded', () => {
                            const timerContainer = document.querySelector('[data-quiz-timer]');
                            if (!timerContainer) {
                                return;
                            }

                            const timerText = timerContainer.querySelector('[data-quiz-timer-text]');
                            const timerBar = timerContainer.querySelector('[data-quiz-timer-bar]');
                            const totalSeconds = Number(timerContainer.dataset.durationSeconds || 0);
                            const remainingFromServer = Number(timerContainer.dataset.remainingSeconds);
                            let remainingSeconds = Number.isFinite(remainingFromServer) ? remainingFromServer : totalSeconds;
                            const quizForm = document.querySelector('#quiz-attempt-form');

                            if (!totalSeconds || !timerText || !timerBar) {
                                return;
                            }

                            let hasSubmitted = false;

                            if (quizForm) {
                                quizForm.addEventListener('submit', () => {
                                    hasSubmitted = true;
                                    quizForm.dataset.submitted = 'true';
                                });
                            }

                            const formatTime = (seconds) => {
                                const minutes = Math.floor(seconds / 60);
                                const remaining = seconds % 60;
                                return `${String(minutes).padStart(2, '0')}:${String(remaining).padStart(2, '0')}`;
                            };

                            const updateDisplay = () => {
                                const clampedSeconds = Math.max(remainingSeconds, 0);
                                timerText.textContent = formatTime(clampedSeconds);
                                const percent = Math.max((clampedSeconds / totalSeconds) * 100, 0);
                                timerBar.style.width = `${percent}%`;
                            };

                            const disableInputs = () => {
                                if (!quizForm) {
                                    return;
                                }
                                quizForm.querySelectorAll('input, button').forEach((input) => {
                                    input.disabled = true;
                                });
                            };

                            updateDisplay();

                            if (remainingSeconds <= 0) {
                                disableInputs();
                                if (quizForm && !hasSubmitted && quizForm.dataset.submitted !== 'true') {
                                    hasSubmitted = true;
                                    quizForm.submit();
                                }
                                return;
                            }

                            const intervalId = setInterval(() => {
                                remainingSeconds -= 1;
                                updateDisplay();

                                if (remainingSeconds <= 0) {
                                    clearInterval(intervalId);
                                    disableInputs();

                                    if (quizForm && !hasSubmitted && quizForm.dataset.submitted !== 'true') {
                                        hasSubmitted = true;
                                        quizForm.submit();
                                    }
                                }
                            }, 1000);
                        });
                    </script>
                @elseif ($attempt->status === 'submitted')
                    <div class="mt-6 rounded-xl border border-emerald-100 bg-emerald-50 px-4 py-3 text-sm text-emerald-700">
                        Submitted successfully. Score: <span class="font-semibold">{{ $attempt->score }}</span>
                    </div>
                @else
                    <div class="mt-6 rounded-xl border border-rose-100 bg-rose-50 px-4 py-3 text-sm text-rose-700">
                        Attempt expired.
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
            window.liveReloadChannels.push({ name: 'quiz-day.{{ $quizDay->id }}', event: 'QuizDayChanged' });
            window.liveReloadChannels.push({ name: 'leaderboard.{{ $quizDay->id }}', event: 'LeaderboardChanged' });
        </script>
    @endif
@endpush
