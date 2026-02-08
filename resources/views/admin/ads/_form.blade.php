@php
    $ad = $ad ?? null;
    $startsAtValue = old('starts_at', $ad?->starts_at?->format('Y-m-d\TH:i'));
    $endsAtValue = old('ends_at', $ad?->ends_at?->format('Y-m-d\TH:i'));
@endphp

<div class="grid gap-4 md:grid-cols-2">
    <div>
        <label class="text-xs font-semibold uppercase tracking-wide text-gray-500" for="ad-name">
            {{ text('admin.ads.form.name', 'Name') }}
        </label>
        <input
            id="ad-name"
            class="mt-1 w-full rounded-xl border-gray-300 px-3 py-2 text-sm focus:border-emerald-500 focus:ring-emerald-500"
            type="text"
            name="name"
            value="{{ old('name', $ad?->name) }}"
        >
    </div>
    <div>
        <label class="text-xs font-semibold uppercase tracking-wide text-gray-500" for="ad-target-url">
            {{ text('admin.ads.form.target_url', 'Target URL') }}
        </label>
        <input
            id="ad-target-url"
            class="mt-1 w-full rounded-xl border-gray-300 px-3 py-2 text-sm focus:border-emerald-500 focus:ring-emerald-500"
            type="url"
            name="target_url"
            value="{{ old('target_url', $ad?->target_url) }}"
            placeholder="https://example.com"
        >
    </div>
    <div>
        <label class="text-xs font-semibold uppercase tracking-wide text-gray-500" for="ad-alt-text">
            {{ text('admin.ads.form.alt_text', 'Alt text') }}
        </label>
        <input
            id="ad-alt-text"
            class="mt-1 w-full rounded-xl border-gray-300 px-3 py-2 text-sm focus:border-emerald-500 focus:ring-emerald-500"
            type="text"
            name="alt_text"
            value="{{ old('alt_text', $ad?->alt_text) }}"
            placeholder="{{ text('admin.ads.form.alt_text_placeholder', 'Homepage ad') }}"
        >
    </div>
    <div>
        <label class="text-xs font-semibold uppercase tracking-wide text-gray-500" for="ad-image">
            {{ text('admin.ads.form.image', 'Ad image') }}
        </label>
        <input
            id="ad-image"
            class="mt-1 w-full rounded-xl border-gray-300 px-3 py-2 text-sm focus:border-emerald-500 focus:ring-emerald-500"
            type="file"
            name="image"
            accept="image/png,image/jpeg,image/webp"
            {{ $ad ? '' : 'required' }}
        >
        <p class="mt-1 text-xs text-gray-500">
            {{ text('admin.ads.form.image_hint', 'PNG, JPG, or WEBP up to 2MB.') }}
        </p>
        @if ($ad?->image_path)
            <div class="mt-3 flex items-center gap-3">
                <img
                    class="h-16 w-24 rounded-lg border object-cover"
                    src="{{ \Illuminate\Support\Facades\Storage::disk('public')->url($ad->image_path) }}"
                    alt="{{ $ad->alt_text ?: ($ad->name ?? 'Current ad image') }}"
                >
                <p class="text-xs text-gray-500">{{ text('admin.ads.form.current_image', 'Current image') }}</p>
            </div>
        @endif
    </div>
    <div>
        <label class="text-xs font-semibold uppercase tracking-wide text-gray-500" for="ad-starts-at">
            {{ text('admin.ads.form.starts_at', 'Starts at') }}
        </label>
        <input
            id="ad-starts-at"
            class="mt-1 w-full rounded-xl border-gray-300 px-3 py-2 text-sm focus:border-emerald-500 focus:ring-emerald-500"
            type="datetime-local"
            name="starts_at"
            value="{{ $startsAtValue }}"
        >
    </div>
    <div>
        <label class="text-xs font-semibold uppercase tracking-wide text-gray-500" for="ad-ends-at">
            {{ text('admin.ads.form.ends_at', 'Ends at') }}
        </label>
        <input
            id="ad-ends-at"
            class="mt-1 w-full rounded-xl border-gray-300 px-3 py-2 text-sm focus:border-emerald-500 focus:ring-emerald-500"
            type="datetime-local"
            name="ends_at"
            value="{{ $endsAtValue }}"
        >
    </div>
</div>

<label class="flex items-center gap-2 text-sm font-medium text-gray-700">
    <input
        class="h-4 w-4 rounded border-gray-300 text-emerald-600 focus:ring-emerald-500"
        type="checkbox"
        name="is_active"
        value="1"
        {{ old('is_active', $ad?->is_active ?? true) ? 'checked' : '' }}
    >
    {{ text('admin.ads.form.is_active', 'Active') }}
</label>
