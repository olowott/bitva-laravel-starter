@props([
    'label' => null,
    'name',
    'required' => false,
    'accept' => null,
    'messages' => null,
])

@php
    $fieldErrors = $messages ?? $errors->get($name);
@endphp

<div>
    @if ($label)
        <x-form.label :value="$label" :for="$name" />
    @endif

    <input id="{{ $attributes->get('id', $name) }}" name="{{ $name }}" type="file" @required($required)
        @if ($accept) accept="{{ $accept }}" @endif
        {{ $attributes->except('id')->class([
            'mt-1 block w-full rounded-xl border bg-white px-3 py-2.5',
            'text-sm text-slate-700 shadow-sm transition',
            'file:mr-4 file:rounded-lg file:border-0 file:bg-slate-100',
            'file:px-3 file:py-2 file:text-sm file:font-medium file:text-slate-700',
            'hover:file:bg-slate-200',
            'focus:outline-none focus:ring-2',
        
            'dark:bg-slate-900 dark:text-slate-300',
            'dark:file:bg-slate-800 dark:file:text-slate-300',
            'dark:hover:file:bg-slate-700',
        
            'border-red-300 focus:border-red-500 focus:ring-red-500/20
                     dark:border-red-800 dark:focus:border-red-500' => $fieldErrors,
        
            'border-slate-300 focus:border-brand-500 focus:ring-brand-500/20
                     dark:border-slate-700 dark:focus:border-brand-500' => !$fieldErrors,
        ]) }}>

    <x-form.error :messages="$fieldErrors" />
</div>
