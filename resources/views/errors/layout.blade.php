@php
    $safeMode = isset($safeMode) && $safeMode;

    $appName = $safeMode ? config('app.name', 'Application') : setting('app_name', config('app.name'));

    $logo = $safeMode ? null : setting('logo');

    $favicon = $safeMode ? null : setting('favicon');

    $primaryColor = $safeMode ? '#4f5bd5' : setting('primary_color', '#4f5bd5');

    $secondaryColor = $safeMode ? '#111827' : setting('secondary_color', '#111827');
@endphp


<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">

    <meta name="viewport" content="width=device-width, initial-scale=1">

    <title>
        @yield('title') | {{ $appName }}
    </title>

    @if ($favicon)
        <link rel="icon" type="image/png" href="{{ Storage::disk('public_assets')->url($favicon) }}">
    @endif

    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <style>
        :root {
            --app-primary: {{ $primaryColor }};
            --app-secondary: {{ $secondaryColor }};
        }
    </style>
</head>

<body class="min-h-screen bg-slate-50 text-slate-900
    dark:bg-slate-950 dark:text-slate-100">

    <main class="flex min-h-screen items-center justify-center px-6 py-12">
        <div class="w-full max-w-lg text-center">

            {{-- Branding --}}
            <div class="mb-8 flex justify-center">

                @if ($logo)
                    <img src="{{ Storage::disk('public_assets')->url($logo) }}"
                        alt="{{ setting('app_name', 'Application') }}" class="max-h-12 max-w-52 object-contain">
                @else
                    <div class="flex items-center gap-3">

                        <div
                            class="flex size-10 items-center justify-center rounded-xl bg-brand-600 text-sm font-bold text-white">
                            {{ strtoupper(substr(setting('app_name', 'A'), 0, 1)) }}
                        </div>

                        <span class="text-sm font-semibold text-slate-900 dark:text-white">
                            {{ setting('app_name', config('app.name')) }}
                        </span>

                    </div>
                @endif

            </div>


            {{-- Error Code --}}
            <p class="text-sm font-semibold uppercase tracking-[0.2em] text-brand-600">
                Error @yield('code')
            </p>


            {{-- Heading --}}
            <h1 class="mt-3 text-3xl font-semibold tracking-tight text-slate-950 sm:text-4xl">
                @yield('heading')
            </h1>


            {{-- Description --}}
            <p class="mx-auto mt-4 max-w-md text-sm leading-6 text-slate-500 sm:text-base">
                @yield('message')
            </p>


            {{-- Actions --}}
            <div class="mt-8 flex flex-wrap justify-center gap-3">

                <a href="{{ url('/') }}"
                    class="inline-flex items-center justify-center rounded-xl bg-brand-600 px-4 py-2.5 text-sm font-medium text-white transition hover:bg-brand-700">
                    <x-heroicon-o-home class="mr-2 size-4" />

                    Go Home
                </a>

                <button type="button" onclick="window.history.back()"
                    class="inline-flex items-center justify-center rounded-xl border border-slate-300 bg-white px-4 py-2.5 text-sm font-medium text-slate-700 transition hover:bg-slate-50">
                    <x-heroicon-o-arrow-left class="mr-2 size-4" />

                    Go Back
                </button>

            </div>

        </div>
    </main>

</body>

</html>
