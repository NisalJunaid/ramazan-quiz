@extends('layouts.app')

@section('content')
    <div class="mx-auto flex w-full max-w-6xl flex-col gap-6 px-4 sm:px-6 lg:px-8">
        <header class="flex flex-wrap items-center justify-between gap-4">
            <div>
                <p class="text-xs font-semibold uppercase tracking-[0.3em] text-amber-500">Admin</p>
                <h1 class="mt-2 text-2xl font-semibold text-emerald-700">Manage Quiz Ranges</h1>
                <p class="mt-1 text-sm text-gray-600">Create quiz ranges and manage daily questions.</p>
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
                <h2 class="text-lg font-semibold text-gray-900">Create Quiz Range</h2>
                <form class="mt-4 grid gap-4 sm:grid-cols-2" method="POST" action="{{ route('admin.quizzes.store') }}">
                    @csrf
                    <div>
                        <label class="text-sm font-medium text-gray-700" for="title">Title</label>
                        <input class="mt-1 w-full rounded-xl border-gray-300" type="text" id="title" name="title" value="{{ old('title') }}" required>
                    </div>
                    <div>
                        <label class="text-sm font-medium text-gray-700" for="duration_seconds">Duration (seconds)</label>
                        <input class="mt-1 w-full rounded-xl border-gray-300" type="number" id="duration_seconds" name="duration_seconds" min="1" value="{{ old('duration_seconds') }}" required>
                    </div>
                    <div>
                        <label class="text-sm font-medium text-gray-700" for="start_date">Start Date</label>
                        <input class="mt-1 w-full rounded-xl border-gray-300" type="date" id="start_date" name="start_date" value="{{ old('start_date') }}" required>
                    </div>
                    <div>
                        <label class="text-sm font-medium text-gray-700" for="end_date">End Date</label>
                        <input class="mt-1 w-full rounded-xl border-gray-300" type="date" id="end_date" name="end_date" value="{{ old('end_date') }}" required>
                    </div>
                    <div class="flex items-center gap-2 pt-6">
                        <input class="h-4 w-4 rounded border-gray-300 text-emerald-600" type="checkbox" id="is_published" name="is_published" value="1" {{ old('is_published') ? 'checked' : '' }}>
                        <label class="text-sm text-gray-700" for="is_published">Published</label>
                    </div>
                    <div class="flex items-center gap-2 pt-6">
                        <input class="h-4 w-4 rounded border-gray-300 text-emerald-600" type="checkbox" id="is_visible" name="is_visible" value="1" {{ old('is_visible', true) ? 'checked' : '' }}>
                        <label class="text-sm text-gray-700" for="is_visible">Visible</label>
                    </div>
                    <div class="flex items-center gap-2 pt-6">
                        <input class="h-4 w-4 rounded border-gray-300 text-emerald-600" type="checkbox" id="leaderboard_is_public" name="leaderboard_is_public" value="1" {{ old('leaderboard_is_public', true) ? 'checked' : '' }}>
                        <label class="text-sm text-gray-700" for="leaderboard_is_public">Show leaderboard to users</label>
                    </div>
                    <div class="sm:col-span-2">
                        <button class="inline-flex items-center justify-center rounded-full bg-emerald-600 px-5 py-2 text-sm font-semibold text-white shadow-sm hover:bg-emerald-700" type="submit">Create Quiz Range</button>
                    </div>
                </form>
            </div>

            <div class="rounded-2xl border border-dashed border-gray-300 bg-white p-6">
                <h2 class="text-lg font-semibold text-gray-900">Admin Checklist</h2>
                <ul class="mt-3 space-y-2 text-sm text-gray-600">
                    <li>Confirm dates align with Ramazan schedule.</li>
                    <li>Review daily questions before publishing.</li>
                    <li>Publish only after completing choices.</li>
                </ul>
            </div>
        </section>

        <section class="rounded-2xl bg-white p-6 shadow-sm ring-1 ring-gray-200">
            <div class="flex flex-wrap items-center justify-between gap-3">
                <h2 class="text-lg font-semibold text-gray-900">Existing Quiz Ranges</h2>
                <p class="text-sm text-gray-500">Manage days and publish status.</p>
            </div>
            @if ($quizRanges->isEmpty())
                <p class="mt-4 text-sm text-gray-600">No quiz ranges created yet.</p>
            @else
                <div class="mt-6 overflow-x-auto">
                    <table class="min-w-full text-sm">
                        <thead class="border-b border-gray-200 text-left text-xs uppercase tracking-widest text-gray-500">
                            <tr>
                                <th class="px-4 py-3">Range</th>
                                <th class="px-4 py-3">Days</th>
                                <th class="px-4 py-3">Duration</th>
                                <th class="px-4 py-3">Published</th>
                                <th class="px-4 py-3">Visible</th>
                                <th class="px-4 py-3">Leaderboard</th>
                                <th class="px-4 py-3">Manage</th>
                                <th class="px-4 py-3">Update</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100">
                            @foreach ($quizRanges as $quizRange)
                                <tr class="align-top">
                                    <td class="px-4 py-3 font-medium text-gray-900">
                                        <div>{{ $quizRange->title }}</div>
                                        <div class="text-xs text-gray-500">{{ $quizRange->start_date }} → {{ $quizRange->end_date }}</div>
                                    </td>
                                    <td class="px-4 py-3 text-gray-600">{{ $quizRange->days_count }}</td>
                                    <td class="px-4 py-3 text-gray-600">{{ $quizRange->duration_seconds }} sec</td>
                                    <td class="px-4 py-3 text-gray-700">{{ $quizRange->is_published ? 'Yes' : 'No' }}</td>
                                    <td class="px-4 py-3 text-gray-700">{{ $quizRange->is_visible ? 'Yes' : 'No' }}</td>
                                    <td class="px-4 py-3 text-gray-700">{{ $quizRange->leaderboard_is_public ? 'Public' : 'Admin only' }}</td>
                                    <td class="px-4 py-3">
                                        <a class="text-sm font-semibold text-emerald-700 hover:text-emerald-800" href="{{ route('admin.quizzes.days', $quizRange) }}">
                                            Manage Days →
                                        </a>
                                    </td>
                                    <td class="px-4 py-3">
                                        <form class="grid gap-3" method="POST" action="{{ route('admin.quizzes.update', $quizRange->id) }}">
                                            @csrf
                                            @method('PUT')
                                            <div>
                                                <label class="text-xs font-medium text-gray-700" for="title_{{ $quizRange->id }}">Title</label>
                                                <input
                                                    class="mt-1 w-full rounded-xl border-gray-300"
                                                    type="text"
                                                    id="title_{{ $quizRange->id }}"
                                                    name="title"
                                                    value="{{ $quizRange->title }}"
                                                    required
                                                >
                                            </div>
                                            <div>
                                                <label class="text-xs font-medium text-gray-700" for="duration_seconds_{{ $quizRange->id }}">Duration (seconds)</label>
                                                <input
                                                    class="mt-1 w-full rounded-xl border-gray-300"
                                                    type="number"
                                                    id="duration_seconds_{{ $quizRange->id }}"
                                                    name="duration_seconds"
                                                    min="1"
                                                    value="{{ $quizRange->duration_seconds }}"
                                                    required
                                                >
                                            </div>
                                            <div class="flex items-center gap-2">
                                                <input
                                                    class="h-4 w-4 rounded border-gray-300 text-emerald-600"
                                                    type="checkbox"
                                                    id="is_published_{{ $quizRange->id }}"
                                                    name="is_published"
                                                    value="1"
                                                    {{ $quizRange->is_published ? 'checked' : '' }}
                                                >
                                                <label class="text-xs text-gray-700" for="is_published_{{ $quizRange->id }}">Published</label>
                                            </div>
                                            <div class="flex items-center gap-2">
                                                <input
                                                    class="h-4 w-4 rounded border-gray-300 text-emerald-600"
                                                    type="checkbox"
                                                    id="is_visible_{{ $quizRange->id }}"
                                                    name="is_visible"
                                                    value="1"
                                                    {{ $quizRange->is_visible ? 'checked' : '' }}
                                                >
                                                <label class="text-xs text-gray-700" for="is_visible_{{ $quizRange->id }}">Visible</label>
                                            </div>
                                            <div class="flex items-center gap-2">
                                                <input
                                                    class="h-4 w-4 rounded border-gray-300 text-emerald-600"
                                                    type="checkbox"
                                                    id="leaderboard_is_public_{{ $quizRange->id }}"
                                                    name="leaderboard_is_public"
                                                    value="1"
                                                    {{ $quizRange->leaderboard_is_public ? 'checked' : '' }}
                                                >
                                                <label class="text-xs text-gray-700" for="leaderboard_is_public_{{ $quizRange->id }}">Show leaderboard to users</label>
                                            </div>
                                            <button class="inline-flex items-center justify-center rounded-full bg-emerald-600 px-4 py-1.5 text-xs font-semibold text-white hover:bg-emerald-700" type="submit">
                                                Update
                                            </button>
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
