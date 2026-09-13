@extends('layouts.app')

@section('title', 'Notifications')

@section('content')

    <x-layout.page-header title="Notifications" description="View your recent account and system notifications.">
        @if (auth()->user()->unreadNotifications->isNotEmpty())
            <form method="POST" action="{{ route('admin.notifications.read-all') }}">
                @csrf
                @method('PATCH')

                <x-ui.button type="submit" variant="secondary">
                    Mark all as read
                </x-ui.button>
            </form>
        @endif

        @can('notifications.send')
            <x-ui.button href="{{ route('admin.notifications.create') }}">
                Send Notification
            </x-ui.button>
        @endcan
    </x-layout.page-header>


    <div class="space-y-3 mt-6">

        @forelse ($notifications as $notification)
            @php
                $isUnread = is_null($notification->read_at);

                $title = data_get($notification->data, 'title', 'Notification');

                $message = data_get($notification->data, 'message');
            @endphp

            <div @class([
                'rounded-2xl border p-5 shadow-sm transition',
                'border-brand-200 bg-brand-50/60 dark:border-brand-900/60 dark:bg-brand-950/30' => $isUnread,
                'border-slate-200 bg-white dark:border-slate-800 dark:bg-slate-900' => !$isUnread,
            ])>

                <div class="flex flex-col gap-4 sm:flex-row sm:items-start sm:justify-between">

                    <div>

                        <div class="flex items-center gap-2">

                            <h3 class="text-sm font-semibold text-slate-900 dark:text-white">
                                {{ $title }}
                            </h3>

                            @if ($isUnread)
                                <span class="size-2 rounded-full bg-brand-600"></span>
                            @endif

                        </div>

                        @if ($message)
                            <p class="mt-1 text-sm text-slate-600 dark:text-slate-300">
                                {{ $message }}
                            </p>
                        @endif

                        <p class="mt-2 text-xs text-slate-400 dark:text-slate-500">
                            {{ $notification->created_at->diffForHumans() }}
                        </p>

                    </div>


                    @if ($isUnread)
                        <form method="POST" action="{{ route('admin.notifications.read', $notification) }}">
                            @csrf
                            @method('PATCH')

                            <button type="submit"
                                class="text-sm font-medium text-brand-600 hover:text-brand-700
    dark:text-brand-400 dark:hover:text-brand-300">
                                Mark as read
                            </button>
                        </form>
                    @endif

                </div>

            </div>

        @empty

            <x-ui.empty-state title="No notifications" description="Your notifications will appear here." icon="bell" />
        @endforelse

    </div>

    @if ($notifications->hasPages())
        <div class="mt-6">
            {{ $notifications->links() }}
        </div>
    @endif

@endsection
