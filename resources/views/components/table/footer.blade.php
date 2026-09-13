@props(['paginator'])

@if ($paginator->total() > 0)
    <div
        {{ $attributes->class(
            'flex flex-col gap-4 border-t border-slate-200 px-6 py-4
                     dark:border-slate-800
                     sm:flex-row sm:items-center sm:justify-between',
        ) }}>
        <p class="text-sm text-slate-500 dark:text-slate-400">
            Showing

            <span class="font-medium text-slate-700 dark:text-slate-300">
                {{ $paginator->firstItem() }}
            </span>

            to

            <span class="font-medium text-slate-700 dark:text-slate-300">
                {{ $paginator->lastItem() }}
            </span>

            of

            <span class="font-medium text-slate-700 dark:text-slate-300">
                {{ $paginator->total() }}
            </span>

            results
        </p>

        @if ($paginator->hasPages())
            <div>
                {{ $paginator->links() }}
            </div>
        @endif
    </div>
@endif
