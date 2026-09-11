@props([
    'padding' => true,
])

<div {{ $attributes->class(['app-card', 'p-6' => $padding]) }}>
    {{ $slot }}
</div>
