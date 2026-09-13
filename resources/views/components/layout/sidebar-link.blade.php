@props(['href', 'active' => false])

<a href="{{ $href }}"
    {{ $attributes->class([
        'flex items-center gap-3 rounded-xl px-3 py-2.5 text-sm font-medium transition',
        'bg-brand-50 text-brand-700 dark:bg-brand-950/40 dark:text-brand-300' => $active,
        'text-slate-600 hover:bg-slate-100 hover:text-slate-900
             dark:text-slate-400 dark:hover:bg-slate-800 dark:hover:text-white' => !$active,
    ]) }}>
    {{ $slot }}
</a>
