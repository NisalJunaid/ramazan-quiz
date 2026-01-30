@extends('layouts.app')

@section('content')
    <div class="mx-auto flex w-full max-w-6xl flex-col gap-6 px-4 sm:px-6 lg:px-8">
        <header class="flex flex-wrap items-center justify-between gap-4">
            <div>
                <p class="text-xs font-semibold uppercase tracking-[0.3em] text-amber-500">Admin</p>
                <h1 class="mt-2 text-2xl font-semibold text-emerald-700">Manage Quizzes</h1>
                <p class="mt-1 text-sm text-gray-600">Create quiz days, add questions, and maintain choices.</p>
            </div>
            <a class="text-sm font-semibold text-emerald-700 hover:text-emerald-800" href="{{ route('admin.index') }}">Back to Admin</a>
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

        <section class="grid gap-6 lg:grid-cols-3">
            <div class="rounded-2xl bg-white p-6 shadow-sm ring-1 ring-gray-200 lg:col-span-2">
                <h2 class="text-lg font-semibold text-gray-900">Create Quiz Day</h2>
                <form class="mt-4 grid gap-4 sm:grid-cols-2" method="POST" action="{{ route('admin.quizzes.store') }}">
                    @csrf
                    <div>
                        <label class="text-sm font-medium text-gray-700" for="quiz_date">Quiz Date</label>
                        <input class="mt-1 w-full rounded-xl border-gray-300" type="date" id="quiz_date" name="quiz_date" value="{{ old('quiz_date') }}" required>
                    </div>
                    <div>
                        <label class="text-sm font-medium text-gray-700" for="title">Title</label>
                        <input class="mt-1 w-full rounded-xl border-gray-300" type="text" id="title" name="title" value="{{ old('title') }}" required>
                    </div>
                    <div>
                        <label class="text-sm font-medium text-gray-700" for="start_at">Start At</label>
                        <input class="mt-1 w-full rounded-xl border-gray-300" type="datetime-local" id="start_at" name="start_at" value="{{ old('start_at') }}" required>
                    </div>
                    <div>
                        <label class="text-sm font-medium text-gray-700" for="end_at">End At</label>
                        <input class="mt-1 w-full rounded-xl border-gray-300" type="datetime-local" id="end_at" name="end_at" value="{{ old('end_at') }}" required>
                    </div>
                    <div>
                        <label class="text-sm font-medium text-gray-700" for="duration_seconds">Duration (seconds)</label>
                        <input class="mt-1 w-full rounded-xl border-gray-300" type="number" id="duration_seconds" name="duration_seconds" min="1" value="{{ old('duration_seconds') }}" required>
                    </div>
                    <div class="flex items-center gap-2 pt-6">
                        <input class="h-4 w-4 rounded border-gray-300 text-emerald-600" type="checkbox" id="is_published" name="is_published" value="1" {{ old('is_published') ? 'checked' : '' }}>
                        <label class="text-sm text-gray-700" for="is_published">Published</label>
                    </div>
                    <div class="sm:col-span-2">
                        <button class="inline-flex items-center justify-center rounded-full bg-emerald-600 px-5 py-2 text-sm font-semibold text-white shadow-sm hover:bg-emerald-700" type="submit">Create Quiz Day</button>
                    </div>
                </form>
            </div>

            <div class="rounded-2xl border border-dashed border-gray-300 bg-white p-6">
                <h2 class="text-lg font-semibold text-gray-900">Admin Checklist</h2>
                <ul class="mt-3 space-y-2 text-sm text-gray-600">
                    <li>Confirm dates align with Ramazan schedule.</li>
                    <li>Keep durations consistent across quiz days.</li>
                    <li>Publish only after adding questions and choices.</li>
                </ul>
            </div>
        </section>

        <section class="grid gap-6 lg:grid-cols-2">
            <div class="rounded-2xl bg-white p-6 shadow-sm ring-1 ring-gray-200">
                <h2 class="text-lg font-semibold text-gray-900">Add Question</h2>
                <form class="mt-4 grid gap-4" method="POST" action="{{ route('admin.questions.store') }}">
                    @csrf
                    <div>
                        <label class="text-sm font-medium text-gray-700" for="question_quiz_day_id">Quiz Day</label>
                        <select class="mt-1 w-full rounded-xl border-gray-300" id="question_quiz_day_id" name="quiz_day_id" required>
                            <option value="">Select quiz day</option>
                            @foreach ($quizDays as $quizDay)
                                <option value="{{ $quizDay->id }}" {{ old('quiz_day_id') == $quizDay->id ? 'selected' : '' }}>
                                    {{ $quizDay->quiz_date }} - {{ $quizDay->title }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label class="text-sm font-medium text-gray-700" for="question_text">Question Text</label>
                        <textarea class="mt-1 w-full rounded-xl border-gray-300" id="question_text" name="question_text" rows="3" required>{{ old('question_text') }}</textarea>
                    </div>
                    <div class="grid gap-4 sm:grid-cols-2">
                        <div>
                            <label class="text-sm font-medium text-gray-700" for="question_points">Points</label>
                            <input
                                class="mt-1 w-full rounded-xl border-gray-300"
                                type="number"
                                id="question_points"
                                name="points"
                                min="1"
                                value="{{ old('points', 1) }}"
                                required
                            >
                        </div>
                        <div>
                            <label class="text-sm font-medium text-gray-700" for="question_order_index">Order Index</label>
                            <input
                                class="mt-1 w-full rounded-xl border-gray-300"
                                type="number"
                                id="question_order_index"
                                name="order_index"
                                min="1"
                                value="{{ old('order_index') }}"
                                required
                            >
                        </div>
                    </div>
                    <button class="inline-flex items-center justify-center rounded-full bg-emerald-600 px-5 py-2 text-sm font-semibold text-white shadow-sm hover:bg-emerald-700" type="submit">
                        Add Question
                    </button>
                </form>
            </div>

            <div class="rounded-2xl bg-white p-6 shadow-sm ring-1 ring-gray-200">
                <h2 class="text-lg font-semibold text-gray-900">Add Choice</h2>
                <form class="mt-4 grid gap-4" method="POST" action="{{ route('admin.choices.store') }}">
                    @csrf
                    <div>
                        <label class="text-sm font-medium text-gray-700" for="choice_question_id">Question</label>
                        <select class="mt-1 w-full rounded-xl border-gray-300" id="choice_question_id" name="question_id" required>
                            <option value="">Select question</option>
                            @foreach ($questions as $question)
                                <option value="{{ $question->id }}" {{ old('question_id') == $question->id ? 'selected' : '' }}>
                                    {{ $question->quizDay?->quiz_date }} - Q{{ $question->order_index }}:
                                    {{ \Illuminate\Support\Str::limit($question->question_text, 80) }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label class="text-sm font-medium text-gray-700" for="choice_text">Choice Text</label>
                        <input class="mt-1 w-full rounded-xl border-gray-300" type="text" id="choice_text" name="choice_text" value="{{ old('choice_text') }}" required>
                    </div>
                    <div class="grid gap-4 sm:grid-cols-2">
                        <div>
                            <label class="text-sm font-medium text-gray-700" for="choice_order_index">Order Index</label>
                            <input
                                class="mt-1 w-full rounded-xl border-gray-300"
                                type="number"
                                id="choice_order_index"
                                name="order_index"
                                min="1"
                                value="{{ old('order_index') }}"
                                required
                            >
                        </div>
                        <div class="flex items-center gap-2 pt-6">
                            <input class="h-4 w-4 rounded border-gray-300 text-emerald-600" type="checkbox" id="is_correct" name="is_correct" value="1" {{ old('is_correct') ? 'checked' : '' }}>
                            <label class="text-sm text-gray-700" for="is_correct">Mark as correct</label>
                        </div>
                    </div>
                    <button class="inline-flex items-center justify-center rounded-full bg-emerald-600 px-5 py-2 text-sm font-semibold text-white shadow-sm hover:bg-emerald-700" type="submit">
                        Add Choice
                    </button>
                </form>
            </div>
        </section>

        <section class="rounded-2xl bg-white p-6 shadow-sm ring-1 ring-gray-200">
            <div class="flex flex-wrap items-center justify-between gap-3">
                <h2 class="text-lg font-semibold text-gray-900">Existing Quiz Days</h2>
                <p class="text-sm text-gray-500">Update details before submissions arrive.</p>
            </div>
            @if ($quizDays->isEmpty())
                <p class="mt-4 text-sm text-gray-600">No quiz days created yet.</p>
            @else
                <div class="mt-6 overflow-x-auto">
                    <table class="min-w-full text-sm">
                        <thead class="border-b border-gray-200 text-left text-xs uppercase tracking-widest text-gray-500">
                            <tr>
                                <th class="px-4 py-3">Date</th>
                                <th class="px-4 py-3">Title</th>
                                <th class="px-4 py-3">Window</th>
                                <th class="px-4 py-3">Duration</th>
                                <th class="px-4 py-3">Published</th>
                                <th class="px-4 py-3">Update</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100">
                            @foreach ($quizDays as $quizDay)
                                <tr class="align-top">
                                    <td class="px-4 py-3 font-medium text-gray-900">{{ $quizDay->quiz_date }}</td>
                                    <td class="px-4 py-3 text-gray-700">{{ $quizDay->title }}</td>
                                    <td class="px-4 py-3 text-gray-600">{{ $quizDay->start_at }} - {{ $quizDay->end_at }}</td>
                                    <td class="px-4 py-3 text-gray-600">{{ $quizDay->duration_seconds }} sec</td>
                                    <td class="px-4 py-3 text-gray-700">{{ $quizDay->is_published ? 'Yes' : 'No' }}</td>
                                    <td class="px-4 py-3">
                                        <form class="grid gap-3" method="POST" action="{{ route('admin.quizzes.update', $quizDay->id) }}">
                                            @csrf
                                            @method('PUT')
                                            <div>
                                                <label class="text-xs font-medium text-gray-700" for="quiz_date_{{ $quizDay->id }}">Quiz Date</label>
                                                <input
                                                    class="mt-1 w-full rounded-xl border-gray-300"
                                                    type="date"
                                                    id="quiz_date_{{ $quizDay->id }}"
                                                    name="quiz_date"
                                                    value="{{ $quizDay->quiz_date }}"
                                                    required
                                                >
                                            </div>
                                            <div>
                                                <label class="text-xs font-medium text-gray-700" for="title_{{ $quizDay->id }}">Title</label>
                                                <input
                                                    class="mt-1 w-full rounded-xl border-gray-300"
                                                    type="text"
                                                    id="title_{{ $quizDay->id }}"
                                                    name="title"
                                                    value="{{ $quizDay->title }}"
                                                    required
                                                >
                                            </div>
                                            <div>
                                                <label class="text-xs font-medium text-gray-700" for="start_at_{{ $quizDay->id }}">Start At</label>
                                                <input
                                                    class="mt-1 w-full rounded-xl border-gray-300"
                                                    type="datetime-local"
                                                    id="start_at_{{ $quizDay->id }}"
                                                    name="start_at"
                                                    value="{{ \Illuminate\Support\Carbon::parse($quizDay->start_at)->format('Y-m-d\TH:i') }}"
                                                    required
                                                >
                                            </div>
                                            <div>
                                                <label class="text-xs font-medium text-gray-700" for="end_at_{{ $quizDay->id }}">End At</label>
                                                <input
                                                    class="mt-1 w-full rounded-xl border-gray-300"
                                                    type="datetime-local"
                                                    id="end_at_{{ $quizDay->id }}"
                                                    name="end_at"
                                                    value="{{ \Illuminate\Support\Carbon::parse($quizDay->end_at)->format('Y-m-d\TH:i') }}"
                                                    required
                                                >
                                            </div>
                                            <div>
                                                <label class="text-xs font-medium text-gray-700" for="duration_seconds_{{ $quizDay->id }}">Duration (seconds)</label>
                                                <input
                                                    class="mt-1 w-full rounded-xl border-gray-300"
                                                    type="number"
                                                    id="duration_seconds_{{ $quizDay->id }}"
                                                    name="duration_seconds"
                                                    min="1"
                                                    value="{{ $quizDay->duration_seconds }}"
                                                    required
                                                >
                                            </div>
                                            <div class="flex items-center gap-2">
                                                <input
                                                    class="h-4 w-4 rounded border-gray-300 text-emerald-600"
                                                    type="checkbox"
                                                    id="is_published_{{ $quizDay->id }}"
                                                    name="is_published"
                                                    value="1"
                                                    {{ $quizDay->is_published ? 'checked' : '' }}
                                                >
                                                <label class="text-xs text-gray-700" for="is_published_{{ $quizDay->id }}">Published</label>
                                            </div>
                                            <button class="inline-flex items-center justify-center rounded-full bg-emerald-600 px-4 py-1.5 text-xs font-semibold text-white hover:bg-emerald-700" type="submit">Update</button>
                                        </form>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @endif
        </section>
    </div>
@endsection
