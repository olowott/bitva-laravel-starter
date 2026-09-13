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


                        <x-form.select name="role" label="Role" required>
                            @foreach ($roles as $role)
                                <option value="{{ $role->name }}" @selected(old('role', 'user') === $role->name)>
                                    {{ str($role->name)->replace('_', ' ')->title() }}
                                </option>
                            @endforeach
                        </x-form.select>



                        <x-form.error :messages="$errors->get('role')" />
                    </div>
                @endcan

                <x-form.input name="password" label="Password" type="password" required autocomplete="new-password" />

                <x-form.input name="password_confirmation" label="Confirm Password" type="password" required
                    autocomplete="new-password" />

                <x-form.checkbox name="is_active" label="Active user" :checked="old('is_active', true)" />

                <div class="flex justify-end gap-3">

                    <x-ui.button :href="route('admin.users.index')" variant="secondary">
                        Cancel
                    </x-ui.button>

                    <x-ui.button type="submit">
                        Create User
                    </x-ui.button>

                </div>

            </form>

        </x-ui.card>

    </div>

@endsection
