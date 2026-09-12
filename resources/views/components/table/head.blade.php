@props([
    'align' => 'left',
])

@php
    $alignment = match ($align) {
        'right' => 'text-right',
        'center' => 'text-center',
        default => 'text-left',
    };
@endphp

<th
    {{ $attributes->class(['px-6 py-3 text-xs font-semibold uppercase tracking-wider text-slate-500', $alignment]) }}>
    {{ $slot }}
</th>
