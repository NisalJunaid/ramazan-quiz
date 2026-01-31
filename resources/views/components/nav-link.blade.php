@props(['active'])

@php
$classes = ($active ?? false)
            ? 'nav-link nav-link-active inline-flex items-center px-3 py-2 text-sm font-semibold focus:outline-none transition duration-150 ease-in-out'
            : 'nav-link inline-flex items-center px-3 py-2 text-sm font-medium focus:outline-none transition duration-150 ease-in-out';
@endphp

<a {{ $attributes->merge(['class' => $classes]) }}>
    {{ $slot }}
</a>
