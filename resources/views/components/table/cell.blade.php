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

<td {{ $attributes->class(['px-6 py-4 text-sm text-slate-700', $alignment]) }}>
    {{ $slot }}
</td>
