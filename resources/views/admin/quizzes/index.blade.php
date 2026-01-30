@extends('layouts.app')

@section('content')
    <h1 class="text-2xl font-semibold">Manage Quizzes</h1>
    <p class="mt-2"><a class="text-blue-600 hover:underline" href="{{ route('admin.dashboard') }}">Back to Admin</a></p>

    @if (session('status'))
        <p class="mt-4 rounded bg-green-50 p-3 text-sm text-green-700">{{ session('status') }}</p>
    @endif

    @if ($errors->any())
        <div class="mt-4 rounded bg-red-50 p-3 text-sm text-red-700">
            <p class="font-semibold">There were problems with your submission:</p>
            <ul class="list-disc pl-5">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <section class="mt-6 rounded-lg border border-gray-200 bg-white p-4">
        <h2 class="text-lg font-semibold">Create Quiz Day</h2>
        <form class="mt-4 grid gap-4 sm:grid-cols-2" method="POST" action="{{ route('admin.quizzes.store') }}">
            @csrf
            <div>
                <label class="text-sm font-medium text-gray-700" for="quiz_date">Quiz Date</label>
                <input class="mt-1 w-full rounded border-gray-300" type="date" id="quiz_date" name="quiz_date" value="{{ old('quiz_date') }}" required>
            </div>
            <div>
                <label class="text-sm font-medium text-gray-700" for="title">Title</label>
                <input class="mt-1 w-full rounded border-gray-300" type="text" id="title" name="title" value="{{ old('title') }}" required>
            </div>
            <div>
                <label class="text-sm font-medium text-gray-700" for="start_at">Start At</label>
                <input class="mt-1 w-full rounded border-gray-300" type="datetime-local" id="start_at" name="start_at" value="{{ old('start_at') }}" required>
            </div>
            <div>
                <label class="text-sm font-medium text-gray-700" for="end_at">End At</label>
                <input class="mt-1 w-full rounded border-gray-300" type="datetime-local" id="end_at" name="end_at" value="{{ old('end_at') }}" required>
            </div>
            <div>
                <label class="text-sm font-medium text-gray-700" for="duration_seconds">Duration (seconds)</label>
                <input class="mt-1 w-full rounded border-gray-300" type="number" id="duration_seconds" name="duration_seconds" min="1" value="{{ old('duration_seconds') }}" required>
            </div>
            <div class="flex items-center gap-2 pt-6">
                <input class="rounded border-gray-300" type="checkbox" id="is_published" name="is_published" value="1" {{ old('is_published') ? 'checked' : '' }}>
                <label class="text-sm text-gray-700" for="is_published">Published</label>
            </div>
            <div class="sm:col-span-2">
                <button class="rounded bg-blue-600 px-4 py-2 text-white" type="submit">Create Quiz Day</button>
            </div>
        </form>
    </section>

    <section class="mt-6 rounded-lg border border-gray-200 bg-white p-4">
        <h2 class="text-lg font-semibold">Add Question</h2>
        <form class="mt-4 grid gap-4 sm:grid-cols-2" method="POST" action="{{ route('admin.questions.store') }}">
            @csrf
            <div class="sm:col-span-2">
                <label class="text-sm font-medium text-gray-700" for="question_quiz_day_id">Quiz Day</label>
                <select class="mt-1 w-full rounded border-gray-300" id="question_quiz_day_id" name="quiz_day_id" required>
                    <option value="">Select quiz day</option>
                    @foreach ($quizDays as $quizDay)
                        <option value="{{ $quizDay->id }}" {{ old('quiz_day_id') == $quizDay->id ? 'selected' : '' }}>
                            {{ $quizDay->quiz_date }} - {{ $quizDay->title }}
                        </option>
                    @endforeach
                </select>
            </div>
            <div class="sm:col-span-2">
                <label class="text-sm font-medium text-gray-700" for="question_text">Question Text</label>
                <textarea class="mt-1 w-full rounded border-gray-300" id="question_text" name="question_text" rows="3" required>{{ old('question_text') }}</textarea>
            </div>
            <div>
                <label class="text-sm font-medium text-gray-700" for="question_points">Points</label>
                <input
                    class="mt-1 w-full rounded border-gray-300"
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
                    class="mt-1 w-full rounded border-gray-300"
                    type="number"
                    id="question_order_index"
                    name="order_index"
                    min="1"
                    value="{{ old('order_index') }}"
                    required
                >
            </div>
            <div class="sm:col-span-2">
                <button class="rounded bg-blue-600 px-4 py-2 text-white" type="submit">Add Question</button>
            </div>
        </form>
    </section>

    <section class="mt-6 rounded-lg border border-gray-200 bg-white p-4">
        <h2 class="text-lg font-semibold">Add Choice</h2>
        <form class="mt-4 grid gap-4 sm:grid-cols-2" method="POST" action="{{ route('admin.choices.store') }}">
            @csrf
            <div class="sm:col-span-2">
                <label class="text-sm font-medium text-gray-700" for="choice_question_id">Question</label>
                <select class="mt-1 w-full rounded border-gray-300" id="choice_question_id" name="question_id" required>
                    <option value="">Select question</option>
                    @foreach ($questions as $question)
                        <option value="{{ $question->id }}" {{ old('question_id') == $question->id ? 'selected' : '' }}>
                            {{ $question->quizDay?->quiz_date }} - Q{{ $question->order_index }}:
                            {{ \Illuminate\Support\Str::limit($question->question_text, 80) }}
                        </option>
                    @endforeach
                </select>
            </div>
            <div class="sm:col-span-2">
                <label class="text-sm font-medium text-gray-700" for="choice_text">Choice Text</label>
                <input class="mt-1 w-full rounded border-gray-300" type="text" id="choice_text" name="choice_text" value="{{ old('choice_text') }}" required>
            </div>
            <div>
                <label class="text-sm font-medium text-gray-700" for="choice_order_index">Order Index</label>
                <input
                    class="mt-1 w-full rounded border-gray-300"
                    type="number"
                    id="choice_order_index"
                    name="order_index"
                    min="1"
                    value="{{ old('order_index') }}"
                    required
                >
            </div>
            <div class="flex items-center gap-2 pt-6">
                <input class="rounded border-gray-300" type="checkbox" id="is_correct" name="is_correct" value="1" {{ old('is_correct') ? 'checked' : '' }}>
                <label class="text-sm text-gray-700" for="is_correct">Mark as correct</label>
            </div>
            <div class="sm:col-span-2">
                <button class="rounded bg-blue-600 px-4 py-2 text-white" type="submit">Add Choice</button>
            </div>
        </form>
    </section>

    <section class="mt-6 rounded-lg border border-gray-200 bg-white p-4">
        <h2 class="text-lg font-semibold">Existing Quiz Days</h2>
        @if ($quizDays->isEmpty())
            <p class="mt-4 text-gray-600">No quiz days created yet.</p>
        @else
            <div class="mt-4 overflow-x-auto">
                <table class="min-w-full border border-gray-200 text-sm">
                    <thead class="bg-gray-50 text-left">
                        <tr>
                            <th class="border border-gray-200 px-3 py-2">Date</th>
                            <th class="border border-gray-200 px-3 py-2">Title</th>
                            <th class="border border-gray-200 px-3 py-2">Window</th>
                            <th class="border border-gray-200 px-3 py-2">Duration</th>
                            <th class="border border-gray-200 px-3 py-2">Published</th>
                            <th class="border border-gray-200 px-3 py-2">Update</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($quizDays as $quizDay)
                            <tr class="align-top">
                                <td class="border border-gray-200 px-3 py-2">{{ $quizDay->quiz_date }}</td>
                                <td class="border border-gray-200 px-3 py-2">{{ $quizDay->title }}</td>
                                <td class="border border-gray-200 px-3 py-2">{{ $quizDay->start_at }} - {{ $quizDay->end_at }}</td>
                                <td class="border border-gray-200 px-3 py-2">{{ $quizDay->duration_seconds }} sec</td>
                                <td class="border border-gray-200 px-3 py-2">{{ $quizDay->is_published ? 'Yes' : 'No' }}</td>
                                <td class="border border-gray-200 px-3 py-2">
                                    <form class="grid gap-3" method="POST" action="{{ route('admin.quizzes.update', $quizDay->id) }}">
                                        @csrf
                                        @method('PUT')
                                        <div>
                                            <label class="text-xs font-medium text-gray-700" for="quiz_date_{{ $quizDay->id }}">Quiz Date</label>
                                            <input
                                                class="mt-1 w-full rounded border-gray-300"
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
                                                class="mt-1 w-full rounded border-gray-300"
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
                                                class="mt-1 w-full rounded border-gray-300"
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
                                                class="mt-1 w-full rounded border-gray-300"
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
                                                class="mt-1 w-full rounded border-gray-300"
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
                                                class="rounded border-gray-300"
                                                type="checkbox"
                                                id="is_published_{{ $quizDay->id }}"
                                                name="is_published"
                                                value="1"
                                                {{ $quizDay->is_published ? 'checked' : '' }}
                                            >
                                            <label class="text-xs text-gray-700" for="is_published_{{ $quizDay->id }}">Published</label>
                                        </div>
                                        <button class="rounded bg-gray-900 px-3 py-1 text-xs text-white" type="submit">Update</button>
                                    </form>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @endif
    </section>
@endsection
