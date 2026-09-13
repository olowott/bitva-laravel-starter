<tr
    {{ $attributes->class(
        'border-b border-slate-100 transition last:border-b-0
             hover:bg-slate-50/60
             dark:border-slate-800 dark:hover:bg-slate-800/50',
    ) }}>
    {{ $slot }}
</tr>
