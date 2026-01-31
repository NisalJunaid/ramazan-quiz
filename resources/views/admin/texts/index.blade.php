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

            <div class="mt-6 overflow-x-auto rounded-2xl border border-gray-200">
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
                        @forelse ($texts as $text)
                            <tr class="bg-white">
                                <td class="px-4 py-3 font-semibold text-gray-900">
                                    {{ $text->key }}
                                </td>
                                <td class="px-4 py-3 text-gray-600">
                                    {{ $text->locale ?? text('admin.texts.table.default', 'Default') }}
                                </td>
                                <td class="px-4 py-3">
                                    <form id="text-form-{{ $text->id }}" method="POST" action="{{ route('admin.texts.update', $text) }}">
                                        @csrf
                                        @method('PUT')
                                        <input
                                            class="w-full rounded-xl border-gray-300 px-3 py-2 text-sm focus:border-emerald-500 focus:ring-emerald-500"
                                            type="text"
                                            name="value"
                                            value="{{ $text->value }}"
                                            required
                                        >
                                    </form>
                                </td>
                                <td class="px-4 py-3 text-gray-500">
                                    {{ $text->locale ? ($fallbacks[$text->key] ?? text('admin.texts.table.none', 'None')) : text('admin.texts.table.primary', 'Primary') }}
                                </td>
                                <td class="px-4 py-3">
                                    <div class="flex flex-wrap items-center gap-2">
                                        <button class="inline-flex items-center rounded-full bg-emerald-600 px-3 py-1 text-xs font-semibold text-white hover:bg-emerald-700" type="submit" form="{{ 'text-form-' . $text->id }}">
                                            {{ text('admin.texts.table.save', 'Save') }}
                                        </button>
                                        <form method="POST" action="{{ route('admin.texts.destroy', $text) }}" onsubmit="return confirm('{{ text('admin.texts.delete.confirm', 'Delete this text key?') }}')">
                                            @csrf
                                            @method('DELETE')
                                            <button class="inline-flex items-center rounded-full border border-rose-300 px-3 py-1 text-xs font-semibold text-rose-600 hover:bg-rose-50" type="submit">
                                                {{ text('admin.texts.table.delete', 'Delete') }}
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td class="px-4 py-6 text-center text-sm text-gray-500" colspan="5">
                                    {{ text('admin.texts.table.empty', 'No text entries found.') }}
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </section>
    </div>
@endsection
