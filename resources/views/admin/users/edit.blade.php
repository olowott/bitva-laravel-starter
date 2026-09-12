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

            @can('documents.view')

                <x-ui.card class="mt-6">

                    <div class="mb-5">

                        <h2 class="text-lg font-semibold text-slate-900">
                            Documents
                        </h2>

                        <p class="mt-1 text-sm text-slate-500">
                            Private files attached to this user.
                        </p>

                    </div>


                    @can('documents.upload')
                        <form method="POST" action="{{ route('admin.documents.store') }}" enctype="multipart/form-data"
                            class="mb-6 grid gap-4 md:grid-cols-3">
                            @csrf

                            <input type="hidden" name="user_id" value="{{ $user->id }}">

                            <div class="md:col-span-2">

                                <x-form.file name="document" label="Document" accept=".pdf,.jpg,.jpeg,.png,.doc,.docx" required />

                            </div>

                            <div>

                                <x-form.input name="category" label="Category" placeholder="e.g. Identity" />

                            </div>

                            <div class="md:col-span-3">

                                <x-ui.button type="submit">
                                    <x-heroicon-o-arrow-up-tray class="mr-2 size-4" />
                                    Upload Document
                                </x-ui.button>

                            </div>

                        </form>
                    @endcan


                    <x-document.list :documents="$user->documents" />

                </x-ui.card>

            @endcan

        </x-ui.card>

    </div>

@endsection
