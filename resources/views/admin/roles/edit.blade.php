@extends('layouts.app')

@section('title', 'Edit Role')

@section('content')

    <x-layout.page-header title="Edit Role" description="Manage permissions assigned to this role." />

    <div class="mt-6 max-w-4xl">

        <form method="POST" action="{{ route('admin.roles.update', $role) }}">
            @csrf
            @method('PUT')

            <x-ui.card>

                <div>
                    <h2 class="text-lg font-semibold text-slate-900">
                        {{ str($role->name)->replace('_', ' ')->title() }}
                    </h2>

                    <p class="mt-1 text-sm text-slate-500">
                        Select the permissions this role should have.
                    </p>
                </div>

                <div class="mt-6 space-y-6">

                    @foreach ($permissions as $group => $groupPermissions)
                        <div>

                            <h3 class="mb-3 text-sm font-semibold uppercase tracking-wide text-slate-500">
                                {{ str($group)->title() }}
                            </h3>

                            <div class="grid gap-3 sm:grid-cols-2 lg:grid-cols-3">

                                @foreach ($groupPermissions as $permission)
                                    <label
                                        class="flex cursor-pointer items-center gap-3 rounded-xl border border-slate-200 p-4 transition hover:bg-slate-50">

                                        <input type="checkbox" name="permissions[]" value="{{ $permission->name }}"
                                            @checked(in_array($permission->name, old('permissions', $role->permissions->pluck('name')->all())))
                                            class="rounded border-slate-300 text-brand-600 focus:ring-brand-500">

                                        <span class="text-sm font-medium text-slate-700">
                                            {{ str($permission->name)->after('.')->replace('_', ' ')->title() }}
                                        </span>

                                    </label>
                                @endforeach

                            </div>

                        </div>
                    @endforeach

                </div>

                <div class="mt-8 flex gap-3">

                    <x-ui.button type="submit">
                        <x-heroicon-o-check class="mr-2 size-4" />
                        Save Permissions
                    </x-ui.button>

                    <a href="{{ route('admin.roles.index') }}"
                        class="inline-flex items-center rounded-xl border border-slate-300 bg-white px-4 py-2.5 text-sm font-semibold text-slate-700 shadow-sm hover:bg-slate-50">
                        Cancel
                    </a>

                </div>

            </x-ui.card>

        </form>

    </div>

@endsection
