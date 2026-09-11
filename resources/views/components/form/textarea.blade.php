@props([
    'label' => null,
    'name',
    'value' => null,
    'rows' => 4,
    'required' => false,
])

<div>
    @if ($label)
        <x-form.label :value="$label" :for="$name" />
    @endif

    <textarea id="{{ $name }}" name="{{ $name }}" rows="{{ $rows }}" @required($required)
        {{ $attributes->class([
            'mt-1 block w-full rounded-xl border border-slate-300 bg-white px-3 py-2.5 text-sm text-slate-900 shadow-sm',
            'placeholder:text-slate-400',
            'focus:border-brand-500 focus:outline-none focus:ring-2 focus:ring-brand-500/20',
        ]) }}>{{ old($name, $value) }}</textarea>

    <x-form.error :messages="$errors->get($name)" />
</div>
