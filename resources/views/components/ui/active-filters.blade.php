@props([
    'filters' => [],
    'clearUrl' => null,
])

@php
    $activeFilters = collect($filters)->filter(fn($value) => filled($value));
@endphp

@if ($activeFilters->isNotEmpty())

    <div
        {{ $attributes->class(
            'flex flex-wrap items-center gap-2 rounded-xl border border-slate-200 bg-white px-4 py-3',
        ) }}>
        <span class="text-xs font-semibold uppercase tracking-wide text-slate-400">
            Active filters
        </span>

        @foreach ($activeFilters as $label => $value)
            <span
                class="inline-flex items-center rounded-lg bg-slate-100 px-2.5 py-1 text-xs font-medium text-slate-600">
                {{ $label }}:
                <span class="ml-1 text-slate-900">
                    {{ $value }}
                </span>
            </span>
        @endforeach

        @if ($clearUrl)
            <a href="{{ $clearUrl }}" class="ml-auto text-xs font-semibold text-brand-600 hover:text-brand-700">
                Clear all
            </a>
        @endif

    </div>

@endif
