<header
    class="sticky top-0 z-30 flex h-16 items-center border-b border-slate-200 bg-white-90 px-4 backdrop-blur sm:px-6 lg:px-8">

    <button type="button"
        class="mr-3 rounded-xl p-2 text-slate-500
           hover:bg-slate-100 hover:text-slate-900 lg:hidden"
        @click="sidebarOpen = true">
        <span class="sr-only">Open sidebar</span>

        <x-heroicon-o-bars-3 class="size-5" />
    </button>

    <div class="flex flex-1 items-center justify-between">

        <div>
            <p class="text-sm text-slate-500">
                {{ now()->format('l, j F Y') }}
            </p>
        </div>

        <div class="flex items-center gap-3">



            <div x-data="{ open: false }" class="relative">
                <button type="button" @click="open = ! open"
                    class="relative inline-flex size-10 items-center justify-center rounded-xl text-slate-500 transition hover:bg-slate-100 hover:text-slate-700"
                    aria-label="Notifications">
                    <x-heroicon-o-bell class="size-5" />

                    @if ($unreadCount > 0)
                        <span
                            class="absolute -right-1 -top-1 flex h-5 min-w-5 items-center justify-center rounded-full bg-red-600 px-1 text-[10px] font-bold leading-none text-white shadow-sm ring-2 ring-white">
                            {{ $unreadCount > 99 ? '99+' : $unreadCount }}
                        </span>
                    @endif
                </button>

                <div x-cloak x-show="open" @click.outside="open = false" @keydown.escape.window="open = false"
                    x-transition
                    class="absolute right-0 z-50 mt-3 w-[calc(100vw-2rem)] max-w-96 origin-top-right overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-xl sm:w-96">

                    <div class="flex items-center justify-between border-b border-slate-200 px-4 py-3">
                        <div>
                            <p class="text-sm font-semibold text-slate-900">
                                Notifications
                            </p>

                            @if ($unreadCount > 0)
                                <p class="mt-0.5 text-xs text-slate-500">
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
                                    'flex w-full gap-3 border-b border-slate-100 px-4 py-3 text-left transition hover:bg-slate-50',
                                    'bg-brand-50/60' => $isUnread,
                                ])>

                                    <div
                                        class="mt-0.5 flex size-9 shrink-0 items-center justify-center rounded-xl
                                @switch($type)
                                    @case('success')
                                        bg-green-50 text-green-600
                                        @break

                                    @case('warning')
                                        bg-amber-50 text-amber-600
                                        @break

                                    @case('danger')
                                        bg-red-50 text-red-600
                                        @break

                                    @default
                                        bg-brand-50 text-brand-600
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
                                        {{ $isUnread ? 'font-semibold text-slate-900' : 'font-medium text-slate-700' }}">
                                                {{ $title }}
                                            </p>

                                            @if ($isUnread)
                                                <span class="mt-1.5 size-2 shrink-0 rounded-full bg-brand-600"></span>
                                            @endif
                                        </div>

                                        @if ($message)
                                            <p class="mt-1 line-clamp-2 text-xs leading-5 text-slate-500">
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

                                    <x-heroicon-o-bell class="mx-auto size-7 text-slate-300" />

                                    <p class="mt-3 text-sm font-medium text-slate-900">
                                        No notifications
                                    </p>

                                    <p class="mt-1 text-xs text-slate-500">
                                        You're all caught up.
                                    </p>

                                </div>
                            @endforelse

                        </div>

                        @if ($recentNotifications->isNotEmpty())
                            <div class="border-t border-slate-200 p-3">

                                <a href="{{ route('admin.notifications.index') }}"
                                    class="flex w-full items-center justify-center rounded-xl px-3 py-2 text-sm font-medium text-slate-700 transition hover:bg-slate-100">
                                    View all notifications
                                </a>

                            </div>
                        @endif

                    </div>
                </div>

                <a href="{{ route('profile.edit') }}"
                    class="flex size-9 items-center justify-center rounded-full bg-brand-600 text-sm font-semibold text-white">
                    {{ strtoupper(substr(auth()->user()->name, 0, 1)) }}
                </a>

            </div>

        </div>

    </header>
