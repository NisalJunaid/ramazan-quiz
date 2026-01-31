@extends('layouts.app')

@php
    $isRtl = is_rtl();
@endphp

@section('content')
    <div class="mx-auto flex w-full max-w-6xl flex-col gap-6 px-4 sm:px-6 lg:px-8">
        <header class="flex flex-wrap items-start justify-between gap-4">
            <div>
                <p class="text-xs font-semibold uppercase tracking-[0.3em] text-amber-500">{{ text('admin.fonts.overline', 'Typography') }}</p>
                <h1 class="mt-2 text-2xl font-semibold text-emerald-700">{{ text('admin.fonts.title', 'Font Manager') }}</h1>
                <p class="mt-2 text-sm text-gray-600">
                    {{ text('admin.fonts.subtitle', 'Register upload or Google Fonts to use across text keys.') }}
                </p>
            </div>
            <a class="text-sm font-semibold text-emerald-700 hover:text-emerald-800" href="{{ route('admin.index') }}">
                {{ text('admin.fonts.back', 'Back to Admin') }}
            </a>
        </header>

        @if (session('status'))
            <div class="rounded-2xl border border-emerald-100 bg-emerald-50 px-4 py-3 text-sm text-emerald-700">
                {{ session('status') }}
            </div>
        @endif

        <section class="rounded-2xl bg-white p-6 shadow-sm ring-1 ring-gray-200">
            <h2 class="text-lg font-semibold text-gray-900">{{ text('admin.fonts.create.title', 'Add a font') }}</h2>
            <p class="mt-2 text-sm text-gray-600">
                {{ text('admin.fonts.create.helper', 'Upload a font file or register a Google Fonts stylesheet URL.') }}
            </p>

            <form class="mt-6 grid gap-4" method="POST" action="{{ route('admin.fonts.store') }}" enctype="multipart/form-data">
                @csrf
                <div class="grid gap-4 md:grid-cols-3">
                    <div>
                        <label class="text-xs font-semibold uppercase tracking-wide text-gray-500" for="font-name">
                            {{ text('admin.fonts.create.name', 'Name') }}
                        </label>
                        <input
                            id="font-name"
                            class="mt-1 w-full rounded-xl border-gray-300 px-3 py-2 text-sm focus:border-emerald-500 focus:ring-emerald-500"
                            type="text"
                            name="name"
                            value="{{ old('name') }}"
                            required
                        >
                    </div>
                    <div>
                        <label class="text-xs font-semibold uppercase tracking-wide text-gray-500" for="font-css-family">
                            {{ text('admin.fonts.create.css_family', 'CSS font-family') }}
                        </label>
                        <input
                            id="font-css-family"
                            class="mt-1 w-full rounded-xl border-gray-300 px-3 py-2 text-sm focus:border-emerald-500 focus:ring-emerald-500"
                            type="text"
                            name="css_family"
                            placeholder='"Inter", sans-serif'
                            value="{{ old('css_family') }}"
                            required
                        >
                    </div>
                    <div>
                        <label class="text-xs font-semibold uppercase tracking-wide text-gray-500" for="font-css-class">
                            {{ text('admin.fonts.create.css_class', 'CSS class') }}
                        </label>
                        <input
                            id="font-css-class"
                            class="mt-1 w-full rounded-xl border-gray-300 px-3 py-2 text-sm focus:border-emerald-500 focus:ring-emerald-500"
                            type="text"
                            name="css_class"
                            placeholder="font-inter"
                            value="{{ old('css_class') }}"
                            required
                        >
                    </div>
                </div>

                <div class="grid gap-4 md:grid-cols-3">
                    <div>
                        <label class="text-xs font-semibold uppercase tracking-wide text-gray-500" for="font-source-type">
                            {{ text('admin.fonts.create.source_type', 'Source type') }}
                        </label>
                        <select
                            id="font-source-type"
                            class="mt-1 w-full rounded-xl border-gray-300 px-3 py-2 text-sm focus:border-emerald-500 focus:ring-emerald-500"
                            name="source_type"
                            required
                        >
                            <option value="upload" {{ old('source_type') === 'upload' ? 'selected' : '' }}>{{ text('admin.fonts.create.source_upload', 'Upload file') }}</option>
                            <option value="google" {{ old('source_type') === 'google' ? 'selected' : '' }}>{{ text('admin.fonts.create.source_google', 'Google Fonts URL') }}</option>
                        </select>
                    </div>
                    <div>
                        <label class="text-xs font-semibold uppercase tracking-wide text-gray-500" for="font-upload">
                            {{ text('admin.fonts.create.upload', 'Font file') }}
                        </label>
                        <input
                            id="font-upload"
                            class="mt-1 w-full rounded-xl border-gray-300 px-3 py-2 text-sm focus:border-emerald-500 focus:ring-emerald-500"
                            type="file"
                            name="font_file"
                            accept=".woff,.woff2,.ttf"
                        >
                        <p class="mt-1 text-xs text-gray-500">
                            {{ text('admin.fonts.create.upload_hint', 'Allowed: .woff, .woff2, .ttf') }}
                        </p>
                    </div>
                    <div>
                        <label class="text-xs font-semibold uppercase tracking-wide text-gray-500" for="font-url">
                            {{ text('admin.fonts.create.google_url', 'Google Fonts URL') }}
                        </label>
                        <input
                            id="font-url"
                            class="mt-1 w-full rounded-xl border-gray-300 px-3 py-2 text-sm focus:border-emerald-500 focus:ring-emerald-500"
                            type="url"
                            name="source_url"
                            placeholder="https://fonts.googleapis.com/css2?family=Amiri"
                            value="{{ old('source_url') }}"
                        >
                    </div>
                </div>

                <label class="flex items-center gap-2 text-sm font-medium text-gray-700">
                    <input
                        class="h-4 w-4 rounded border-gray-300 text-emerald-600 focus:ring-emerald-500"
                        type="checkbox"
                        name="is_rtl_optimized"
                        value="1"
                        {{ old('is_rtl_optimized') ? 'checked' : '' }}
                    >
                    {{ text('admin.fonts.create.rtl', 'Optimized for RTL content') }}
                </label>

                <button class="inline-flex items-center rounded-full bg-emerald-600 px-4 py-2 text-sm font-semibold text-white hover:bg-emerald-700" type="submit">
                    {{ text('admin.fonts.create.submit', 'Save Font') }}
                </button>
            </form>
        </section>

        <section class="rounded-2xl bg-white p-6 shadow-sm ring-1 ring-gray-200">
            <div class="flex flex-wrap items-center justify-between gap-3">
                <h2 class="text-lg font-semibold text-gray-900">{{ text('admin.fonts.list.title', 'Registered fonts') }}</h2>
                <span class="text-xs text-gray-500">{{ count($fonts) }} {{ text('admin.fonts.list.count', 'fonts') }}</span>
            </div>

            @if ($fonts->isEmpty())
                <p class="mt-4 text-sm text-gray-600">{{ text('admin.fonts.list.empty', 'No fonts registered yet.') }}</p>
            @else
                <div class="mt-4 overflow-x-auto">
                    <table class="min-w-full text-sm">
                        <thead class="border-b border-gray-200 text-start text-xs uppercase tracking-widest text-gray-500">
                            <tr>
                                <th class="px-4 py-3">{{ text('admin.fonts.list.headers.name', 'Name') }}</th>
                                <th class="px-4 py-3">{{ text('admin.fonts.list.headers.class', 'CSS class') }}</th>
                                <th class="px-4 py-3">{{ text('admin.fonts.list.headers.type', 'Source') }}</th>
                                <th class="px-4 py-3">{{ text('admin.fonts.list.headers.rtl', 'RTL') }}</th>
                                <th class="px-4 py-3">{{ text('admin.fonts.list.headers.actions', 'Actions') }}</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100">
                            @foreach ($fonts as $font)
                                <tr class="bg-white">
                                    <td class="px-4 py-3 font-semibold text-gray-900">
                                        <div class="flex items-center gap-2">
                                            <span class="{{ $font->css_class }}">{{ $font->name }}</span>
                                            <span class="text-xs text-gray-400">Aa</span>
                                        </div>
                                    </td>
                                    <td class="px-4 py-3 text-gray-600">
                                        .{{ $font->css_class }}
                                    </td>
                                    <td class="px-4 py-3 text-gray-600">
                                        {{ $font->source_type === 'upload' ? text('admin.fonts.list.source_upload', 'Upload') : text('admin.fonts.list.source_google', 'Google') }}
                                    </td>
                                    <td class="px-4 py-3">
                                        @if ($font->is_rtl_optimized)
                                            <span class="inline-flex items-center rounded-full bg-emerald-50 px-2 py-1 text-xs font-semibold text-emerald-700">
                                                {{ text('admin.fonts.list.rtl_yes', 'RTL Ready') }}
                                            </span>
                                        @else
                                            <span class="inline-flex items-center rounded-full bg-gray-100 px-2 py-1 text-xs font-semibold text-gray-600">
                                                {{ text('admin.fonts.list.rtl_no', 'LTR only') }}
                                            </span>
                                            @if ($isRtl)
                                                <p class="mt-1 text-xs text-amber-600">
                                                    {{ text('admin.fonts.list.rtl_warning', 'RTL mode is enabled; consider an RTL-optimized font.') }}
                                                </p>
                                            @endif
                                        @endif
                                    </td>
                                    <td class="px-4 py-3">
                                        <button
                                            class="inline-flex items-center rounded-full border border-rose-300 px-3 py-1 text-xs font-semibold text-rose-600 hover:bg-rose-50"
                                            form="delete-font-{{ $font->id }}"
                                            type="submit"
                                        >
                                            {{ text('admin.fonts.list.delete', 'Delete') }}
                                        </button>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @endif

            @foreach ($fonts as $font)
                <form
                    id="delete-font-{{ $font->id }}"
                    method="POST"
                    action="{{ route('admin.fonts.destroy', $font) }}"
                    onsubmit="return confirm('{{ text('admin.fonts.delete.confirm', 'Delete this font? Any assigned text will fall back to default.') }}')"
                >
                    @csrf
                    @method('DELETE')
                </form>
            @endforeach
        </section>
    </div>
@endsection
