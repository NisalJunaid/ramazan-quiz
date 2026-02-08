@extends('layouts.app')

@section('content')
    <div class="mx-auto flex w-full max-w-5xl flex-col gap-6 px-4 sm:px-6 lg:px-8">
        <header class="flex flex-wrap items-start justify-between gap-4">
            <div>
                <p class="text-xs font-semibold uppercase tracking-[0.3em] text-amber-500">{{ text('admin.ads.create.overline', 'Advertising') }}</p>
                <h1 class="mt-2 text-2xl font-semibold text-emerald-700">{{ text('admin.ads.create.title', 'Create Ad') }}</h1>
                <p class="mt-2 text-sm text-gray-600">{{ text('admin.ads.create.subtitle', 'Upload creative and schedule when it should be shown.') }}</p>
            </div>
            <a class="text-sm font-semibold text-emerald-700 hover:text-emerald-800" href="{{ route('admin.ads.index') }}">
                {{ text('admin.ads.create.back', 'Back to Ads') }}
            </a>
        </header>

        <section class="rounded-2xl bg-white p-6 shadow-sm ring-1 ring-gray-200">
            <form class="grid gap-6" method="POST" action="{{ route('admin.ads.store') }}" enctype="multipart/form-data">
                @csrf

                @include('admin.ads._form')

                <button class="inline-flex items-center rounded-full bg-emerald-600 px-4 py-2 text-sm font-semibold text-white hover:bg-emerald-700" type="submit">
                    {{ text('admin.ads.create.submit', 'Save Ad') }}
                </button>
            </form>
        </section>
    </div>
@endsection
