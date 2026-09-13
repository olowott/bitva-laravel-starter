@props(['name', 'label' => null, 'value' => 1, 'checked' => false])

<label class="flex items-start gap-3">
    <input type="checkbox" name="{{ $name }}" value="{{ $value }}" @checked(old($name, $checked))
        {{ $attributes->class([
            'mt-0.5 size-4 rounded border-slate-300 text-brand-600',
            'focus:ring-brand-500',
            'dark:border-slate-700 dark:bg-slate-900',
            'dark:checked:bg-brand-600 dark:checked:border-brand-600',
            'dark:focus:ring-brand-500',
        ]) }}>

    <span class="text-sm text-slate-700 dark:text-slate-300">
        {{ $label ?? $slot }}
    </span>
</label>
