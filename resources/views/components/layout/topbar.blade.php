<header
    class="sticky top-0 z-30 flex h-16 items-center border-b border-slate-200 bg-white/90 px-4 backdrop-blur transition-colors
        dark:border-slate-800 dark:bg-slate-950/90
        sm:px-6 lg:px-8">
    <button type="button"
        class="mr-3 rounded-xl p-2 text-slate-500
    hover:bg-slate-100 hover:text-slate-900
    dark:text-slate-400 dark:hover:bg-slate-800 dark:hover:text-white
    lg:hidden"
        @click="sidebarOpen = true">
        <span class="sr-only">Open sidebar</span>

        <x-heroicon-o-bars-3 class="size-5" />
    </button>

    <div class="flex flex-1 items-center justify-between">

        <div>
            <p class="text-sm text-slate-500 dark:text-slate-400">
                {{ now()->format('l, j F Y') }}
            </p>
        </div>

        <div class="flex items-center gap-3">

            <div x-data="appearance" class="relative">
                <div x-data="{ open: false }" class="relative" @keydown.escape.window="open = false">
                    <button type="button" @click="open = ! open"
                        class="inline-flex size-10 items-center justify-center rounded-xl
                text-slate-500 transition
                hover:bg-slate-100 hover:text-slate-700
                dark:text-slate-400
                dark:hover:bg-slate-800 dark:hover:text-white"
                        aria-label="Change appearance" :aria-expanded="open">
                        <x-heroicon-o-computer-desktop x-cloak x-show="mode === 'system'" class="size-5" />

                        <x-heroicon-o-sun x-cloak x-show="mode === 'light'" class="size-5" />

                        <x-heroicon-o-moon x-cloak x-show="mode === 'dark'" class="size-5" />
                    </button>

                    <div x-cloak x-show="open" x-transition.origin.top.right @click.outside="open = false"
                        class="absolute right-0 z-50 mt-3 w-44 origin-top-right
                overflow-hidden rounded-2xl border border-slate-200
                bg-white p-2 shadow-xl
                dark:border-slate-800 dark:bg-slate-900">
                        <button type="button" @click="setMode('system'); open = false"
                            class="flex w-full items-center gap-3 rounded-xl px-3 py-2.5
                    text-left text-sm transition"
                            :class="mode === 'system'
                                ?
                                'bg-brand-50 text-brand-700 dark:bg-brand-950 dark:text-brand-300' :
                                'text-slate-700 hover:bg-slate-50 dark:text-slate-300 dark:hover:bg-slate-800'">
                            <x-heroicon-o-computer-desktop class="size-5" />

                            <span class="flex-1">
                                System
                            </span>

                            <x-heroicon-o-check x-cloak x-show="mode === 'system'" class="size-4" />
                        </button>

                        <button type="button" @click="setMode('light'); open = false"
                            class="flex w-full items-center gap-3 rounded-xl px-3 py-2.5
                    text-left text-sm transition"
                            :class="mode === 'light'
                                ?
                                'bg-brand-50 text-brand-700 dark:bg-brand-950 dark:text-brand-300' :
                                'text-slate-700 hover:bg-slate-50 dark:text-slate-300 dark:hover:bg-slate-800'">
                            <x-heroicon-o-sun class="size-5" />

                            <span class="flex-1">
                                Light
                            </span>

                            <x-heroicon-o-check x-cloak x-show="mode === 'light'" class="size-4" />
                        </button>

                        <button type="button" @click="setMode('dark'); open = false"
                            class="flex w-full items-center gap-3 rounded-xl px-3 py-2.5
                    text-left text-sm transition"
                            :class="mode === 'dark'
                                ?
                                'bg-brand-50 text-brand-700 dark:bg-brand-950 dark:text-brand-300' :
                                'text-slate-700 hover:bg-slate-50 dark:text-slate-300 dark:hover:bg-slate-800'">
                            <x-heroicon-o-moon class="size-5" />

                            <span class="flex-1">
                                Dark
                            </span>

                            <x-heroicon-o-check x-cloak x-show="mode === 'dark'" class="size-4" />
                        </button>
                    </div>
                </div>
            </div>

            <div x-data="{ open: false }" class="relative">
                <button type="button" @click="open = ! open"
                    class="relative inline-flex size-10 items-center justify-center rounded-xl
    text-slate-500 transition
    hover:bg-slate-100 hover:text-slate-700
    dark:text-slate-400
    dark:hover:bg-slate-800 dark:hover:text-white"
                    aria-label="Notifications">
                    <x-heroicon-o-bell class="size-5" />

                    @if ($unreadCount > 0)
                        <span
                            class="absolute -right-1 -top-1 flex h-5 min-w-5 items-center justify-center rounded-full bg-red-600 px-1 text-[10px] font-bold leading-none text-white shadow-sm ring-2 ring-white dark:ring-slate-950">
                            {{ $unreadCount > 99 ? '99+' : $unreadCount }}
                        </span>
                    @endif
                </button>

                <div x-cloak x-show="open" @click.outside="open = false" @keydown.escape.window="open = false"
                    x-transition
                    class="absolute right-0 z-50 mt-3 w-[calc(100vw-2rem)] max-w-96
    origin-top-right overflow-hidden rounded-2xl
    border border-slate-200 bg-white shadow-xl
    dark:border-slate-800 dark:bg-slate-900
    sm:w-96">

                    <div
                        class="flex items-center justify-between border-b border-slate-200 px-4 py-3
    dark:border-slate-800">
                        <div>
                            <p class="text-sm font-semibold text-slate-900 dark:text-white">
                                Notifications
                            </p>

                            @if ($unreadCount > 0)
                                <p class="mt-0.5 text-xs text-slate-500 dark:text-slate-400">
                                    {{ $unreadCount }}
                                    unread
                                </p>
                            @endif
                        </div>

                        <a href="{{ route('admin.notifications.index') }}"
                            class="text-xs font-medium text-brand-600 hover:text-brand-700">
                            View all
                        </a>
                    </div>

                    <div class="max-h-96 overflow-y-auto">

                        @forelse ($recentNotifications as $notification)
                            @php
                                $isUnread = is_null($notification->read_at);

                                $title = data_get($notification->data, 'title', 'Notification');

                                $message = data_get($notification->data, 'message');

                                $type = data_get($notification->data, 'type', 'info');
                            @endphp

                            <form method="POST" action="{{ route('admin.notifications.read', $notification) }}">
                                @csrf
                                @method('PATCH')

                                <button type="submit" @class([
                                    'flex w-full gap-3 border-b border-slate-100 px-4 py-3 text-left transition',
                                    'hover:bg-slate-50 dark:border-slate-800 dark:hover:bg-slate-800/70',
                                    'bg-brand-50/60 dark:bg-brand-950/30' => $isUnread,
                                ])>

                                    <div
                                        class="mt-0.5 flex size-9 shrink-0 items-center justify-center rounded-xl
                                @switch($type)
    @case('success')
        bg-green-50 text-green-600
        dark:bg-green-950/40 dark:text-green-400
        @break

    @case('warning')
        bg-amber-50 text-amber-600
        dark:bg-amber-950/40 dark:text-amber-400
        @break

    @case('danger')
        bg-red-50 text-red-600
        dark:bg-red-950/40 dark:text-red-400
        @break

    @default
        bg-brand-50 text-brand-600
        dark:bg-brand-950/40 dark:text-brand-400
@endswitch
                            ">
                                        @switch($type)
                                            @case('success')
                                                <x-heroicon-o-check-circle class="size-5" />
                                            @break

                                            @case('warning')
                                                <x-heroicon-o-exclamation-triangle class="size-5" />
                                            @break

                                            @case('danger')
                                                <x-heroicon-o-x-circle class="size-5" />
                                            @break

                                            @default
                                                <x-heroicon-o-information-circle class="size-5" />
                                        @endswitch
                                    </div>

                                    <div class="min-w-0 flex-1">

                                        <div class="flex items-start justify-between gap-2">
                                            <p
                                                class="truncate text-sm
        {{ $isUnread
            ? 'font-semibold text-slate-900 dark:text-white'
            : 'font-medium text-slate-700 dark:text-slate-300' }}">
                                                {{ $title }}
                                            </p>

                                            @if ($isUnread)
                                                <span class="mt-1.5 size-2 shrink-0 rounded-full bg-brand-600"></span>
                                            @endif
                                        </div>

                                        @if ($message)
                                            <p class="mt-1 text-[11px] text-slate-400 dark:text-slate-500">
                                                {{ $message }}
                                            </p>
                                        @endif

                                        <p class="mt-1 text-[11px] text-slate-400">
                                            {{ $notification->created_at->diffForHumans() }}
                                        </p>

                                    </div>

                                </button>
                            </form>

                            @empty

                                <div class="px-6 py-10 text-center">

                                    <x-heroicon-o-bell class="mx-auto size-7 text-slate-300 dark:text-slate-600" />

                                    <p class="mt-3 text-sm font-medium text-slate-900 dark:text-white">
                                        No notifications
                                    </p>

                                    <p class="mt-1 text-xs text-slate-500 dark:text-slate-400">
                                        You're all caught up.
                                    </p>

                                </div>
                            @endforelse

                        </div>

                        @if ($recentNotifications->isNotEmpty())
                            <div class="border-t border-slate-200 p-3 dark:border-slate-800">

                                <a href="{{ route('admin.notifications.index') }}"
                                    class="flex w-full items-center justify-center rounded-xl px-3 py-2
    text-sm font-medium text-slate-700 transition
    hover:bg-slate-100
    dark:text-slate-300 dark:hover:bg-slate-800 dark:hover:text-white">
                                    View all notifications
                                </a>

                            </div>
                        @endif

                    </div>
                </div>

                <div x-data="{ open: false }" class="relative" @keydown.escape.window="open = false">
                    {{-- Avatar trigger --}}
                    <button type="button" @click="open = !open"
                        class="flex size-9 items-center justify-center rounded-full bg-brand-600 text-sm font-semibold text-white transition hover:bg-brand-700 focus:outline-none focus:ring-2 focus:ring-brand-500/30"
                        aria-label="Open user menu" :aria-expanded="open">
                        {{ strtoupper(substr(auth()->user()->name, 0, 1)) }}
                    </button>


                    {{-- Dropdown --}}
                    <div x-cloak x-show="open" x-transition.origin.top.right @click.outside="open = false"
                        class="absolute right-0 z-50 mt-3 w-64 origin-top-right
    overflow-hidden rounded-2xl border border-slate-200
    bg-white shadow-xl
    dark:border-slate-800 dark:bg-slate-900">

                        {{-- User --}}

                        <div class="flex items-center gap-3 border-b border-slate-100 px-4 py-3
    dark:border-slate-800">

                            <x-ui.avatar :user="auth()->user()" size="md" />

                            <div class="min-w-0">
                                <p class="truncate text-sm font-medium text-slate-900 dark:text-white">
                                    {{ auth()->user()->name }}
                                </p>

                                <p class="truncate text-xs text-slate-500 dark:text-slate-400">
                                    {{ auth()->user()->email }}
                                </p>

                                @if (auth()->user()->job_title)
                                    <p class="mt-0.5 truncate text-xs text-slate-400 dark:text-slate-500">
                                        {{ auth()->user()->job_title }}
                                    </p>
                                @endif
                            </div>

                        </div>


                        {{-- Menu --}}
                        <div class="p-2">

                            <a href="{{ route('profile.edit') }}"
                                class="flex items-center gap-3 rounded-xl px-3 py-2.5
    text-sm text-slate-700 transition
    hover:bg-slate-50 hover:text-slate-900
    dark:text-slate-300
    dark:hover:bg-slate-800 dark:hover:text-white">
                                <x-heroicon-o-user class="size-5 text-slate-400 dark:text-slate-500" />

                                My Profile
                            </a>


                            @can('settings.view')
                                <a href="{{ route('admin.settings.edit') }}"
                                    class="flex items-center gap-3 rounded-xl px-3 py-2.5
    text-sm text-slate-700 transition
    hover:bg-slate-50 hover:text-slate-900
    dark:text-slate-300
    dark:hover:bg-slate-800 dark:hover:text-white">
                                    <x-heroicon-o-cog-6-tooth class="size-5 text-slate-400 dark:text-slate-500" />

                                    Settings
                                </a>
                            @endcan

                        </div>


                        {{-- Logout --}}
                        <div class="border-t border-slate-100 p-2 dark:border-slate-800">

                            <form method="POST" action="{{ route('logout') }}">
                                @csrf

                                <button type="submit"
                                    class="flex w-full items-center gap-3 rounded-xl px-3 py-2.5
    text-left text-sm text-red-600 transition
    hover:bg-red-50
    dark:text-red-400 dark:hover:bg-red-950/30">
                                    <x-heroicon-o-arrow-right-on-rectangle class="size-5" />

                                    Sign Out
                                </button>
                            </form>

                        </div>

                    </div>
                </div>

            </div>

        </div>

    </header>
