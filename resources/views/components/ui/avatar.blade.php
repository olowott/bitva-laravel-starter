@props(['user', 'size' => 'md'])

@php
    $sizes = [
        'xs' => 'h-7 w-7 text-xs',
        'sm' => 'h-8 w-8 text-xs',
        'md' => 'h-10 w-10 text-sm',
        'lg' => 'h-14 w-14 text-lg',
        'xl' => 'h-20 w-20 text-2xl',
    ];

    $sizeClass = $sizes[$size] ?? $sizes['md'];

    $initials = collect(explode(' ', trim($user->name ?? '')))
        ->filter()
        ->take(2)
        ->map(fn($word) => strtoupper(substr($word, 0, 1)))
        ->implode('');

    $avatarUrl = $user->avatar ? \Illuminate\Support\Facades\Storage::disk('public_assets')->url($user->avatar) : null;
@endphp

@if ($avatarUrl)
    <img src="{{ $avatarUrl }}" alt="{{ $user->name }}"
        {{ $attributes->class([$sizeClass, 'shrink-0 rounded-full object-cover ring-1 ring-slate-200']) }}>
@else
    <div
        {{ $attributes->class([
            $sizeClass,
            'flex shrink-0 items-center justify-center rounded-full bg-brand-100 font-semibold text-brand-700 ring-1 ring-brand-200',
        ]) }}>
        {{ $initials ?: '?' }}
    </div>
@endif
