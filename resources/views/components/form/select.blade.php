@props([
    'label' => null,
    'name',
    'options' => [],
    'value' => null,
    'placeholder' => 'Select an option',
    'required' => false,
])

<div>
    @if ($label)
        <x-form.label :value="$label" :for="$name" />
    @endif

    <select id="{{ $name }}" name="{{ $name }}" @required($required)
        {{ $attributes->class([
            'mt-1 block w-full rounded-xl border border-slate-300 bg-white px-3 py-2.5 text-sm text-slate-900 shadow-sm',
            'focus:border-brand-500 focus:outline-none focus:ring-2 focus:ring-brand-500/20',
        ]) }}>
        <option value="">
            {{ $placeholder }}
        </option>

        @foreach ($options as $optionValue => $optionLabel)
            <option value="{{ $optionValue }}" @selected(old($name, $value) == $optionValue)>
                {{ $optionLabel }}
            </option>
        @endforeach
    </select>

    <x-form.error :messages="$errors->get($name)" />
</div>
