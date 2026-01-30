@props(['active'])

@php
$classes = ($active ?? false)
            ? 'block w-full rounded-lg px-4 py-2 text-start text-base font-semibold text-emerald-700 bg-emerald-50 border border-emerald-200 focus:outline-none focus:ring-2 focus:ring-emerald-200 transition duration-150 ease-in-out'
            : 'block w-full rounded-lg px-4 py-2 text-start text-base font-medium text-gray-600 hover:text-gray-800 hover:bg-emerald-50/70 border border-transparent focus:outline-none focus:ring-2 focus:ring-emerald-100 transition duration-150 ease-in-out';
@endphp

<a {{ $attributes->merge(['class' => $classes]) }}>
    {{ $slot }}
</a>
