@props([
    'label' => null,
    'name',
    'value' => null,
    'rows' => 4,
    'required' => false,
    'messages' => null,
])

@php
    $fieldErrors = $messages ?? $errors->get($name);
@endphp

<div>
    @if ($label)
        <x-form.label :value="$label" :for="$name" />
    @endif

    <textarea id="{{ $attributes->get('id', $name) }}" name="{{ $name }}" rows="{{ $rows }}" @required($required)
        {{ $attributes->except('id')->class([
            'mt-1 block w-full rounded-xl border bg-white px-3 py-2.5',
            'text-sm text-slate-900 shadow-sm transition',
            'placeholder:text-slate-400',
            'focus:outline-none focus:ring-2',
        
            'dark:bg-slate-900 dark:text-slate-100',
            'dark:placeholder:text-slate-500',
        
            'border-red-300 focus:border-red-500 focus:ring-red-500/20
                     dark:border-red-800 dark:focus:border-red-500' => $fieldErrors,
        
            'border-slate-300 focus:border-brand-500 focus:ring-brand-500/20
                     dark:border-slate-700 dark:focus:border-brand-500' => !$fieldErrors,
        ]) }}>{{ old($name, $value) }}</textarea>

    <x-form.error :messages="$fieldErrors" />
</div>
