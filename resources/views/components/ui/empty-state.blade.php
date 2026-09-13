@props([
    'title' => 'Nothing here yet',
    'description' => null,
    'icon' => 'inbox',
])

<div
    {{ $attributes->class([
        'rounded-2xl border border-dashed border-slate-300
             bg-white px-6 py-12 text-center
             dark:border-slate-700 dark:bg-slate-900',
    ]) }}>
    <div
        class="mx-auto flex size-12 items-center justify-center
            rounded-2xl bg-slate-100 text-slate-400
            dark:bg-slate-800 dark:text-slate-500">
        @switch($icon)
            @case('users')
                <x-heroicon-o-users class="size-6" />
            @break

            @case('bell')
                <x-heroicon-o-bell class="size-6" />
            @break

            @case('document')
                <x-heroicon-o-document class="size-6" />
            @break

            @case('folder')
                <x-heroicon-o-folder class="size-6" />
            @break

            @case('search')
                <x-heroicon-o-magnifying-glass class="size-6" />
            @break

            @default
                <x-heroicon-o-inbox class="size-6" />
        @endswitch
    </div>

    <h3 class="mt-4 text-sm font-semibold
            text-slate-900 dark:text-white">
        {{ $title }}
    </h3>

    @if ($description)
        <p class="mx-auto mt-1 max-w-md text-sm
                text-slate-500 dark:text-slate-400">
            {{ $description }}
        </p>
    @endif

    @if ($slot->isNotEmpty())
        <div class="mt-5 flex justify-center">
            {{ $slot }}
        </div>
    @endif
</div>
