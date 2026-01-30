@extends('layouts.app')

@section('content')
    <div class="mx-auto flex w-full max-w-5xl flex-col gap-6 px-4 sm:px-6 lg:px-8" data-quiz-page>
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
            @if ($activeQuizRanges->count() > 1)
                <div class="rounded-2xl border border-gray-100 bg-white px-4 py-3 shadow-sm">
                    <div class="flex flex-wrap items-center gap-3 text-sm text-gray-700">
                        <span class="text-xs font-semibold uppercase tracking-[0.2em] text-amber-500">Active Quiz</span>
                        <div class="flex items-center gap-2">
                            <label class="sr-only" for="quiz-range-selector">Select quiz range</label>
                            <select
                                id="quiz-range-selector"
                                class="rounded-full border-gray-300 pr-8 text-sm font-semibold text-gray-800 focus:border-emerald-500 focus:ring-emerald-500"
                                onchange="window.location.href='{{ route('quiz.today') }}?quiz_range_id=' + this.value"
                            >
                                @foreach ($activeQuizRanges as $quizRange)
                                    <option value="{{ $quizRange->id }}" {{ $selectedQuizRange?->id === $quizRange->id ? 'selected' : '' }}>
                                        {{ $quizRange->title }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                </div>
            @endif

            <section class="grid gap-6 lg:grid-cols-[minmax(0,2fr)_minmax(0,1fr)]">
                <div class="rounded-2xl bg-white p-6 shadow-sm ring-1 ring-gray-200">
                    <div class="flex flex-wrap items-start justify-between gap-4">
                        <div>
                            <p class="text-xs font-semibold uppercase tracking-[0.2em] text-amber-500">Quiz Progress</p>
                            <h2 class="mt-2 text-lg font-semibold text-gray-900">
                                {{ $selectedQuizRange?->title ?? $quizDay->quizRange?->title ?? "Ramazan Quiz" }}
                            </h2>
                            <p class="mt-2 text-sm text-gray-600">
                                @if ($currentDayNumber)
                                    Day {{ $currentDayNumber }} of {{ $totalDays }}
                                @else
                                    {{ $totalDays }} days in this quiz range
                                @endif
                            </p>
                        </div>
                        <div class="rounded-xl bg-emerald-50 px-4 py-2 text-xs font-semibold text-emerald-700">
                            Total Days: {{ $totalDays }}
                        </div>
                    </div>

                    <div class="mt-5 flex items-center gap-3 overflow-x-auto">
                        @forelse ($daysProgress as $day)
                            @php
                                $statusClass = match ($day['status']) {
                                    'correct' => 'bg-emerald-500 text-white',
                                    'wrong' => 'bg-rose-500 text-white',
                                    'missed' => 'bg-amber-400 text-white',
                                    'today' => 'bg-emerald-100 text-emerald-700',
                                    default => 'bg-gray-100 text-gray-400',
                                };
                                $ringClass = $day['is_today'] ? 'ring-2 ring-emerald-500 ring-offset-2' : '';
                            @endphp
                            <div class="flex h-10 w-10 flex-none items-center justify-center rounded-full text-xs font-semibold {{ $statusClass }} {{ $ringClass }}">
                                {{ $day['label'] }}
                            </div>
                        @empty
                            <p class="text-sm text-gray-600">No quiz days available.</p>
                        @endforelse
                    </div>

                    <div class="mt-4 flex flex-wrap gap-4 text-xs text-gray-600">
                        <span class="flex items-center gap-2">
                            <span class="h-2 w-2 rounded-full bg-emerald-500"></span> ✓ Correct
                        </span>
                        <span class="flex items-center gap-2">
                            <span class="h-2 w-2 rounded-full bg-rose-500"></span> ✕ Wrong
                        </span>
                        <span class="flex items-center gap-2">
                            <span class="h-2 w-2 rounded-full bg-amber-400"></span> ⏳ Missed
                        </span>
                        <span class="flex items-center gap-2">
                            <span class="h-2 w-2 rounded-full bg-gray-200"></span> ○ Remaining
                        </span>
                    </div>
                </div>

                <div class="rounded-2xl bg-white p-6 shadow-sm ring-1 ring-gray-200">
                    <p class="text-xs font-semibold uppercase tracking-[0.2em] text-amber-500">Score Summary</p>
                    <div class="mt-4">
                        <p class="text-3xl font-semibold text-gray-900">{{ $currentScore }}</p>
                        <p class="mt-1 text-sm text-gray-600">
                            Current Score
                            @if ($maxPossibleScore)
                                <span class="text-gray-400">/ {{ $maxPossibleScore }}</span>
                            @endif
                        </p>
                    </div>
                    <div class="mt-6 grid grid-cols-2 gap-4 text-sm">
                        <div class="rounded-xl bg-emerald-50 px-3 py-3 text-emerald-800">
                            <p class="text-xs uppercase tracking-wide text-emerald-600">Correct</p>
                            <p class="mt-1 text-lg font-semibold">{{ $answeredCorrectCount }}</p>
                        </div>
                        <div class="rounded-xl bg-rose-50 px-3 py-3 text-rose-700">
                            <p class="text-xs uppercase tracking-wide text-rose-500">Wrong</p>
                            <p class="mt-1 text-lg font-semibold">{{ $answeredWrongCount }}</p>
                        </div>
                        <div class="rounded-xl bg-amber-50 px-3 py-3 text-amber-700">
                            <p class="text-xs uppercase tracking-wide text-amber-600">Missed</p>
                            <p class="mt-1 text-lg font-semibold">{{ $missedCount }}</p>
                        </div>
                        <div class="rounded-xl bg-gray-50 px-3 py-3 text-gray-700">
                            <p class="text-xs uppercase tracking-wide text-gray-500">Remaining</p>
                            <p class="mt-1 text-lg font-semibold">{{ $remainingCount }}</p>
                        </div>
                    </div>
                </div>
            </section>

            <section class="rounded-3xl bg-white p-6 shadow-sm ring-1 ring-gray-200">
                <div class="flex flex-wrap items-start justify-between gap-4">
                    <div>
                        <p class="text-xs font-semibold uppercase tracking-[0.2em] text-amber-500">Today's Question</p>
                        <h2 class="mt-2 text-xl font-semibold text-gray-900">{{ $quizDay->title }}</h2>
                        <p class="mt-2 text-sm text-gray-600">{{ $quizDay->start_at }} → {{ $quizDay->end_at }}</p>
                    </div>
                    <div class="flex w-full flex-col gap-3 sm:w-auto">
                        <div class="rounded-xl bg-emerald-50 px-4 py-3 text-sm text-emerald-700">
                            Duration: <span class="font-semibold">{{ $quizDay->duration_seconds }} sec</span>
                        </div>
                        @if ($attempt && $attempt->status === 'submitted')
                            <div class="rounded-xl border {{ $attempt->score > 0 ? 'border-emerald-200 bg-emerald-50 text-emerald-700' : 'border-rose-200 bg-rose-50 text-rose-700' }} px-4 py-3 text-sm font-semibold">
                                {{ $attempt->score > 0 ? 'Correct' : 'Wrong' }} · Score {{ $attempt->score }}
                            </div>
                        @endif
                    </div>
                </div>

                @if (!$attempt)
                    <div class="mt-6 rounded-2xl border border-dashed border-emerald-200 bg-emerald-50/40 px-6 py-6 text-sm text-gray-700">
                        <p class="text-base font-semibold text-gray-900">Ready for today's question?</p>
                        <p class="mt-2 text-sm text-gray-600">
                            Start your attempt to unlock the question and begin the timer.
                        </p>
                        <form class="mt-4" method="POST" action="{{ route('quiz.start', $quizDay) }}">
                            @csrf
                            <button class="inline-flex items-center justify-center rounded-full bg-emerald-600 px-6 py-2 text-sm font-semibold text-white shadow-sm hover:bg-emerald-700" type="submit">
                                Start Quiz
                            </button>
                        </form>
                    </div>
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
                            <div class="rounded-2xl border border-gray-200 bg-gray-50/60 p-5">
                                <div class="flex flex-wrap items-center justify-between gap-3">
                                    <p class="text-sm font-semibold text-gray-800">
                                        {{ $question->order_index }}. {{ $question->question_text }}
                                    </p>
                                    <span class="text-xs font-semibold uppercase tracking-wide text-gray-500">
                                        {{ $question->points }} pts
                                    </span>
                                </div>
                                <div class="mt-4 grid gap-3">
                                    @foreach ($question->choices as $choice)
                                        <label class="flex cursor-pointer items-start gap-3 rounded-2xl border border-gray-200 bg-white px-4 py-4 text-sm text-gray-700 shadow-sm transition hover:border-emerald-300 hover:bg-emerald-50/40">
                                            <input
                                                class="mt-1 h-4 w-4 text-emerald-600"
                                                type="radio"
                                                name="answers[{{ $question->id }}]"
                                                value="{{ $choice->id }}"
                                            >
                                            <span>{{ $choice->choice_text }}</span>
                                        </label>
                                    @endforeach
                                </div>
                            </div>
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
                            const overlay = document.querySelector('[data-quiz-expired-overlay]');
                            const quizPage = document.querySelector('[data-quiz-page]');
                            const redirectCountdown = overlay ? overlay.querySelector('[data-redirect-countdown]') : null;
                            const redirectSeconds = overlay ? Number(overlay.dataset.redirectSeconds || 15) : 0;
                            const redirectUrl = overlay ? overlay.dataset.redirectUrl : null;

                            if (!totalSeconds || !timerText || !timerBar) {
                                return;
                            }

                            let overlayShown = false;

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

                            // Overlay logic for expired attempts + redirect countdown.
                            const showExpiredOverlay = () => {
                                if (!overlay || overlayShown) {
                                    return;
                                }
                                overlayShown = true;
                                overlay.classList.remove('pointer-events-none', 'opacity-0');
                                overlay.classList.add('opacity-100');
                                overlay.setAttribute('aria-hidden', 'false');
                                document.body.classList.add('overflow-hidden');

                                if (quizPage) {
                                    quizPage.classList.add('pointer-events-none', 'select-none');
                                    quizPage.setAttribute('aria-hidden', 'true');
                                }

                                if (redirectCountdown) {
                                    let remainingRedirect = Number.isFinite(redirectSeconds) && redirectSeconds > 0 ? redirectSeconds : 15;
                                    redirectCountdown.textContent = remainingRedirect;

                                    const redirectInterval = setInterval(() => {
                                        remainingRedirect -= 1;
                                        redirectCountdown.textContent = Math.max(remainingRedirect, 0);

                                        if (remainingRedirect <= 0) {
                                            clearInterval(redirectInterval);
                                            if (redirectUrl) {
                                                window.location.href = redirectUrl;
                                            }
                                        }
                                    }, 1000);
                                }

                                overlay.focus({ preventScroll: true });
                            };

                            updateDisplay();

                            if (remainingSeconds <= 0) {
                                disableInputs();
                                showExpiredOverlay();
                                return;
                            }

                            const intervalId = setInterval(() => {
                                remainingSeconds -= 1;
                                updateDisplay();

                                if (remainingSeconds <= 0) {
                                    clearInterval(intervalId);
                                    disableInputs();
                                    showExpiredOverlay();
                                }
                            }, 1000);
                        });
                    </script>
                @elseif ($attempt->status === 'submitted')
                    @php
                        $isCorrect = $attempt->score > 0;
                    @endphp
                    @if (! $question)
                        <p class="mt-4 text-sm text-gray-600">No questions available.</p>
                    @else
                        <div class="mt-6 rounded-2xl border border-gray-200 bg-gray-50/60 p-5">
                            <div class="flex flex-wrap items-center justify-between gap-3">
                                <p class="text-sm font-semibold text-gray-800">
                                    {{ $question->order_index }}. {{ $question->question_text }}
                                </p>
                                <span class="text-xs font-semibold uppercase tracking-wide text-gray-500">
                                    {{ $question->points }} pts
                                </span>
                            </div>
                            <p class="mt-3 text-sm {{ $isCorrect ? 'text-emerald-700' : 'text-rose-700' }}">
                                {{ $isCorrect ? 'You answered correctly.' : 'Your answer was incorrect.' }}
                            </p>
                            <div class="mt-4 grid gap-3">
                                @foreach ($question->choices as $choice)
                                    @php
                                        $isChoiceCorrect = $correctChoiceId === $choice->id;
                                        $isChoiceSelected = $selectedChoiceId === $choice->id;
                                        $choiceClasses = 'border-gray-200 bg-white text-gray-700';
                                        if ($isChoiceCorrect) {
                                            $choiceClasses = 'border-emerald-300 bg-emerald-50 text-emerald-900';
                                        } elseif ($isChoiceSelected) {
                                            $choiceClasses = 'border-rose-300 bg-rose-50 text-rose-700';
                                        }
                                    @endphp
                                    <label class="flex items-start gap-3 rounded-2xl border px-4 py-4 text-sm shadow-sm {{ $choiceClasses }} opacity-80">
                                        <input
                                            class="mt-1 h-4 w-4 text-emerald-600"
                                            type="radio"
                                            name="answers[{{ $question->id }}]"
                                            value="{{ $choice->id }}"
                                            {{ $isChoiceSelected ? 'checked' : '' }}
                                            disabled
                                        >
                                        <div class="flex flex-1 items-start justify-between gap-3">
                                            <span>{{ $choice->choice_text }}</span>
                                            @if ($isChoiceCorrect)
                                                <span class="text-xs font-semibold uppercase tracking-wide text-emerald-600">Correct</span>
                                            @elseif ($isChoiceSelected)
                                                <span class="text-xs font-semibold uppercase tracking-wide text-rose-600">Your answer</span>
                                            @endif
                                        </div>
                                    </label>
                                @endforeach
                            </div>
                        </div>
                    @endif
                @else
                    <div class="mt-6 rounded-xl border border-rose-100 bg-rose-50 px-4 py-3 text-sm text-rose-700">
                        Attempt expired.
                    </div>
                @endif
            </section>

            <div
                class="pointer-events-none fixed inset-0 z-50 flex items-center justify-center bg-gray-900/70 opacity-0 backdrop-blur-sm transition-opacity duration-300"
                data-quiz-expired-overlay
                data-redirect-seconds="15"
                data-redirect-url="{{ route('quiz.today', ['quiz_range_id' => $quizDay->quiz_range_id]) }}"
                aria-hidden="true"
                role="dialog"
                aria-modal="true"
                tabindex="-1"
            >
                <div class="mx-4 w-full max-w-md rounded-2xl bg-white px-6 py-8 text-center shadow-xl">
                    <h2 class="text-2xl font-semibold text-gray-900">Oh no 😔</h2>
                    <p class="mt-3 text-sm text-gray-600">
                        You didn't submit the Answer on time. Try again tomorrow
                    </p>
                    <p class="mt-4 text-xs font-semibold uppercase tracking-wide text-emerald-600">
                        Redirecting in <span class="tabular-nums" data-redirect-countdown>15</span> seconds…
                    </p>
                </div>
            </div>
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
