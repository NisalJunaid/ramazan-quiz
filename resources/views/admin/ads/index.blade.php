@extends('layouts.app')

@section('content')
    <div class="mx-auto flex w-full max-w-6xl flex-col gap-6 px-4 sm:px-6 lg:px-8">
        <header class="flex flex-wrap items-start justify-between gap-4">
            <div>
                <p class="text-xs font-semibold uppercase tracking-[0.3em] text-amber-500">{{ text('admin.ads.overline', 'Advertising') }}</p>
                <h1 class="mt-2 text-2xl font-semibold text-emerald-700">{{ text('admin.ads.title', 'Ad Manager') }}</h1>
                <p class="mt-2 text-sm text-gray-600">{{ text('admin.ads.subtitle', 'Upload and schedule ads for the homepage slot.') }}</p>
            </div>
            <div class="flex flex-wrap items-center gap-3">
                <a class="text-sm font-semibold text-emerald-700 hover:text-emerald-800" href="{{ route('admin.index') }}">
                    {{ text('admin.ads.back', 'Back to Admin') }}
                </a>
                <a class="inline-flex items-center rounded-full bg-emerald-600 px-4 py-2 text-sm font-semibold text-white hover:bg-emerald-700" href="{{ route('admin.ads.create') }}">
                    {{ text('admin.ads.create_link', 'Create Ad') }}
                </a>
            </div>
        </header>

        @if (session('status'))
            <div class="rounded-2xl border px-4 py-3 text-sm" style="border-color: var(--color-ring); background: var(--color-surface-tint); color: var(--color-primary);">
                {{ session('status') }}
            </div>
        @endif

        <section class="rounded-2xl bg-white p-6 shadow-sm ring-1 ring-gray-200">
            <div class="flex flex-wrap items-center justify-between gap-3">
                <h2 class="text-lg font-semibold text-gray-900">{{ text('admin.ads.list.title', 'All ads') }}</h2>
                <span class="text-xs text-gray-500">{{ $ads->count() }} {{ text('admin.ads.list.count', 'ads') }}</span>
            </div>

            @if ($ads->isEmpty())
                <p class="mt-4 text-sm text-gray-600">{{ text('admin.ads.list.empty', 'No ads created yet.') }}</p>
            @else
                <div class="mt-4 overflow-x-auto">
                    <table class="min-w-full text-sm">
                        <thead class="border-b border-gray-200 text-start text-xs uppercase tracking-widest text-gray-500">
                            <tr>
                                <th class="px-4 py-3">{{ text('admin.ads.list.headers.preview', 'Preview') }}</th>
                                <th class="px-4 py-3">{{ text('admin.ads.list.headers.name', 'Name') }}</th>
                                <th class="px-4 py-3">{{ text('admin.ads.list.headers.status', 'Status') }}</th>
                                <th class="px-4 py-3">{{ text('admin.ads.list.headers.schedule', 'Schedule') }}</th>
                                <th class="px-4 py-3">{{ text('admin.ads.list.headers.actions', 'Actions') }}</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100">
                            @foreach ($ads as $ad)
                                <tr class="bg-white">
                                    <td class="px-4 py-3">
                                        <img
                                            class="h-12 w-20 rounded-lg border object-cover"
                                            src="{{ \Illuminate\Support\Facades\Storage::disk('public')->url($ad->image_path) }}"
                                            alt="{{ $ad->alt_text ?: ($ad->name ?? 'Ad preview') }}"
                                        >
                                    </td>
                                    <td class="px-4 py-3 font-semibold text-gray-900">
                                        {{ $ad->name ?? text('admin.ads.list.unnamed', 'Untitled ad') }}
                                        @if ($ad->target_url)
                                            <p class="mt-1 text-xs text-gray-500">{{ $ad->target_url }}</p>
                                        @endif
                                    </td>
                                    <td class="px-4 py-3">
                                        @if ($ad->is_active)
                                            <span class="inline-flex items-center rounded-full bg-emerald-50 px-2 py-1 text-xs font-semibold text-emerald-700">
                                                {{ text('admin.ads.list.status.active', 'Active') }}
                                            </span>
                                        @else
                                            <span class="inline-flex items-center rounded-full bg-gray-100 px-2 py-1 text-xs font-semibold text-gray-600">
                                                {{ text('admin.ads.list.status.inactive', 'Inactive') }}
                                            </span>
                                        @endif
                                    </td>
                                    <td class="px-4 py-3 text-sm text-gray-600">
                                        <div>{{ $ad->starts_at ? $ad->starts_at->format('M d, Y H:i') : text('admin.ads.list.schedule.start_any', 'Starts anytime') }}</div>
                                        <div>{{ $ad->ends_at ? $ad->ends_at->format('M d, Y H:i') : text('admin.ads.list.schedule.end_any', 'No end date') }}</div>
                                    </td>
                                    <td class="px-4 py-3">
                                        <div class="flex flex-wrap items-center gap-2">
                                            <a
                                                class="inline-flex items-center rounded-full border border-emerald-200 px-3 py-1 text-xs font-semibold text-emerald-700 hover:bg-emerald-50"
                                                href="{{ route('admin.ads.edit', $ad) }}"
                                            >
                                                {{ text('admin.ads.list.edit', 'Edit') }}
                                            </a>
                                            <form method="POST" action="{{ route('admin.ads.destroy', $ad) }}" onsubmit="return confirm('{{ text('admin.ads.list.confirm', 'Delete this ad?') }}')">
                                                @csrf
                                                @method('DELETE')
                                                <button class="inline-flex items-center rounded-full border border-rose-300 px-3 py-1 text-xs font-semibold text-rose-600 hover:bg-rose-50" type="submit">
                                                    {{ text('admin.ads.list.delete', 'Delete') }}
                                                </button>
                                            </form>
                                        </div>
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
