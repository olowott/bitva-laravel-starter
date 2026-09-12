@props([
    'href' => null,
    'type' => 'button',
    'variant' => 'primary',
])

@php
    $classes =
        'inline-flex items-center justify-center rounded-xl px-4 py-2.5 text-sm font-medium transition focus:outline-none focus:ring-2 focus:ring-offset-2';

    $variantClasses = match ($variant) {
        'secondary' => 'border border-slate-300 bg-white text-slate-700 hover:bg-slate-50 focus:ring-slate-400',
        'danger' => 'bg-red-600 text-white hover:bg-red-700 focus:ring-red-500',
        default => 'bg-brand-600 text-white hover:bg-brand-700 focus:ring-brand-500',
    };

    $classes .= ' ' . $variantClasses;
@endphp

@if ($href)
    <a href="{{ $href }}" {{ $attributes->except(['href', 'type'])->class($classes) }}>
        {{ $slot }}
    </a>
@else
    <button type="{{ $type }}" {{ $attributes->except(['href'])->class($classes) }}>
        {{ $slot }}
    </button>
@endif
