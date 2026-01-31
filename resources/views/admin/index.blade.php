@extends('layouts.app')

@section('content')
    <div class="mx-auto flex w-full max-w-5xl flex-col gap-6 px-4 sm:px-6 lg:px-8">
        <header>
            <p class="text-xs font-semibold uppercase tracking-[0.3em] text-amber-500">{{ text('admin.dashboard.overline', 'Admin Panel') }}</p>
            <h1 class="mt-2 text-2xl font-semibold text-emerald-700">{{ text('admin.dashboard.title', 'Quiz Management Dashboard') }}</h1>
            <p class="mt-2 text-sm text-gray-600">{{ text('admin.dashboard.subtitle', 'Create quiz ranges, manage daily questions, and publish quizzes.') }}</p>
        </header>

        <section class="grid gap-4 sm:grid-cols-2">
            <div class="rounded-2xl bg-white p-6 shadow-sm ring-1 ring-gray-200">
                <h2 class="text-lg font-semibold text-gray-900">{{ text('admin.dashboard.quizzes.title', 'Manage Quizzes') }}</h2>
                <p class="mt-2 text-sm text-gray-600">{{ text('admin.dashboard.quizzes.subtitle', 'Create quiz ranges, edit daily questions, and set publication status.') }}</p>
                <a class="mt-4 inline-flex items-center gap-2 text-sm font-semibold text-emerald-700 hover:text-emerald-800" href="{{ route('admin.quizzes.index') }}">
                    {{ text('admin.dashboard.quizzes.link', 'Go to Quiz Manager') }}
                    <span aria-hidden="true">→</span>
                </a>
            </div>

            <div class="rounded-2xl bg-white p-6 shadow-sm ring-1 ring-gray-200">
                <h2 class="text-lg font-semibold text-gray-900">{{ text('admin.dashboard.texts.title', 'Manage App Text') }}</h2>
                <p class="mt-2 text-sm text-gray-600">{{ text('admin.dashboard.texts.subtitle', 'Update labels, messages, and RTL preferences without code changes.') }}</p>
                <a class="mt-4 inline-flex items-center gap-2 text-sm font-semibold text-emerald-700 hover:text-emerald-800" href="{{ route('admin.texts.index') }}">
                    {{ text('admin.dashboard.texts.link', 'Open Text Manager') }}
                    <span aria-hidden="true">→</span>
                </a>
            </div>

            <div class="rounded-2xl border border-dashed border-gray-300 bg-white p-6">
                <h2 class="text-lg font-semibold text-gray-900">{{ text('admin.dashboard.tips.title', 'Quick Tips') }}</h2>
                <ul class="mt-3 space-y-2 text-sm text-gray-600">
                    <li>{{ text('admin.dashboard.tips.publish', 'Publish quizzes only after verifying questions and choices.') }}</li>
                    <li>{{ text('admin.dashboard.tips.locked', 'Editing is locked once submissions arrive.') }}</li>
                </ul>
            </div>
        </section>

        <div>
            <a class="text-sm font-semibold text-emerald-700 hover:text-emerald-800" href="{{ route('home') }}">
                {{ text('admin.dashboard.back', 'Back to Home') }}
            </a>
        </div>
    </div>
@endsection
