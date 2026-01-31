@props(['active'])

@php
$classes = ($active ?? false)
            ? 'nav-link nav-link-active block w-full px-4 py-2 text-start text-base font-semibold focus:outline-none transition duration-150 ease-in-out'
            : 'nav-link block w-full px-4 py-2 text-start text-base font-medium focus:outline-none transition duration-150 ease-in-out';
@endphp

<a {{ $attributes->merge(['class' => $classes]) }}>
    {{ $slot }}
</a>
