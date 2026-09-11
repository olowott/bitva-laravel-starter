@extends('layouts.app')

@section('title', 'Roles & Permissions')

@section('content')

    <x-layout.page-header title="Roles & Permissions" description="Manage access levels and permissions." />

    <div class="mt-6">
        <x-ui.card :padding="false">

            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-slate-200">

                    <thead class="bg-slate-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-semibold uppercase tracking-wide text-slate-500">
                                Role
                            </th>

                            <th class="px-6 py-3 text-left text-xs font-semibold uppercase tracking-wide text-slate-500">
                                Users
                            </th>

                            <th class="px-6 py-3 text-left text-xs font-semibold uppercase tracking-wide text-slate-500">
                                Permissions
                            </th>

                            <th class="px-6 py-3 text-right text-xs font-semibold uppercase tracking-wide text-slate-500">
                                Action
                            </th>
                        </tr>
                    </thead>

                    <tbody class="divide-y divide-slate-100 bg-white">

                        @foreach ($roles as $role)
                            <tr>

                                <td class="px-6 py-4">
                                    <div class="font-medium text-slate-900">
                                        {{ str($role->name)->replace('_', ' ')->title() }}
                                    </div>
                                </td>

                                <td class="px-6 py-4 text-sm text-slate-600">
                                    {{ $role->users_count }}
                                </td>

                                <td class="px-6 py-4">
                                    <div class="flex flex-wrap gap-2">

                                        @if ($role->name === 'super_admin')
                                            <x-ui.badge variant="info">
                                                Full Access
                                            </x-ui.badge>
                                        @elseif ($role->permissions->isEmpty())
                                            <span class="text-sm text-slate-400">
                                                No permissions
                                            </span>
                                        @else
                                            @foreach ($role->permissions as $permission)
                                                <x-ui.badge>
                                                    {{ $permission->name }}
                                                </x-ui.badge>
                                            @endforeach
                                        @endif

                                    </div>
                                </td>

                                <td class="px-6 py-4 text-right">

                                    @if ($role->name !== 'super_admin')
                                        @can('roles.manage')
                                            <a href="{{ route('admin.roles.edit', $role) }}"
                                                class="inline-flex items-center gap-2 text-sm font-medium text-brand-600 hover:text-brand-700">
                                                <x-heroicon-o-pencil-square class="size-4" />
                                                Edit
                                            </a>
                                        @endcan
                                    @else
                                        <span class="text-sm text-slate-400">
                                            Protected
                                        </span>
                                    @endif

                                </td>

                            </tr>
                        @endforeach

                    </tbody>
                </table>
            </div>

        </x-ui.card>
    </div>

@endsection
