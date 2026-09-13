@props([
    'label' => null,
    'name',
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

    <select id="{{ $attributes->get('id', $name) }}" name="{{ $name }}" @required($required)
        {{ $attributes->except('id')->class([
            'mt-1 block w-full rounded-xl border bg-white px-3 py-2.5',
            'text-sm text-slate-900 shadow-sm transition',
            'focus:outline-none focus:ring-2',
            'disabled:cursor-not-allowed disabled:bg-slate-100 disabled:text-slate-500',
        
            'dark:bg-slate-900 dark:text-slate-100',
            'dark:disabled:bg-slate-800 dark:disabled:text-slate-500',
        
            'border-red-300 focus:border-red-500 focus:ring-red-500/20
                     dark:border-red-800 dark:focus:border-red-500' => $fieldErrors,
        
            'border-slate-300 focus:border-brand-500 focus:ring-brand-500/20
                     dark:border-slate-700 dark:focus:border-brand-500' => !$fieldErrors,
        ]) }}>
        {{ $slot }}
    </select>

    <x-form.error :messages="$fieldErrors" />
</div>
