@extends('layouts.app')

@section('content')
    <div class="mx-auto flex w-full max-w-6xl flex-col gap-6 px-4 sm:px-6 lg:px-8">
        <header class="flex flex-wrap items-start justify-between gap-4">
            <div>
                <p class="text-xs font-semibold uppercase tracking-[0.3em] text-amber-500">{{ text('admin.texts.overline', 'Content') }}</p>
                <h1 class="mt-2 text-2xl font-semibold text-emerald-700">{{ text('admin.texts.title', 'Text Manager') }}</h1>
                <p class="mt-2 text-sm text-gray-600">
                    {{ text('admin.texts.subtitle', 'Edit user-facing text and manage RTL layout settings.') }}
                </p>
            </div>
            <a class="text-sm font-semibold text-emerald-700 hover:text-emerald-800" href="{{ route('admin.index') }}">
                {{ text('admin.texts.back', 'Back to Admin') }}
            </a>
        </header>

        @if (session('status'))
            <div class="rounded-2xl border border-emerald-100 bg-emerald-50 px-4 py-3 text-sm text-emerald-700">
                {{ session('status') }}
            </div>
        @endif

        <section class="rounded-2xl bg-white p-6 shadow-sm ring-1 ring-gray-200">
            <h2 class="text-lg font-semibold text-gray-900">{{ text('admin.texts.rtl.heading', 'RTL Layout Setting') }}</h2>
            <p class="mt-2 text-sm text-gray-600">
                {{ text('admin.texts.rtl.description', 'Toggle RTL globally. Locale-based RTL can also be enabled via config (see config/localization.php).') }}
            </p>
            <form class="mt-4 flex flex-wrap items-center gap-4" method="POST" action="{{ route('admin.settings.rtl') }}">
                @csrf
                @method('PUT')
                <label class="flex items-center gap-2 text-sm font-medium text-gray-700">
                    <input
                        class="h-4 w-4 rounded border-gray-300 text-emerald-600 focus:ring-emerald-500"
                        type="checkbox"
                        name="is_rtl"
                        value="1"
                        {{ $settings->is_rtl ? 'checked' : '' }}
                    >
                    {{ text('admin.texts.rtl.toggle', 'Enable RTL mode') }}
                </label>
                <button class="inline-flex items-center rounded-full bg-emerald-600 px-4 py-2 text-sm font-semibold text-white hover:bg-emerald-700" type="submit">
                    {{ text('admin.texts.rtl.save', 'Save RTL Setting') }}
                </button>
            </form>
        </section>

        <section class="rounded-2xl bg-white p-6 shadow-sm ring-1 ring-gray-200">
            <div class="flex flex-wrap items-center justify-between gap-3">
                <h2 class="text-lg font-semibold text-gray-900">{{ text('admin.texts.manage.heading', 'Manage Text Keys') }}</h2>
                <form method="GET" action="{{ route('admin.texts.index') }}" class="flex flex-wrap items-center gap-2">
                    <input
                        class="h-9 rounded-full border-gray-300 px-4 text-sm focus:border-emerald-500 focus:ring-emerald-500"
                        type="text"
                        name="search"
                        placeholder="{{ text('admin.texts.search.placeholder', 'Search by key') }}"
                        value="{{ $search }}"
                    >
                    <button class="inline-flex items-center rounded-full border border-emerald-600 px-4 py-2 text-xs font-semibold uppercase tracking-wide text-emerald-700 hover:bg-emerald-50" type="submit">
                        {{ text('admin.texts.search.button', 'Search') }}
                    </button>
                </form>
            </div>

            <form class="mt-6 grid gap-4 rounded-2xl border border-dashed border-gray-200 p-4" method="POST" action="{{ route('admin.texts.store') }}">
                @csrf
                <div class="grid gap-4 md:grid-cols-3">
                    <div>
                        <label class="text-xs font-semibold uppercase tracking-wide text-gray-500" for="new-text-key">
                            {{ text('admin.texts.create.key', 'Key') }}
                        </label>
                        <input
                            id="new-text-key"
                            class="mt-1 w-full rounded-xl border-gray-300 px-3 py-2 text-sm focus:border-emerald-500 focus:ring-emerald-500"
                            type="text"
                            name="key"
                            required
                        >
                    </div>
                    <div>
                        <label class="text-xs font-semibold uppercase tracking-wide text-gray-500" for="new-text-locale">
                            {{ text('admin.texts.create.locale', 'Locale (optional)') }}
                        </label>
                        <input
                            id="new-text-locale"
                            class="mt-1 w-full rounded-xl border-gray-300 px-3 py-2 text-sm focus:border-emerald-500 focus:ring-emerald-500"
                            type="text"
                            name="locale"
                            placeholder="en"
                        >
                    </div>
                    <div>
                        <label class="text-xs font-semibold uppercase tracking-wide text-gray-500" for="new-text-value">
                            {{ text('admin.texts.create.value', 'Value') }}
                        </label>
                        <input
                            id="new-text-value"
                            class="mt-1 w-full rounded-xl border-gray-300 px-3 py-2 text-sm focus:border-emerald-500 focus:ring-emerald-500"
                            type="text"
                            name="value"
                            required
                        >
                    </div>
                </div>
                <div>
                    <button class="inline-flex items-center rounded-full bg-emerald-600 px-4 py-2 text-sm font-semibold text-white hover:bg-emerald-700" type="submit">
                        {{ text('admin.texts.create.button', 'Add Text') }}
                    </button>
                </div>
            </form>

            <form
                class="mt-6 space-y-6"
                method="POST"
                action="{{ route('admin.texts.bulkUpdate') }}"
                onsubmit="const button = this.querySelector('[data-save-all]'); if (button) { button.disabled = true; button.classList.add('opacity-70','cursor-not-allowed'); button.querySelector('[data-label]').classList.add('hidden'); button.querySelector('[data-loading]').classList.remove('hidden'); }"
            >
                @csrf
                <div class="sticky top-4 z-20 flex justify-end">
                    <button
                        class="inline-flex items-center gap-2 rounded-full bg-emerald-600 px-5 py-2 text-sm font-semibold text-white shadow-sm transition hover:bg-emerald-700"
                        data-save-all
                        type="submit"
                    >
                        <span data-label>{{ text('admin.texts.bulk.save', 'Save All Changes') }}</span>
                        <span class="hidden text-xs uppercase tracking-widest" data-loading>{{ text('admin.texts.bulk.saving', 'Saving...') }}</span>
                    </button>
                </div>

                @forelse ($groupedTexts as $groupLabel => $groupTexts)
                    @php
                        $groupTitles = [
                            'Home' => 'Home Page',
                            'Quiz' => 'Quiz Page',
                            'Leaderboard' => 'Leaderboard Page',
                            'Admin' => 'Admin Panel',
                            'General' => 'General',
                        ];
                    @endphp
                    <details class="rounded-2xl border border-gray-200 bg-white shadow-sm" open>
                        <summary class="flex cursor-pointer items-center justify-between gap-4 rounded-2xl px-5 py-4 text-left">
                            <div>
                                <h3 class="text-base font-semibold text-gray-900">
                                    {{ $groupTitles[$groupLabel] ?? $groupLabel }}
                                </h3>
                                <p class="text-xs text-gray-500">
                                    {{ count($groupTexts) }} {{ text('admin.texts.group.count', 'keys') }}
                                </p>
                            </div>
                            <span class="text-xs font-semibold uppercase tracking-widest text-emerald-600">
                                {{ text('admin.texts.group.toggle', 'Toggle') }}
                            </span>
                        </summary>
                        <div class="border-t border-gray-100 px-5 py-4">
                            <div class="overflow-x-auto">
                                <table class="min-w-full text-sm">
                                    <thead class="border-b border-gray-200 text-start text-xs uppercase tracking-widest text-gray-500">
                                        <tr>
                                            <th class="px-4 py-3">{{ text('admin.texts.table.key', 'Key') }}</th>
                                            <th class="px-4 py-3">{{ text('admin.texts.table.locale', 'Locale') }}</th>
                                            <th class="px-4 py-3">{{ text('admin.texts.table.value', 'Value') }}</th>
                                            <th class="px-4 py-3">{{ text('admin.texts.table.fallback', 'Fallback') }}</th>
                                            <th class="px-4 py-3">{{ text('admin.texts.table.actions', 'Actions') }}</th>
                                        </tr>
                                    </thead>
                                    <tbody class="divide-y divide-gray-100">
                                        @foreach ($groupTexts as $text)
                                            <tr class="bg-white">
                                                <td class="px-4 py-3 font-semibold text-gray-900">
                                                    {{ $text->key }}
                                                </td>
                                                <td class="px-4 py-3 text-gray-600">
                                                    {{ $text->locale ?? text('admin.texts.table.default', 'Default') }}
                                                </td>
                                                <td class="px-4 py-3">
                                                    <input
                                                        class="w-full rounded-xl border-gray-300 px-3 py-2 text-sm focus:border-emerald-500 focus:ring-emerald-500"
                                                        type="text"
                                                        name="texts[{{ $text->id }}][value]"
                                                        value="{{ $text->value }}"
                                                    >
                                                </td>
                                                <td class="px-4 py-3">
                                                    <input
                                                        class="w-full rounded-xl border-gray-200 bg-gray-50 px-3 py-2 text-sm text-gray-500"
                                                        type="text"
                                                        value="{{ $text->locale ? ($text->fallback ?? text('admin.texts.table.none', 'None')) : text('admin.texts.table.primary', 'Primary') }}"
                                                        readonly
                                                    >
                                                </td>
                                                <td class="px-4 py-3">
                                                    <button
                                                        class="inline-flex items-center rounded-full border border-rose-300 px-3 py-1 text-xs font-semibold text-rose-600 hover:bg-rose-50"
                                                        form="delete-text-{{ $text->id }}"
                                                        type="submit"
                                                    >
                                                        {{ text('admin.texts.table.delete', 'Delete') }}
                                                    </button>
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </details>
                @empty
                    <div class="rounded-2xl border border-dashed border-gray-200 px-4 py-6 text-center text-sm text-gray-500">
                        {{ text('admin.texts.table.empty', 'No text entries found.') }}
                    </div>
                @endforelse
            </form>

            @foreach ($groupedTexts as $groupTexts)
                @foreach ($groupTexts as $text)
                    <form
                        id="delete-text-{{ $text->id }}"
                        method="POST"
                        action="{{ route('admin.texts.destroy', $text) }}"
                        onsubmit="return confirm('{{ text('admin.texts.delete.confirm', 'Delete this text key?') }}')"
                    >
                        @csrf
                        @method('DELETE')
                    </form>
                @endforeach
            @endforeach
        </section>
    </div>
@endsection
