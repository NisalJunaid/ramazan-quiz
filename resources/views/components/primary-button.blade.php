<button {{ $attributes->merge(['type' => 'submit', 'class' => 'btn-primary inline-flex items-center px-4 py-2 border border-transparent font-semibold text-xs uppercase tracking-widest transition ease-in-out duration-150']) }}>
    {{ $slot }}
</button>
