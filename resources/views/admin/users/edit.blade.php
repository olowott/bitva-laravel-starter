@extends('layouts.app')

@section('title', 'Edit User')

@section('content')

    <x-layout.page-header title="Edit User" description="Update user information and access." />

    <div class="mt-6 max-w-3xl">

        <x-ui.card>

            <form method="POST" action="{{ route('admin.users.update', $user) }}" class="space-y-6">
                @csrf
                @method('PUT')

                <x-form.input name="name" label="Name" :value="$user->name" required />

                <x-form.input name="email" label="Email Address" type="email" :value="$user->email" required />

                @can('roles.manage')

                    <div>

                        <x-form.label for="role" value="Role" />

                        <select id="role" name="role"
                            class="mt-1 block w-full rounded-xl border border-slate-300 bg-white px-3 py-2.5 text-sm text-slate-900 shadow-sm focus:border-brand-500 focus:outline-none focus:ring-2 focus:ring-brand-500/20">
                            @foreach ($roles as $role)
                                <option value="{{ $role->name }}" @selected(old('role', $user->roles->first()?->name) === $role->name)>
                                    {{ str($role->name)->replace('_', ' ')->title() }}
                                </option>
                            @endforeach
                        </select>

                        <x-form.error :messages="$errors->get('role')" />

                    </div>

                @endcan

                <div class="border-t border-slate-200 pt-6">

                    <p class="mb-4 text-sm text-slate-500">
                        Leave the password fields blank to keep the current password.
                    </p>

                    <div class="space-y-6">

                        <x-form.input name="password" label="New Password" type="password" autocomplete="new-password" />

                        <x-form.input name="password_confirmation" label="Confirm New Password" type="password"
                            autocomplete="new-password" />

                    </div>

                </div>

                <x-form.checkbox name="is_active" label="Active user" :checked="old('is_active', $user->is_active)" />

                <div class="flex justify-end gap-3">

                    <a href="{{ route('admin.users.index') }}"
                        class="inline-flex items-center justify-center rounded-xl border border-slate-300 bg-white px-4 py-2.5 text-sm font-semibold text-slate-700 shadow-sm hover:bg-slate-50">
                        Cancel
                    </a>

                    <x-ui.button type="submit">
                        Save Changes
                    </x-ui.button>

                </div>

            </form>

        </x-ui.card>

    </div>

@endsection
