@props(['action', 'method' => 'GET'])

<x-ui.card>
    <form method="{{ $method }}" action="{{ $action }}" {{ $attributes->class('grid gap-4 md:grid-cols-4') }}>
        {{ $slot }}
    </form>
</x-ui.card>
