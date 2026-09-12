@props([
    'empty' => false,
    'emptyTitle' => 'No records found',
    'emptyDescription' => null,
])

<div class="overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm">

    @if ($empty)

        <div class="px-6 py-16 text-center">

            <div class="mx-auto flex size-12 items-center justify-center rounded-full bg-slate-100 text-slate-500">
                <x-heroicon-o-inbox class="size-6" />
            </div>

            <h3 class="mt-4 text-sm font-semibold text-slate-900">
                {{ $emptyTitle }}
            </h3>

            @if ($emptyDescription)
                <p class="mt-1 text-sm text-slate-500">
                    {{ $emptyDescription }}
                </p>
            @endif

        </div>
    @else
        <div class="overflow-x-auto">
            {{ $slot }}
        </div>

        @isset($footer)
            {{ $footer }}
        @endisset

    @endif

</div>
