@extends('layouts.app')

@section('title', 'Add User')

@section('content')

    <x-layout.page-header title="Add User" description="Create a new user account." />

    <div class="mt-6 max-w-3xl">

        <x-ui.card>

            <form method="POST" action="{{ route('admin.users.store') }}" class="space-y-6">
                @csrf

                <x-form.input name="name" label="Name" required autofocus />

                <x-form.input name="email" label="Email Address" type="email" required />

                @can('roles.manage')

                    <div>
                        <x-form.label for="role" value="Role" />

                        <select id="role" name="role"
                            class="mt-1 block w-full rounded-xl border border-slate-300 bg-white px-3 py-2.5 text-sm text-slate-900 shadow-sm focus:border-brand-500 focus:outline-none focus:ring-2 focus:ring-brand-500/20">
                            @foreach ($roles as $role)
                                <option value="{{ $role->name }}" @selected(old('role', 'user') === $role->name)>
                                    {{ str($role->name)->replace('_', ' ')->title() }}
                                </option>
                            @endforeach
                        </select>

                        <x-form.error :messages="$errors->get('role')" />
                    </div>

                @endcan

                <x-form.input name="password" label="Password" type="password" required autocomplete="new-password" />

                <x-form.input name="password_confirmation" label="Confirm Password" type="password" required
                    autocomplete="new-password" />

                <x-form.checkbox name="is_active" label="Active user" :checked="old('is_active', true)" />

                <div class="flex justify-end gap-3">

                    <a href="{{ route('admin.users.index') }}"
                        class="inline-flex items-center justify-center rounded-xl border border-slate-300 bg-white px-4 py-2.5 text-sm font-semibold text-slate-700 shadow-sm hover:bg-slate-50">
                        Cancel
                    </a>

                    <x-ui.button type="submit">
                        Create User
                    </x-ui.button>

                </div>

            </form>

        </x-ui.card>

    </div>

@endsection
