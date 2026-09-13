@props([
    'type' => 'info',
    'title' => null,
    'message' => null,
    'dismissible' => false,
])

@php
    $styles = match ($type) {
        'success' => [
            'wrapper' => 'border-emerald-200 bg-emerald-50 text-emerald-800
                 dark:border-emerald-900/70 dark:bg-emerald-950/40 dark:text-emerald-300',

            'icon' => 'text-emerald-600 dark:text-emerald-400',
        ],

        'warning' => [
            'wrapper' => 'border-amber-200 bg-amber-50 text-amber-800
                 dark:border-amber-900/70 dark:bg-amber-950/40 dark:text-amber-300',

            'icon' => 'text-amber-600 dark:text-amber-400',
        ],

        'danger' => [
            'wrapper' => 'border-red-200 bg-red-50 text-red-800
                 dark:border-red-900/70 dark:bg-red-950/40 dark:text-red-300',

            'icon' => 'text-red-600 dark:text-red-400',
        ],

        default => [
            'wrapper' => 'border-blue-200 bg-blue-50 text-blue-800
                 dark:border-blue-900/70 dark:bg-blue-950/40 dark:text-blue-300',

            'icon' => 'text-blue-600 dark:text-blue-400',
        ],
    };
@endphp

<div @if ($dismissible) x-data="{ show: true }"
        x-show="show"
        x-transition @endif
    {{ $attributes->class(['rounded-xl border px-4 py-3', $styles['wrapper']]) }}>
    <div class="flex items-start gap-3">

        <div class="mt-0.5 shrink-0 {{ $styles['icon'] }}">

            @switch($type)
                @case('success')
                    <x-heroicon-o-check-circle class="size-5" />
                @break

                @case('warning')
                    <x-heroicon-o-exclamation-triangle class="size-5" />
                @break

                @case('danger')
                    <x-heroicon-o-x-circle class="size-5" />
                @break

                @default
                    <x-heroicon-o-information-circle class="size-5" />
            @endswitch

        </div>

        <div class="min-w-0 flex-1">

            @if ($title)
                <p class="text-sm font-semibold">
                    {{ $title }}
                </p>
            @endif

            @if ($message)
                <p @class(['text-sm', 'mt-1' => $title])>
                    {{ $message }}
                </p>
            @endif

            @if ($slot->isNotEmpty())
                <div @class(['text-sm', 'mt-1' => $title || $message])>
                    {{ $slot }}
                </div>
            @endif

        </div>

        @if ($dismissible)
            <button type="button" @click="show = false"
                class="-mr-1 shrink-0 rounded-lg p-1 opacity-60 transition
                    hover:opacity-100
                    dark:hover:bg-white/5"
                aria-label="Dismiss">
                <x-heroicon-o-x-mark class="size-4" />
            </button>
        @endif

    </div>
</div>
