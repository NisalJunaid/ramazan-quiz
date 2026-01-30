@extends('layouts.app')

@section('content')
    <div class="mx-auto flex w-full max-w-5xl flex-col gap-6 px-4 sm:px-6 lg:px-8">
        <header class="flex flex-wrap items-center justify-between gap-4">
            <div>
                <p class="text-xs font-semibold uppercase tracking-[0.3em] text-amber-500">Admin</p>
                <h1 class="mt-2 text-2xl font-semibold text-emerald-700">Edit {{ $quizDay->quiz_date }}</h1>
                <p class="mt-1 text-sm text-gray-600">{{ $quizRange->title }}</p>
            </div>
            <a class="text-sm font-semibold text-emerald-700 hover:text-emerald-800" href="{{ route('admin.quizzes.days', $quizRange) }}">Back to Days</a>
        </header>

        @if (session('status'))
            <div class="rounded-2xl border border-emerald-100 bg-emerald-50 px-4 py-3 text-sm text-emerald-700">
                {{ session('status') }}
            </div>
        @endif

        @if ($errors->any())
            <div class="rounded-2xl border border-rose-100 bg-rose-50 px-4 py-3 text-sm text-rose-700">
                <p class="font-semibold">There were problems with your submission:</p>
                <ul class="mt-2 list-disc space-y-1 pl-5">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        @if ($hasSubmittedAttempts)
            <div class="rounded-2xl border border-amber-200 bg-amber-50 px-4 py-3 text-sm text-amber-800">
                This day has submitted attempts. Editing is locked.
            </div>
        @endif

        <section class="rounded-2xl bg-white p-6 shadow-sm ring-1 ring-gray-200">
            <h2 class="text-lg font-semibold text-gray-900">Question</h2>
            <form class="mt-4 grid gap-4" method="POST" action="{{ route('admin.questions.update', $quizDay) }}">
                @csrf
                @method('PUT')
                <div>
                    <label class="text-sm font-medium text-gray-700" for="question_text">Question Text</label>
                    <textarea class="mt-1 w-full rounded-xl border-gray-300" id="question_text" name="question_text" rows="3" {{ $hasSubmittedAttempts ? 'disabled' : '' }} required>{{ old('question_text', $quizDay->question?->question_text) }}</textarea>
                </div>
                <div class="grid gap-4 sm:grid-cols-2">
                    <div>
                        <label class="text-sm font-medium text-gray-700" for="points">Points</label>
                        <input class="mt-1 w-full rounded-xl border-gray-300" type="number" id="points" name="points" min="1" value="{{ old('points', $quizDay->question?->points ?? 1) }}" {{ $hasSubmittedAttempts ? 'disabled' : '' }} required>
                    </div>
                    <div>
                        <label class="text-sm font-medium text-gray-700" for="order_index">Order Index</label>
                        <input class="mt-1 w-full rounded-xl border-gray-300" type="number" id="order_index" name="order_index" min="1" value="{{ old('order_index', $quizDay->question?->order_index ?? 1) }}" {{ $hasSubmittedAttempts ? 'disabled' : '' }} required>
                    </div>
                </div>
                <button class="inline-flex items-center justify-center rounded-full bg-emerald-600 px-6 py-2 text-sm font-semibold text-white shadow-sm hover:bg-emerald-700 disabled:opacity-60" type="submit" {{ $hasSubmittedAttempts ? 'disabled' : '' }}>
                    Update Question
                </button>
            </form>
        </section>

        <section class="rounded-2xl bg-white p-6 shadow-sm ring-1 ring-gray-200">
            <h2 class="text-lg font-semibold text-gray-900">Choices</h2>
            <p class="mt-1 text-sm text-gray-600">Mark exactly one answer as correct.</p>

            <div class="mt-4 space-y-4">
                @forelse ($quizDay->question?->choices ?? [] as $choice)
                    <form class="grid gap-4 rounded-2xl border border-gray-200 bg-gray-50/40 p-4" method="POST" action="{{ route('admin.choices.update', $choice) }}">
                        @csrf
                        @method('PUT')
                        <div>
                            <label class="text-xs font-medium text-gray-700" for="choice_text_{{ $choice->id }}">Choice Text</label>
                            <input class="mt-1 w-full rounded-xl border-gray-300" type="text" id="choice_text_{{ $choice->id }}" name="choice_text" value="{{ old('choice_text', $choice->choice_text) }}" {{ $hasSubmittedAttempts ? 'disabled' : '' }} required>
                        </div>
                        <div class="grid gap-4 sm:grid-cols-2">
                            <div>
                                <label class="text-xs font-medium text-gray-700" for="order_index_{{ $choice->id }}">Order Index</label>
                                <input class="mt-1 w-full rounded-xl border-gray-300" type="number" id="order_index_{{ $choice->id }}" name="order_index" min="1" value="{{ old('order_index', $choice->order_index) }}" {{ $hasSubmittedAttempts ? 'disabled' : '' }} required>
                            </div>
                            <div class="flex items-center gap-2 pt-6">
                                <input class="h-4 w-4 rounded border-gray-300 text-emerald-600" type="checkbox" id="is_correct_{{ $choice->id }}" name="is_correct" value="1" {{ $choice->is_correct ? 'checked' : '' }} {{ $hasSubmittedAttempts ? 'disabled' : '' }}>
                                <label class="text-xs text-gray-700" for="is_correct_{{ $choice->id }}">Correct answer</label>
                            </div>
                        </div>
                        <button class="inline-flex items-center justify-center rounded-full bg-emerald-600 px-4 py-1.5 text-xs font-semibold text-white hover:bg-emerald-700 disabled:opacity-60" type="submit" {{ $hasSubmittedAttempts ? 'disabled' : '' }}>
                            Update Choice
                        </button>
                    </form>
                @empty
                    <p class="text-sm text-gray-600">No choices yet. Add the first choice below.</p>
                @endforelse
            </div>

            <form class="mt-6 grid gap-4 rounded-2xl border border-dashed border-gray-200 p-4" method="POST" action="{{ route('admin.choices.store') }}">
                @csrf
                <input type="hidden" name="question_id" value="{{ $quizDay->question?->id }}">
                <div>
                    <label class="text-xs font-medium text-gray-700" for="new_choice_text">Choice Text</label>
                    <input class="mt-1 w-full rounded-xl border-gray-300" type="text" id="new_choice_text" name="choice_text" value="{{ old('choice_text') }}" {{ $hasSubmittedAttempts ? 'disabled' : '' }} required>
                </div>
                <div class="grid gap-4 sm:grid-cols-2">
                    <div>
                        <label class="text-xs font-medium text-gray-700" for="new_order_index">Order Index</label>
                        <input class="mt-1 w-full rounded-xl border-gray-300" type="number" id="new_order_index" name="order_index" min="1" value="{{ old('order_index') }}" {{ $hasSubmittedAttempts ? 'disabled' : '' }} required>
                    </div>
                    <div class="flex items-center gap-2 pt-6">
                        <input class="h-4 w-4 rounded border-gray-300 text-emerald-600" type="checkbox" id="new_is_correct" name="is_correct" value="1" {{ old('is_correct') ? 'checked' : '' }} {{ $hasSubmittedAttempts ? 'disabled' : '' }}>
                        <label class="text-xs text-gray-700" for="new_is_correct">Correct answer</label>
                    </div>
                </div>
                <button class="inline-flex items-center justify-center rounded-full bg-emerald-600 px-4 py-1.5 text-xs font-semibold text-white hover:bg-emerald-700 disabled:opacity-60" type="submit" {{ $hasSubmittedAttempts ? 'disabled' : '' }}>
                    Add Choice
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
