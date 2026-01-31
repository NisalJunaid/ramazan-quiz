@extends('layouts.app')

@section('content')
    <div class="mx-auto flex w-full max-w-5xl flex-col gap-6 px-4 sm:px-6 lg:px-8">
        <header class="flex flex-wrap items-start justify-between gap-4">
            <div>
                <p class="text-xs font-semibold uppercase tracking-[0.3em] text-amber-500">{{ text('admin.theme.overline', 'Appearance') }}</p>
                <h1 class="mt-2 text-2xl font-semibold text-emerald-700">{{ text('admin.theme.title', 'Theme Settings') }}</h1>
                <p class="mt-2 text-sm text-gray-600">{{ text('admin.theme.subtitle', 'Control the global background and logo used across the app.') }}</p>
            </div>
            <a class="text-sm font-semibold text-emerald-700 hover:text-emerald-800" href="{{ route('admin.index') }}">
                {{ text('admin.theme.back', 'Back to Admin') }}
            </a>
        </header>

        @if (session('status'))
            <div class="rounded-2xl border border-emerald-100 bg-emerald-50 px-4 py-3 text-sm text-emerald-700">
                {{ session('status') }}
            </div>
        @endif

        <section class="rounded-2xl bg-white p-6 shadow-sm ring-1 ring-gray-200">
            <h2 class="text-lg font-semibold text-gray-900">{{ text('admin.theme.form.title', 'Global appearance settings') }}</h2>
            <p class="mt-2 text-sm text-gray-600">{{ text('admin.theme.form.helper', 'Upload images and adjust sizing to update the look everywhere.') }}</p>

            <form class="mt-6 grid gap-6" method="POST" action="{{ route('admin.theme.update') }}" enctype="multipart/form-data">
                @csrf
                @method('PUT')

                <div class="grid gap-4 md:grid-cols-2">
                    <div>
                        <label class="text-xs font-semibold uppercase tracking-wide text-gray-500" for="body-background-image">
                            {{ text('admin.theme.background.image', 'Body background image') }}
                        </label>
                        <input
                            id="body-background-image"
                            class="mt-1 w-full rounded-xl border-gray-300 px-3 py-2 text-sm focus:border-emerald-500 focus:ring-emerald-500"
                            type="file"
                            name="body_background_image"
                            accept="image/png,image/jpeg,image/webp"
                        >
                        <p class="mt-1 text-xs text-gray-500">
                            {{ text('admin.theme.background.image_hint', 'PNG, JPG, or WEBP up to 2MB.') }}
                        </p>
                        @if ($settings->body_background_image)
                            <p class="mt-2 text-xs text-gray-500">
                                {{ text('admin.theme.background.current', 'Current background image uploaded.') }}
                            </p>
                        @endif
                    </div>
                    <div>
                        <label class="text-xs font-semibold uppercase tracking-wide text-gray-500" for="body-background-fit">
                            {{ text('admin.theme.background.fit', 'Background fit') }}
                        </label>
                        <select
                            id="body-background-fit"
                            class="mt-1 w-full rounded-xl border-gray-300 px-3 py-2 text-sm focus:border-emerald-500 focus:ring-emerald-500"
                            name="body_background_fit"
                            required
                        >
                            @php
                                $selectedFit = old('body_background_fit', $settings->body_background_fit ?? 'cover');
                            @endphp
                            <option value="cover" {{ $selectedFit === 'cover' ? 'selected' : '' }}>{{ text('admin.theme.background.fit_cover', 'Cover') }}</option>
                            <option value="contain" {{ $selectedFit === 'contain' ? 'selected' : '' }}>{{ text('admin.theme.background.fit_contain', 'Contain') }}</option>
                            <option value="fill" {{ $selectedFit === 'fill' ? 'selected' : '' }}>{{ text('admin.theme.background.fit_fill', 'Fill') }}</option>
                        </select>
                    </div>
                </div>

                <div class="grid gap-4 md:grid-cols-2">
                    <div>
                        <label class="text-xs font-semibold uppercase tracking-wide text-gray-500" for="app-logo">
                            {{ text('admin.theme.logo.image', 'App logo image') }}
                        </label>
                        <input
                            id="app-logo"
                            class="mt-1 w-full rounded-xl border-gray-300 px-3 py-2 text-sm focus:border-emerald-500 focus:ring-emerald-500"
                            type="file"
                            name="app_logo"
                            accept="image/png,image/jpeg,image/webp"
                        >
                        <p class="mt-1 text-xs text-gray-500">
                            {{ text('admin.theme.logo.image_hint', 'PNG, JPG, or WEBP up to 2MB.') }}
                        </p>
                        @if ($settings->app_logo)
                            <p class="mt-2 text-xs text-gray-500">
                                {{ text('admin.theme.logo.current', 'Current logo uploaded.') }}
                            </p>
                        @endif
                    </div>
                    <div class="grid gap-4 md:grid-cols-2">
                        <div>
                            <label class="text-xs font-semibold uppercase tracking-wide text-gray-500" for="logo-width">
                                {{ text('admin.theme.logo.width', 'Logo width') }}
                            </label>
                            <input
                                id="logo-width"
                                class="mt-1 w-full rounded-xl border-gray-300 px-3 py-2 text-sm focus:border-emerald-500 focus:ring-emerald-500"
                                type="number"
                                name="logo_width"
                                step="0.1"
                                min="0"
                                value="{{ old('logo_width', $settings->logo_width) }}"
                            >
                        </div>
                        <div>
                            <label class="text-xs font-semibold uppercase tracking-wide text-gray-500" for="logo-width-unit">
                                {{ text('admin.theme.logo.width_unit', 'Width unit') }}
                            </label>
                            <select
                                id="logo-width-unit"
                                class="mt-1 w-full rounded-xl border-gray-300 px-3 py-2 text-sm focus:border-emerald-500 focus:ring-emerald-500"
                                name="logo_width_unit"
                                required
                            >
                                @php
                                    $selectedWidthUnit = old('logo_width_unit', $settings->logo_width_unit ?? 'px');
                                @endphp
                                <option value="px" {{ $selectedWidthUnit === 'px' ? 'selected' : '' }}>px</option>
                                <option value="%" {{ $selectedWidthUnit === '%' ? 'selected' : '' }}>%</option>
                                <option value="rem" {{ $selectedWidthUnit === 'rem' ? 'selected' : '' }}>rem</option>
                                <option value="vw" {{ $selectedWidthUnit === 'vw' ? 'selected' : '' }}>vw</option>
                            </select>
                        </div>
                        <div>
                            <label class="text-xs font-semibold uppercase tracking-wide text-gray-500" for="logo-height">
                                {{ text('admin.theme.logo.height', 'Logo height (optional)') }}
                            </label>
                            <input
                                id="logo-height"
                                class="mt-1 w-full rounded-xl border-gray-300 px-3 py-2 text-sm focus:border-emerald-500 focus:ring-emerald-500"
                                type="number"
                                name="logo_height"
                                step="0.1"
                                min="0"
                                value="{{ old('logo_height', $settings->logo_height) }}"
                            >
                        </div>
                        <div>
                            <label class="text-xs font-semibold uppercase tracking-wide text-gray-500" for="logo-height-unit">
                                {{ text('admin.theme.logo.height_unit', 'Height unit') }}
                            </label>
                            <select
                                id="logo-height-unit"
                                class="mt-1 w-full rounded-xl border-gray-300 px-3 py-2 text-sm focus:border-emerald-500 focus:ring-emerald-500"
                                name="logo_height_unit"
                            >
                                @php
                                    $selectedHeightUnit = old('logo_height_unit', $settings->logo_height_unit);
                                @endphp
                                <option value="" {{ $selectedHeightUnit === null ? 'selected' : '' }}>{{ text('admin.theme.logo.height_unit_auto', 'Auto') }}</option>
                                <option value="px" {{ $selectedHeightUnit === 'px' ? 'selected' : '' }}>px</option>
                                <option value="%" {{ $selectedHeightUnit === '%' ? 'selected' : '' }}>%</option>
                                <option value="rem" {{ $selectedHeightUnit === 'rem' ? 'selected' : '' }}>rem</option>
                                <option value="vw" {{ $selectedHeightUnit === 'vw' ? 'selected' : '' }}>vw</option>
                            </select>
                        </div>
                    </div>
                </div>

                <button class="inline-flex items-center rounded-full bg-emerald-600 px-4 py-2 text-sm font-semibold text-white hover:bg-emerald-700" type="submit">
                    {{ text('admin.theme.save', 'Save Theme Settings') }}
                </button>
            </form>
        </section>
    </div>
@endsection
