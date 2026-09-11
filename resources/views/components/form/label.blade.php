@props([
    'value' => null,
])

<label {{ $attributes->class('block text-sm font-medium text-slate-700') }}>
    {{ $value ?? $slot }}
</label>
