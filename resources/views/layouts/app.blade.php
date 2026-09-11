<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>
        @yield('title', config('app.name'))
        | {{ config('app.name') }}
    </title>

    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>

<body class="min-h-screen">

    <div x-data="{ sidebarOpen: false }" class="min-h-screen">

        <x-layout.sidebar />

        <div class="lg:pl-72">

            <x-layout.topbar />

            <main class="px-4 py-6 sm:px-6 lg:px-8">

                @yield('content')

            </main>

        </div>

    </div>

</body>

</html>
