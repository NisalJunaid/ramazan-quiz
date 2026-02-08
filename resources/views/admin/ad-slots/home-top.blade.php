@extends('layouts.app')

@section('content')
    @php
        $selectedMode = old('mode', $slot->mode);
        $selectedStrategy = old('rotation_strategy', $slot->rotation_strategy);
        $rotationSeconds = old('rotation_seconds', $slot->rotation_seconds);
        $fixedAdId = old('fixed_ad_id', $slot->fixed_ad_id);
    @endphp

    <div class="mx-auto flex w-full max-w-6xl flex-col gap-6 px-4 sm:px-6 lg:px-8">
        <header class="flex flex-wrap items-start justify-between gap-4">
            <div>
                <p class="text-xs font-semibold uppercase tracking-[0.3em] text-amber-500">{{ text('admin.ad_slots.overline', 'Advertising') }}</p>
                <h1 class="mt-2 text-2xl font-semibold text-emerald-700">{{ text('admin.ad_slots.title', 'Homepage Top Ad Slot') }}</h1>
                <p class="mt-2 text-sm text-gray-600">{{ text('admin.ad_slots.subtitle', 'Configure which ads appear above the homepage hero card.') }}</p>
            </div>
            <a class="text-sm font-semibold text-emerald-700 hover:text-emerald-800" href="{{ route('admin.index') }}">
                {{ text('admin.ad_slots.back', 'Back to Admin') }}
            </a>
        </header>

        @if (session('status'))
            <div class="rounded-2xl border px-4 py-3 text-sm" style="border-color: var(--color-ring); background: var(--color-surface-tint); color: var(--color-primary);">
                {{ session('status') }}
            </div>
        @endif

        <section class="rounded-2xl bg-white p-6 shadow-sm ring-1 ring-gray-200">
            <form class="grid gap-6" method="POST" action="{{ route('admin.ad-slots.home-top.update') }}">
                @csrf
                @method('PUT')

                <div class="grid gap-4 md:grid-cols-2">
                    <div>
                        <label class="text-xs font-semibold uppercase tracking-wide text-gray-500" for="slot-mode">
                            {{ text('admin.ad_slots.mode', 'Mode') }}
                        </label>
                        <select
                            id="slot-mode"
                            class="mt-1 w-full rounded-xl border-gray-300 px-3 py-2 text-sm focus:border-emerald-500 focus:ring-emerald-500"
                            name="mode"
                            required
                        >
                            <option value="fixed" {{ $selectedMode === 'fixed' ? 'selected' : '' }}>{{ text('admin.ad_slots.mode_fixed', 'Fixed') }}</option>
                            <option value="rotating" {{ $selectedMode === 'rotating' ? 'selected' : '' }}>{{ text('admin.ad_slots.mode_rotating', 'Rotating') }}</option>
                        </select>
                    </div>
                    <div>
                        <label class="text-xs font-semibold uppercase tracking-wide text-gray-500" for="rotation-strategy">
                            {{ text('admin.ad_slots.rotation_strategy', 'Rotation strategy') }}
                        </label>
                        <select
                            id="rotation-strategy"
                            class="mt-1 w-full rounded-xl border-gray-300 px-3 py-2 text-sm focus:border-emerald-500 focus:ring-emerald-500"
                            name="rotation_strategy"
                            required
                        >
                            <option value="random" {{ $selectedStrategy === 'random' ? 'selected' : '' }}>{{ text('admin.ad_slots.rotation_random', 'Random') }}</option>
                            <option value="sequential" {{ $selectedStrategy === 'sequential' ? 'selected' : '' }}>{{ text('admin.ad_slots.rotation_sequential', 'Sequential') }}</option>
                        </select>
                    </div>
                </div>

                <div class="grid gap-4 md:grid-cols-2">
                    <div>
                        <label class="text-xs font-semibold uppercase tracking-wide text-gray-500" for="rotation-seconds">
                            {{ text('admin.ad_slots.rotation_seconds', 'Rotation seconds (optional)') }}
                        </label>
                        <input
                            id="rotation-seconds"
                            class="mt-1 w-full rounded-xl border-gray-300 px-3 py-2 text-sm focus:border-emerald-500 focus:ring-emerald-500"
                            type="number"
                            name="rotation_seconds"
                            min="1"
                            value="{{ $rotationSeconds }}"
                            placeholder="60"
                        >
                        <p class="mt-1 text-xs text-gray-500">{{ text('admin.ad_slots.rotation_seconds_hint', 'Leave blank to rotate on each page load.') }}</p>
                    </div>
                </div>

                <div class="grid gap-4 rounded-2xl border border-dashed p-4" data-mode-section="fixed">
                    <div>
                        <h3 class="text-base font-semibold text-gray-900">{{ text('admin.ad_slots.fixed.title', 'Fixed ad') }}</h3>
                        <p class="mt-1 text-sm text-gray-600">{{ text('admin.ad_slots.fixed.subtitle', 'Select a single active ad to display every time.') }}</p>
                    </div>
                    <div>
                        <label class="text-xs font-semibold uppercase tracking-wide text-gray-500" for="fixed-ad-id">
                            {{ text('admin.ad_slots.fixed.select', 'Select ad') }}
                        </label>
                        <select
                            id="fixed-ad-id"
                            class="mt-1 w-full rounded-xl border-gray-300 px-3 py-2 text-sm focus:border-emerald-500 focus:ring-emerald-500"
                            name="fixed_ad_id"
                        >
                            <option value="">{{ text('admin.ad_slots.fixed.none', 'No ad selected') }}</option>
                            @foreach ($ads as $ad)
                                <option value="{{ $ad->id }}" {{ (string) $fixedAdId === (string) $ad->id ? 'selected' : '' }}>
                                    {{ $ad->name ?? text('admin.ad_slots.fixed.untitled', 'Untitled ad') }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                </div>

                <div class="grid gap-4 rounded-2xl border border-dashed p-4" data-mode-section="rotating">
                    <div>
                        <h3 class="text-base font-semibold text-gray-900">{{ text('admin.ad_slots.rotating.title', 'Rotating ads') }}</h3>
                        <p class="mt-1 text-sm text-gray-600">{{ text('admin.ad_slots.rotating.subtitle', 'Pick multiple ads and set their weight or sort order.') }}</p>
                    </div>

                    @if ($ads->isEmpty())
                        <p class="text-sm text-gray-600">{{ text('admin.ad_slots.rotating.empty', 'No active ads available. Create ads first.') }}</p>
                    @else
                        <div class="overflow-x-auto">
                            <table class="min-w-full text-sm">
                                <thead class="border-b border-gray-200 text-start text-xs uppercase tracking-widest text-gray-500">
                                    <tr>
                                        <th class="px-4 py-3">{{ text('admin.ad_slots.rotating.headers.enable', 'Enable') }}</th>
                                        <th class="px-4 py-3">{{ text('admin.ad_slots.rotating.headers.preview', 'Preview') }}</th>
                                        <th class="px-4 py-3">{{ text('admin.ad_slots.rotating.headers.name', 'Name') }}</th>
                                        <th class="px-4 py-3">{{ text('admin.ad_slots.rotating.headers.weight', 'Weight') }}</th>
                                        <th class="px-4 py-3">{{ text('admin.ad_slots.rotating.headers.sort', 'Sort order') }}</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-gray-100">
                                    @foreach ($ads as $ad)
                                        @php
                                            $adPivot = $slotAds->get($ad->id)?->pivot;
                                            $isEnabled = old("ads.{$ad->id}.is_enabled", $adPivot?->is_enabled ?? false);
                                            $weightValue = old("ads.{$ad->id}.weight", $adPivot?->weight ?? 1);
                                            $sortOrderValue = old("ads.{$ad->id}.sort_order", $adPivot?->sort_order ?? 0);
                                        @endphp
                                        <tr class="bg-white">
                                            <td class="px-4 py-3">
                                                <input type="hidden" name="ads[{{ $ad->id }}][is_enabled]" value="0">
                                                <input
                                                    class="h-4 w-4 rounded border-gray-300 text-emerald-600 focus:ring-emerald-500"
                                                    type="checkbox"
                                                    name="ads[{{ $ad->id }}][is_enabled]"
                                                    value="1"
                                                    {{ $isEnabled ? 'checked' : '' }}
                                                >
                                            </td>
                                            <td class="px-4 py-3">
                                                <img
                                                    class="h-12 w-20 rounded-lg border object-cover"
                                                    src="{{ \Illuminate\Support\Facades\Storage::disk('public')->url($ad->image_path) }}"
                                                    alt="{{ $ad->alt_text ?: ($ad->name ?? 'Ad preview') }}"
                                                >
                                            </td>
                                            <td class="px-4 py-3 font-semibold text-gray-900">
                                                {{ $ad->name ?? text('admin.ad_slots.rotating.untitled', 'Untitled ad') }}
                                            </td>
                                            <td class="px-4 py-3">
                                                <input
                                                    class="w-24 rounded-xl border-gray-300 px-3 py-2 text-sm focus:border-emerald-500 focus:ring-emerald-500"
                                                    type="number"
                                                    name="ads[{{ $ad->id }}][weight]"
                                                    min="1"
                                                    value="{{ $weightValue }}"
                                                >
                                            </td>
                                            <td class="px-4 py-3">
                                                <input
                                                    class="w-24 rounded-xl border-gray-300 px-3 py-2 text-sm focus:border-emerald-500 focus:ring-emerald-500"
                                                    type="number"
                                                    name="ads[{{ $ad->id }}][sort_order]"
                                                    min="0"
                                                    value="{{ $sortOrderValue }}"
                                                >
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @endif
                </div>

                <button class="inline-flex items-center rounded-full bg-emerald-600 px-4 py-2 text-sm font-semibold text-white hover:bg-emerald-700" type="submit">
                    {{ text('admin.ad_slots.submit', 'Save Slot Settings') }}
                </button>
            </form>
        </section>
    </div>

    <script>
        const modeSelect = document.getElementById('slot-mode');
        const modeSections = document.querySelectorAll('[data-mode-section]');

        const toggleSections = () => {
            modeSections.forEach((section) => {
                const targetMode = section.getAttribute('data-mode-section');
                section.classList.toggle('hidden', modeSelect.value !== targetMode);
            });
        };

        toggleSections();
        modeSelect.addEventListener('change', toggleSections);
    </script>
@endsection
