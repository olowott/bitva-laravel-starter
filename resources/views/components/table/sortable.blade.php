@props(['column', 'label'])

@php
    $currentSort = request('sort');
    $currentDirection = request('direction', 'asc');

    $isActive = $currentSort === $column;

    $nextDirection = $isActive && $currentDirection === 'asc' ? 'desc' : 'asc';

    $url = request()->fullUrlWithQuery([
        'sort' => $column,
        'direction' => $nextDirection,
        'page' => null,
    ]);
@endphp

<a href="{{ $url }}"
    class="inline-flex items-center gap-1.5 transition
        hover:text-slate-700
        dark:hover:text-slate-200">
    <span>
        {{ $label }}
    </span>

    @if ($isActive)

        @if ($currentDirection === 'asc')
            <x-heroicon-o-chevron-up class="size-3.5" />
        @else
            <x-heroicon-o-chevron-down class="size-3.5" />
        @endif
    @else
        <x-heroicon-o-chevron-up-down class="size-3.5 text-slate-300 dark:text-slate-600" />

    @endif
</a>
