@extends('layouts.app')

@section('title', 'Send Notification')

@section('content')
    <x-layout.page-header title="Send Notification"
        description="Send an in-app notification to a user and optionally deliver it by email." />

    <div class="mt-6 max-w-3xl">
        <x-ui.card>
            <form method="POST" action="{{ route('admin.notifications.send') }}" class="space-y-6 p-6">
                @csrf

                {{-- Recipient --}}
                <div>
                    <x-form.select name="user_id" label="Recipient" required>
                        <option value="">
                            Select a user
                        </option>

                        @foreach ($users as $user)
                            <option value="{{ $user->id }}" @selected(old('user_id') == $user->id)>
                                {{ $user->name }} — {{ $user->email }}
                            </option>
                        @endforeach
                    </x-form.select>

                    <x-form.error name="user_id" />
                </div>

                {{-- Title --}}
                <div>
                    <x-form.input name="title" label="Title" type="text" :value="old('title')" required maxlength="255"
                        placeholder="e.g. Account Updated" />
                </div>

                {{-- Message --}}
                <div>
                    <x-form.textarea name="message" label="Message" rows="6" required maxlength="2000"
                        placeholder="Write the notification message..." :value="old('message')" />

                    <div class="mt-1 flex justify-end">
                        <span class="text-xs text-slate-400 dark:text-slate-500">
                            Maximum 2,000 characters
                        </span>
                    </div>
                </div>

                {{-- Type --}}
                <x-form.select name="type" label="Notification Type" required>
                    @foreach ([
            'info' => 'Information',
            'success' => 'Success',
            'warning' => 'Warning',
            'danger' => 'Important / Danger',
        ] as $value => $label)
                        <option value="{{ $value }}" @selected(old('type', 'info') === $value)>
                            {{ $label }}
                        </option>
                    @endforeach
                </x-form.select>

                {{-- Optional URL --}}
                <div>
                    <x-form.label for="url" value="Internal Link" />

                    <x-form.input id="url" name="url" type="text" :value="old('url')" maxlength="2048"
                        placeholder="/profile" />
                    <p class="mt-1 text-xs text-slate-500 dark:text-slate-400">
                        Optional. Use an internal path such as
                        <code>/profile</code> or
                        <code>/admin/users/...</code>.
                    </p>

                    <x-form.error name="url" />
                </div>

                {{-- Email --}}
                <div
                    class="rounded-xl border border-slate-200 bg-slate-50 p-4
    dark:border-slate-800 dark:bg-slate-950/60">
                    <label for="send_email" class="flex cursor-pointer items-start gap-3">
                        <input id="send_email" name="send_email" type="checkbox" value="1" @checked(old('send_email'))
                            class="mt-1 rounded border-slate-300 text-brand-600 focus:ring-brand-500
    dark:border-slate-700 dark:bg-slate-900
    dark:checked:border-brand-600 dark:checked:bg-brand-600">

                        <span>
                            <span class="block text-sm font-medium text-slate-900 dark:text-white">
                                Also send by email
                            </span>

                            <span class="mt-1 block text-sm text-slate-500 dark:text-slate-400">
                                The user will receive the same message by email
                                in addition to their in-app notification.
                            </span>
                        </span>
                    </label>

                    <x-form.error name="send_email" />
                </div>

                {{-- Actions --}}
                <div class="flex items-center justify-end gap-3 border-t
    border-slate-100 pt-6 dark:border-slate-800">
                    <x-ui.button href="{{ route('admin.notifications.index') }}" variant="secondary">
                        Cancel
                    </x-ui.button>

                    <x-ui.button type="submit">
                        Send Notification
                    </x-ui.button>
                </div>
            </form>
        </x-ui.card>
    </div>
@endsection
