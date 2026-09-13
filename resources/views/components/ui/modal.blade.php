@props(['name', 'title' => null, 'maxWidth' => 'md'])

@php
    $maxWidthClass = match ($maxWidth) {
        'sm' => 'sm:max-w-sm',
        'lg' => 'sm:max-w-lg',
        'xl' => 'sm:max-w-xl',
        '2xl' => 'sm:max-w-2xl',
        default => 'sm:max-w-md',
    };
@endphp

<div x-data="{ open: false }"
    @open-modal.window="
        if ($event.detail === '{{ $name }}') {
            open = true
        }
    "
    @close-modal.window="
        if ($event.detail === '{{ $name }}') {
            open = false
        }
    "
    @keydown.escape.window="open = false">
    <template x-teleport="body">

        <div x-show="open" x-cloak class="fixed inset-0 z-[60] flex items-center justify-center p-4" role="dialog"
            aria-modal="true">

            {{-- Backdrop --}}
            <div x-show="open" x-transition.opacity class="absolute inset-0 bg-slate-950/65" @click="open = false">
            </div>

            {{-- Modal Panel --}}
            <div x-show="open" x-transition:enter="transition ease-out duration-200"
                x-transition:enter-start="opacity-0 scale-95" x-transition:enter-end="opacity-100 scale-100"
                x-transition:leave="transition ease-in duration-150" x-transition:leave-start="opacity-100 scale-100"
                x-transition:leave-end="opacity-0 scale-95" @click.stop
                class="relative z-[70] w-full {{ $maxWidthClass }}
                    overflow-hidden rounded-2xl
                    bg-white shadow-2xl
                    dark:border dark:border-slate-800
                    dark:bg-slate-900">

                @if ($title)
                    <div
                        class="flex items-center justify-between
                            border-b border-slate-200 px-6 py-4
                            dark:border-slate-800">
                        <h2
                            class="text-base font-semibold
                                text-slate-900 dark:text-white">
                            {{ $title }}
                        </h2>

                        <button type="button" @click="open = false"
                            class="rounded-lg p-1.5
                                text-slate-400 transition
                                hover:bg-slate-100 hover:text-slate-600
                                dark:text-slate-500
                                dark:hover:bg-slate-800
                                dark:hover:text-slate-300"
                            aria-label="Close modal">
                            <x-heroicon-o-x-mark class="size-5" />
                        </button>
                    </div>
                @endif

                <div class="p-6 text-slate-700 dark:text-slate-300">
                    {{ $slot }}
                </div>

            </div>

        </div>

    </template>
</div>
