@props([
    'name' => null,
    'messages' => null,
])

@php
    $messages = $messages ?? ($name ? $errors->get($name) : []);
@endphp

@if ($messages)
    @foreach ((array) $messages as $message)
        <p {{ $attributes->class('mt-1 text-sm text-red-600 dark:text-red-400') }}>
            {{ $message }}
        </p>
    @endforeach
@endif
