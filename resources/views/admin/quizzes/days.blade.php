@extends('layouts.app')

@section('content')
    <div class="mx-auto flex w-full max-w-6xl flex-col gap-6 px-4 sm:px-6 lg:px-8">
        <header class="flex flex-wrap items-center justify-between gap-4">
            <div>
                <p class="text-xs font-semibold uppercase tracking-[0.3em] text-amber-500">{{ text('admin.days.overline', 'Admin') }}</p>
                <h1 class="mt-2 text-2xl font-semibold text-emerald-700">{{ $quizRange->title }}</h1>
                <p class="mt-1 text-sm text-gray-600">
                    {{ $quizRange->start_date }} → {{ $quizRange->end_date }}
                    · {{ str_replace(':count', $quizRange->days_count, text('admin.days.count', ':count days')) }}
                </p>
            </div>
            <a class="text-sm font-semibold text-emerald-700 hover:text-emerald-800" href="{{ route('admin.quizzes.index') }}">
                {{ text('admin.days.back', 'Back to Ranges') }}
            </a>
        </header>

        @if (session('status'))
            <div class="rounded-2xl border border-emerald-100 bg-emerald-50 px-4 py-3 text-sm text-emerald-700">
                {{ session('status') }}
            </div>
        @endif

        <section class="rounded-2xl bg-white p-6 shadow-sm ring-1 ring-gray-200">
            <div class="flex flex-wrap items-center justify-between gap-3">
                <h2 class="text-lg font-semibold text-gray-900">{{ text('admin.days.title', 'Daily Questions') }}</h2>
                <span class="text-sm text-gray-500">{{ text('admin.days.subtitle', 'Edit each day before submissions.') }}</span>
            </div>
            <div class="mt-6 overflow-x-auto">
                <table class="min-w-full text-sm">
                    <thead class="border-b border-gray-200 text-start text-xs uppercase tracking-widest text-gray-500">
                        <tr>
                            <th class="px-4 py-3">{{ text('admin.days.table.date', 'Date') }}</th>
                            <th class="px-4 py-3">{{ text('admin.days.table.question', 'Question') }}</th>
                            <th class="px-4 py-3">{{ text('admin.days.table.manage', 'Manage') }}</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">
                        @foreach ($quizRange->quizDays as $quizDay)
                            <tr class="align-top">
                                <td class="px-4 py-3 font-medium text-gray-900">{{ $quizDay->quiz_date }}</td>
                                <td class="px-4 py-3 text-gray-600">
                                    {{ \Illuminate\Support\Str::limit($quizDay->question?->question_text ?: text('admin.days.table.empty', 'No question yet.'), 80) }}
                                </td>
                                <td class="px-4 py-3">
                                    <a class="text-sm font-semibold text-emerald-700 hover:text-emerald-800" href="{{ route('admin.quizzes.days.edit', [$quizRange, $quizDay]) }}">
                                        {{ text('admin.days.table.edit', 'Edit Day') }} →
                                    </a>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </section>
    </div>
@endsection
