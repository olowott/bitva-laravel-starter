@props([
    'showName' => true,
    'logoClass' => 'h-9 w-auto max-w-40',
])

<div class="flex items-center gap-3">

    @if (setting('logo'))

        <img src="{{ Storage::disk('public_assets')->url(setting('logo')) }}"
            alt="{{ setting('app_name', 'Application') }}" class="{{ $logoClass }} object-contain">
    @else
        <div
            class="flex size-9 shrink-0 items-center justify-center rounded-xl bg-brand-600 text-sm font-bold text-white">
            {{ strtoupper(substr(setting('app_name', 'A'), 0, 1)) }}
        </div>

        @if ($showName)
            <span class="truncate text-sm font-semibold text-slate-900 dark:text-white">
                {{ setting('app_name', 'Application') }}
            </span>
        @endif

    @endif

</div>
