@props(['href', 'active' => false])

<a href="{{ $href }}"
    {{ $attributes->class([
        'flex items-center gap-3 rounded-xl px-3 py-2.5 text-sm font-medium transition',
        'bg-brand-50 text-brand-700' => $active,
        'text-slate-600 hover:bg-slate-100 hover:text-slate-900' => !$active,
    ]) }}>
    {{ $slot }}
</a>
