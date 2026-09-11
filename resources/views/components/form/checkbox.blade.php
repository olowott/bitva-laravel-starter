@props(['name', 'label' => null, 'value' => 1, 'checked' => false])

<label class="flex items-start gap-3">
    <input type="checkbox" name="{{ $name }}" value="{{ $value }}" @checked(old($name, $checked))
        {{ $attributes->class(['mt-0.5 size-4 rounded border-slate-300 text-brand-600', 'focus:ring-brand-500']) }}>

    @if ($label)
        <span class="text-sm text-slate-700">
            {{ $label }}
        </span>
    @else
        <span class="text-sm text-slate-700">
            {{ $slot }}
        </span>
    @endif
</label>
