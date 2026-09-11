@props([
    'type' => 'button',
    'variant' => 'primary',
])

@php
    $variants = [
        'primary' => 'bg-brand-600 text-white hover:bg-brand-700 focus:ring-brand-500',

        'secondary' => 'border border-slate-300 bg-white text-slate-700 hover:bg-slate-50 focus:ring-slate-400',

        'danger' => 'bg-red-600 text-white hover:bg-red-700 focus:ring-red-500',
    ];
@endphp

<button type="{{ $type }}"
    {{ $attributes->class([
        'inline-flex items-center justify-center rounded-xl px-4 py-2.5 text-sm font-semibold shadow-sm transition focus:outline-none focus:ring-2 focus:ring-offset-2 disabled:pointer-events-none disabled:opacity-50',
        $variants[$variant],
    ]) }}>
    {{ $slot }}
</button>
