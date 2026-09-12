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
    </x-layout.page-header>


    <div class="space-y-3 mt-6">

        @forelse ($notifications as $notification)
            @php
                $isUnread = is_null($notification->read_at);

                $title = data_get($notification->data, 'title', 'Notification');

                $message = data_get($notification->data, 'message');
            @endphp

            <div @class([
                'rounded-2xl border border-slate-200 p-5 shadow-sm transition',
                'bg-brand-50/60' => $isUnread,
                'bg-white' => !$isUnread,
            ])>

                <div class="flex flex-col gap-4 sm:flex-row sm:items-start sm:justify-between">

                    <div>

                        <div class="flex items-center gap-2">

                            <h3 class="text-sm font-semibold text-slate-900">
                                {{ $title }}
                            </h3>

                            @if ($isUnread)
                                <span class="size-2 rounded-full bg-brand-600"></span>
                            @endif

                        </div>

                        @if ($message)
                            <p class="mt-1 text-sm text-slate-600">
                                {{ $message }}
                            </p>
                        @endif

                        <p class="mt-2 text-xs text-slate-400">
                            {{ $notification->created_at->diffForHumans() }}
                        </p>

                    </div>


                    @if ($isUnread)
                        <form method="POST" action="{{ route('admin.notifications.read', $notification) }}">
                            @csrf
                            @method('PATCH')

                            <button type="submit" class="text-sm font-medium text-brand-600 hover:text-brand-700">
                                Mark as read
                            </button>
                        </form>
                    @endif

                </div>

            </div>

        @empty

            <x-ui.card>

                <div class="py-10 text-center">

                    <x-heroicon-o-bell class="mx-auto size-8 text-slate-300" />

                    <p class="mt-3 text-sm font-medium text-slate-900">
                        No notifications
                    </p>

                    <p class="mt-1 text-sm text-slate-500">
                        Your notifications will appear here.
                    </p>

                </div>

            </x-ui.card>
        @endforelse

    </div>


    @if ($notifications->hasPages())
        <div class="mt-6">
            {{ $notifications->links() }}
        </div>
    @endif

@endsection
