@extends('layouts.app')

@section('content')
    <div class="mx-auto flex w-full max-w-5xl flex-col gap-6 px-4 sm:px-6 lg:px-8">
        <header class="flex flex-wrap items-center justify-between gap-4">
            <div>
                <p class="text-xs font-semibold uppercase tracking-[0.3em] text-amber-500">{{ text('admin.day_edit.overline', 'Admin') }}</p>
                <h1 class="mt-2 text-2xl font-semibold text-emerald-700">{{ str_replace(':date', $quizDay->quiz_date, text('admin.day_edit.title', 'Edit :date')) }}</h1>
                <p class="mt-1 text-sm text-gray-600">{{ $quizRange->title }}</p>
            </div>
            <a class="text-sm font-semibold text-emerald-700 hover:text-emerald-800" href="{{ route('admin.quizzes.days', $quizRange) }}">
                {{ text('admin.day_edit.back', 'Back to Days') }}
            </a>
        </header>

        @if (session('status'))
            <div class="rounded-2xl border border-emerald-100 bg-emerald-50 px-4 py-3 text-sm text-emerald-700">
                {{ session('status') }}
            </div>
        @endif

        @if ($errors->any())
            <div class="rounded-2xl border border-rose-100 bg-rose-50 px-4 py-3 text-sm text-rose-700">
                <p class="font-semibold">{{ text('admin.day_edit.errors.title', 'There were problems with your submission:') }}</p>
                <ul class="mt-2 list-disc space-y-1 ps-5">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        @if ($hasSubmittedAttempts)
            <div class="rounded-2xl border border-amber-200 bg-amber-50 px-4 py-3 text-sm text-amber-800">
                {{ text('admin.day_edit.locked', 'This day has submitted attempts. Editing is locked.') }}
            </div>
        @endif

        @php
            $choices = $quizDay->question?->choices?->sortBy('order_index')->values() ?? collect();
            $correctIndex = null;

            foreach ($choices as $index => $choice) {
                if ($choice->is_correct) {
                    $correctIndex = $index;
                    break;
                }
            }

            $selectedCorrect = old('correct_answer', $correctIndex);
        @endphp

        <section class="rounded-2xl bg-white p-6 shadow-sm ring-1 ring-gray-200">
            <h2 class="text-lg font-semibold text-gray-900">{{ text('admin.day_edit.section.title', 'Question & Answers') }}</h2>
            <form class="mt-4 grid gap-6" method="POST" action="{{ route('admin.questions.save', $quizDay) }}">
                @csrf
                @method('PUT')
                <div>
                    <label class="text-sm font-medium text-gray-700" for="question_text">{{ text('admin.day_edit.question.label', 'Question Text') }}</label>
                    <textarea class="mt-1 w-full rounded-xl border-gray-300" id="question_text" name="question_text" rows="3" {{ $hasSubmittedAttempts ? 'disabled' : '' }} required>{{ old('question_text', $quizDay->question?->question_text) }}</textarea>
                </div>
                <div class="grid gap-4 sm:grid-cols-2">
                    <div>
                        <label class="text-sm font-medium text-gray-700" for="points">{{ text('admin.day_edit.question.points', 'Points') }}</label>
                        <input class="mt-1 w-full rounded-xl border-gray-300" type="number" id="points" name="points" min="1" value="{{ old('points', $quizDay->question?->points ?? 1) }}" {{ $hasSubmittedAttempts ? 'disabled' : '' }} required>
                    </div>
                    <div>
                        <label class="text-sm font-medium text-gray-700" for="order_index">{{ text('admin.day_edit.question.order', 'Order Index') }}</label>
                        <input class="mt-1 w-full rounded-xl border-gray-300" type="number" id="order_index" name="order_index" min="1" value="{{ old('order_index', $quizDay->question?->order_index ?? 1) }}" {{ $hasSubmittedAttempts ? 'disabled' : '' }} required>
                    </div>
                </div>

                <div>
                    <h3 class="text-sm font-semibold text-gray-900">{{ text('admin.day_edit.answers.title', 'Answers') }}</h3>
                    <p class="mt-1 text-sm text-gray-600">{{ text('admin.day_edit.answers.subtitle', 'Select exactly one correct answer.') }}</p>
                    <div class="mt-4 grid gap-4 sm:grid-cols-2">
                        @for ($index = 0; $index < 4; $index++)
                            @php
                                $choice = $choices->get($index);
                            @endphp
                            <div class="rounded-2xl border border-gray-200 bg-gray-50/40 p-4">
                                <label class="text-xs font-medium text-gray-700" for="answer_text_{{ $index }}">
                                    {{ str_replace(':number', $index + 1, text('admin.day_edit.answers.answer_label', 'Answer :number')) }}
                                </label>
                                <input class="mt-1 w-full rounded-xl border-gray-300" type="text" id="answer_text_{{ $index }}" name="answers[{{ $index }}][text]" value="{{ old("answers.$index.text", $choice?->choice_text) }}" {{ $hasSubmittedAttempts ? 'disabled' : '' }} required>
                                <div class="mt-3 flex items-center gap-2">
                                    <input class="h-4 w-4 rounded border-gray-300 text-emerald-600" type="radio" id="correct_answer_{{ $index }}" name="correct_answer" value="{{ $index }}" {{ (string) $selectedCorrect === (string) $index ? 'checked' : '' }} {{ $hasSubmittedAttempts ? 'disabled' : '' }} required>
                                    <label class="text-xs text-gray-700" for="correct_answer_{{ $index }}">{{ text('admin.day_edit.answers.correct', 'Correct answer') }}</label>
                                </div>
                            </div>
                        @endfor
                    </div>
                </div>

                <button class="inline-flex items-center justify-center rounded-full bg-emerald-600 px-6 py-2 text-sm font-semibold text-white shadow-sm hover:bg-emerald-700 disabled:opacity-60" type="submit" {{ $hasSubmittedAttempts ? 'disabled' : '' }}>
                    {{ text('admin.day_edit.save', 'Save Question & Answers') }}
                </button>
            </form>
        </section>
    </div>
@endsection

@push('live-reload')
    <script>
        window.liveReloadChannels = window.liveReloadChannels || [];
        window.liveReloadChannels.push({ name: 'quiz-day.{{ $quizDay->id }}', event: 'QuizDayChanged' });
    </script>
@endpush
