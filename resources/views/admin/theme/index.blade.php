@extends('layouts.app')

@section('content')
    <div class="mx-auto flex w-full max-w-5xl flex-col gap-6 px-4 sm:px-6 lg:px-8">
        <header class="flex flex-wrap items-start justify-between gap-4">
            <div>
                <p class="text-xs font-semibold uppercase tracking-[0.3em] text-amber-500">{{ text('admin.theme.overline', 'Appearance') }}</p>
                <h1 class="mt-2 text-2xl font-semibold text-theme">{{ text('admin.theme.title', 'Theme Settings') }}</h1>
                <p class="mt-2 text-sm text-muted">{{ text('admin.theme.subtitle', 'Control the global background and logo used across the app.') }}</p>
            </div>
            <a class="text-sm font-semibold text-theme hover:text-theme" href="{{ route('admin.index') }}">
                {{ text('admin.theme.back', 'Back to Admin') }}
            </a>
        </header>

        @if (session('status'))
            <div class="rounded-2xl border px-4 py-3 text-sm" style="border-color: var(--color-ring); background: var(--color-surface-tint); color: var(--color-primary);">
                {{ session('status') }}
            </div>
        @endif

        <section class="card p-6 shadow-sm">
            <h2 class="text-lg font-semibold text-theme">{{ text('admin.theme.form.title', 'Global appearance settings') }}</h2>
            <p class="mt-2 text-sm text-muted">{{ text('admin.theme.form.helper', 'Upload images and adjust sizing to update the look everywhere.') }}</p>

            <form class="mt-6 grid gap-6" method="POST" action="{{ route('admin.theme.update') }}" enctype="multipart/form-data">
                @csrf
                @method('PUT')

                <div class="grid gap-4 md:grid-cols-2">
                    <div>
                        <label class="text-xs font-semibold uppercase tracking-wide text-muted" for="body-background-image">
                            {{ text('admin.theme.background.image', 'Body background image') }}
                        </label>
                        <input
                            id="body-background-image"
                            class="input mt-1 w-full px-3 py-2 text-sm"
                            type="file"
                            name="body_background_image"
                            accept="image/png,image/jpeg,image/webp"
                        >
                        <p class="mt-1 text-xs text-muted">
                            {{ text('admin.theme.background.image_hint', 'PNG, JPG, or WEBP up to 2MB.') }}
                        </p>
                        @if ($settings->body_background_image)
                            <p class="mt-2 text-xs text-muted">
                                {{ text('admin.theme.background.current', 'Current background image uploaded.') }}
                            </p>
                        @endif
                    </div>
                    <div>
                        <label class="text-xs font-semibold uppercase tracking-wide text-muted" for="body-background-fit">
                            {{ text('admin.theme.background.fit', 'Background fit') }}
                        </label>
                        <select
                            id="body-background-fit"
                            class="input mt-1 w-full px-3 py-2 text-sm"
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
                    <div class="md:col-span-2">
                        @php
                            $overlayOpacity = old('body_background_overlay_opacity', $settings->body_background_overlay_opacity ?? 0.90);
                        @endphp
                        <label class="text-xs font-semibold uppercase tracking-wide text-muted" for="body-background-overlay-opacity">
                            {{ text('admin.theme.background.overlay_opacity', 'Background overlay opacity') }}
                        </label>
                        <div class="mt-2 flex items-center gap-3">
                            <input
                                id="body-background-overlay-opacity"
                                class="w-full"
                                type="range"
                                name="body_background_overlay_opacity"
                                min="0"
                                max="1"
                                step="0.05"
                                value="{{ $overlayOpacity }}"
                                oninput="document.getElementById('body-background-overlay-opacity-value').textContent = this.value"
                            >
                            <span class="text-xs font-semibold text-muted" id="body-background-overlay-opacity-value">{{ $overlayOpacity }}</span>
                        </div>
                        <p class="mt-1 text-xs text-muted">
                            {{ text('admin.theme.background.overlay_opacity_hint', 'Controls how strong the white overlay on top of the background image is.') }}
                        </p>
                    </div>
                </div>

                <div class="grid gap-4 md:grid-cols-2">
                    <div>
                        <label class="text-xs font-semibold uppercase tracking-wide text-muted" for="app-logo">
                            {{ text('admin.theme.logo.image', 'App logo image') }}
                        </label>
                        <input
                            id="app-logo"
                            class="input mt-1 w-full px-3 py-2 text-sm"
                            type="file"
                            name="app_logo"
                            accept="image/png,image/jpeg,image/webp"
                        >
                        <p class="mt-1 text-xs text-muted">
                            {{ text('admin.theme.logo.image_hint', 'PNG, JPG, or WEBP up to 2MB.') }}
                        </p>
                        @if ($settings->app_logo)
                            <p class="mt-2 text-xs text-muted">
                                {{ text('admin.theme.logo.current', 'Current logo uploaded.') }}
                            </p>
                        @endif
                    </div>
                    <div class="grid gap-4 md:grid-cols-2">
                        <div>
                            <label class="text-xs font-semibold uppercase tracking-wide text-muted" for="logo-width">
                                {{ text('admin.theme.logo.width', 'Logo width') }}
                            </label>
                            <input
                                id="logo-width"
                                class="input mt-1 w-full px-3 py-2 text-sm"
                                type="number"
                                name="logo_width"
                                step="0.1"
                                min="0"
                                value="{{ old('logo_width', $settings->logo_width) }}"
                            >
                        </div>
                        <div>
                            <label class="text-xs font-semibold uppercase tracking-wide text-muted" for="logo-width-unit">
                                {{ text('admin.theme.logo.width_unit', 'Width unit') }}
                            </label>
                            <select
                                id="logo-width-unit"
                                class="input mt-1 w-full px-3 py-2 text-sm"
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
                            <label class="text-xs font-semibold uppercase tracking-wide text-muted" for="logo-height">
                                {{ text('admin.theme.logo.height', 'Logo height (optional)') }}
                            </label>
                            <input
                                id="logo-height"
                                class="input mt-1 w-full px-3 py-2 text-sm"
                                type="number"
                                name="logo_height"
                                step="0.1"
                                min="0"
                                value="{{ old('logo_height', $settings->logo_height) }}"
                            >
                        </div>
                        <div>
                            <label class="text-xs font-semibold uppercase tracking-wide text-muted" for="logo-height-unit">
                                {{ text('admin.theme.logo.height_unit', 'Height unit') }}
                            </label>
                            <select
                                id="logo-height-unit"
                                class="input mt-1 w-full px-3 py-2 text-sm"
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

                <div class="grid gap-4 rounded-2xl border border-dashed p-4">
                    <div>
                        <h3 class="text-base font-semibold text-theme">{{ text('admin.theme.logo.home.title', 'Homepage hero logo size') }}</h3>
                        <p class="mt-1 text-sm text-muted">{{ text('admin.theme.logo.home.helper', 'Adjust sizing for the homepage hero logo independently from the global logo settings.') }}</p>
                    </div>
                    <div class="grid gap-4 md:grid-cols-2">
                        <div>
                            <label class="text-xs font-semibold uppercase tracking-wide text-muted" for="home-logo-width">
                                {{ text('admin.theme.logo.home.width', 'Homepage logo width') }}
                            </label>
                            <input
                                id="home-logo-width"
                                class="input mt-1 w-full px-3 py-2 text-sm"
                                type="number"
                                name="home_logo_width"
                                step="0.1"
                                min="0"
                                value="{{ old('home_logo_width', $settings->home_logo_width) }}"
                            >
                        </div>
                        <div>
                            <label class="text-xs font-semibold uppercase tracking-wide text-muted" for="home-logo-width-unit">
                                {{ text('admin.theme.logo.home.width_unit', 'Homepage width unit') }}
                            </label>
                            <select
                                id="home-logo-width-unit"
                                class="input mt-1 w-full px-3 py-2 text-sm"
                                name="home_logo_width_unit"
                                required
                            >
                                @php
                                    $selectedHomeWidthUnit = old('home_logo_width_unit', $settings->home_logo_width_unit ?? 'px');
                                @endphp
                                <option value="px" {{ $selectedHomeWidthUnit === 'px' ? 'selected' : '' }}>px</option>
                                <option value="%" {{ $selectedHomeWidthUnit === '%' ? 'selected' : '' }}>%</option>
                                <option value="rem" {{ $selectedHomeWidthUnit === 'rem' ? 'selected' : '' }}>rem</option>
                                <option value="vw" {{ $selectedHomeWidthUnit === 'vw' ? 'selected' : '' }}>vw</option>
                            </select>
                        </div>
                        <div>
                            <label class="text-xs font-semibold uppercase tracking-wide text-muted" for="home-logo-height">
                                {{ text('admin.theme.logo.home.height', 'Homepage logo height (optional)') }}
                            </label>
                            <input
                                id="home-logo-height"
                                class="input mt-1 w-full px-3 py-2 text-sm"
                                type="number"
                                name="home_logo_height"
                                step="0.1"
                                min="0"
                                value="{{ old('home_logo_height', $settings->home_logo_height) }}"
                            >
                        </div>
                        <div>
                            <label class="text-xs font-semibold uppercase tracking-wide text-muted" for="home-logo-height-unit">
                                {{ text('admin.theme.logo.home.height_unit', 'Homepage height unit') }}
                            </label>
                            <select
                                id="home-logo-height-unit"
                                class="input mt-1 w-full px-3 py-2 text-sm"
                                name="home_logo_height_unit"
                            >
                                @php
                                    $selectedHomeHeightUnit = old('home_logo_height_unit', $settings->home_logo_height_unit);
                                @endphp
                                <option value="" {{ $selectedHomeHeightUnit === null ? 'selected' : '' }}>{{ text('admin.theme.logo.home.height_unit_auto', 'Auto') }}</option>
                                <option value="px" {{ $selectedHomeHeightUnit === 'px' ? 'selected' : '' }}>px</option>
                                <option value="%" {{ $selectedHomeHeightUnit === '%' ? 'selected' : '' }}>%</option>
                                <option value="rem" {{ $selectedHomeHeightUnit === 'rem' ? 'selected' : '' }}>rem</option>
                                <option value="vw" {{ $selectedHomeHeightUnit === 'vw' ? 'selected' : '' }}>vw</option>
                            </select>
                        </div>
                    </div>
                </div>

                <div class="grid gap-4 rounded-2xl border border-dashed p-4">
                    @php
                        $primaryColor = old('primary_color', $settings->primary_color ?? '#059669');
                        $primaryHoverColor = old('primary_hover_color', $settings->primary_hover_color ?? '#047857');
                        $accentColor = old('accent_color', $settings->accent_color ?? '#f59e0b');
                        $textColor = old('text_color', $settings->text_color ?? '#111827');
                        $borderColor = old('border_color', $settings->border_color ?? '#e5e7eb');

                        if (! str_starts_with($borderColor, '#')) {
                            $borderColor = '#e5e7eb';
                        }
                    @endphp
                    <div>
                        <h3 class="text-base font-semibold text-theme">{{ text('admin.theme.tokens.title', 'Theme design tokens') }}</h3>
                        <p class="mt-1 text-sm text-muted">{{ text('admin.theme.tokens.helper', 'These values update global colors, borders, and radius styles across the UI.') }}</p>
                    </div>
                    <div class="grid gap-4 md:grid-cols-2">
                        <div>
                            <label class="text-xs font-semibold uppercase tracking-wide text-muted" for="primary-color">
                                {{ text('admin.theme.tokens.primary', 'Primary color') }}
                            </label>
                            <input
                                id="primary-color"
                                class="input mt-1 w-full px-3 py-2 text-sm"
                                type="color"
                                name="primary_color"
                                value="{{ $primaryColor }}"
                            >
                        </div>
                        <div>
                            <label class="text-xs font-semibold uppercase tracking-wide text-muted" for="primary-hover-color">
                                {{ text('admin.theme.tokens.primary_hover', 'Primary hover color') }}
                            </label>
                            <input
                                id="primary-hover-color"
                                class="input mt-1 w-full px-3 py-2 text-sm"
                                type="color"
                                name="primary_hover_color"
                                value="{{ $primaryHoverColor }}"
                            >
                        </div>
                        <div>
                            <label class="text-xs font-semibold uppercase tracking-wide text-muted" for="accent-color">
                                {{ text('admin.theme.tokens.accent', 'Accent color') }}
                            </label>
                            <input
                                id="accent-color"
                                class="input mt-1 w-full px-3 py-2 text-sm"
                                type="color"
                                name="accent_color"
                                value="{{ $accentColor }}"
                            >
                        </div>
                        <div>
                            <label class="text-xs font-semibold uppercase tracking-wide text-muted" for="text-color">
                                {{ text('admin.theme.tokens.text', 'Primary text color') }}
                            </label>
                            <input
                                id="text-color"
                                class="input mt-1 w-full px-3 py-2 text-sm"
                                type="color"
                                name="text_color"
                                value="{{ $textColor }}"
                            >
                        </div>
                        <div>
                            <label class="text-xs font-semibold uppercase tracking-wide text-muted" for="border-color">
                                {{ text('admin.theme.tokens.border', 'Border color') }}
                            </label>
                            <input
                                id="border-color"
                                class="input mt-1 w-full px-3 py-2 text-sm"
                                type="color"
                                name="border_color"
                                value="{{ $borderColor }}"
                            >
                        </div>
                        <div>
                            <label class="text-xs font-semibold uppercase tracking-wide text-muted" for="ring-color">
                                {{ text('admin.theme.tokens.ring', 'Ring color (RGBA)') }}
                            </label>
                            <input
                                id="ring-color"
                                class="input mt-1 w-full px-3 py-2 text-sm"
                                type="text"
                                name="ring_color"
                                value="{{ old('ring_color', $settings->ring_color ?? 'rgba(5,150,105,0.18)') }}"
                            >
                        </div>
                        <div>
                            <label class="text-xs font-semibold uppercase tracking-wide text-muted" for="focus-ring-color">
                                {{ text('admin.theme.tokens.focus_ring', 'Focus ring color (RGBA)') }}
                            </label>
                            <input
                                id="focus-ring-color"
                                class="input mt-1 w-full px-3 py-2 text-sm"
                                type="text"
                                name="focus_ring_color"
                                value="{{ old('focus_ring_color', $settings->focus_ring_color ?? 'rgba(5,150,105,0.35)') }}"
                            >
                        </div>
                    </div>
                    <div class="grid gap-4 md:grid-cols-2">
                        <div>
                            <label class="text-xs font-semibold uppercase tracking-wide text-muted" for="button-radius">
                                {{ text('admin.theme.tokens.button_radius', 'Button radius') }}
                            </label>
                            <input
                                id="button-radius"
                                class="input mt-1 w-full px-3 py-2 text-sm"
                                type="text"
                                name="button_radius"
                                placeholder="9999px"
                                value="{{ old('button_radius', $settings->button_radius ?? '9999px') }}"
                            >
                        </div>
                        <div>
                            <label class="text-xs font-semibold uppercase tracking-wide text-muted" for="card-radius">
                                {{ text('admin.theme.tokens.card_radius', 'Card radius') }}
                            </label>
                            <input
                                id="card-radius"
                                class="input mt-1 w-full px-3 py-2 text-sm"
                                type="text"
                                name="card_radius"
                                placeholder="24px"
                                value="{{ old('card_radius', $settings->card_radius ?? '24px') }}"
                            >
                        </div>
                    </div>
                    <p class="text-xs text-muted">{{ text('admin.theme.tokens.note', 'Theme values update buttons, cards, borders, and focus states across the site.') }}</p>
                </div>

                <button class="btn-primary inline-flex items-center px-4 py-2 text-sm font-semibold" type="submit">
                    {{ text('admin.theme.save', 'Save Theme Settings') }}
                </button>
            </form>
        </section>
    </div>
@endsection
