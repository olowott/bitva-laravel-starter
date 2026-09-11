@extends('layouts.app')

@section('title', 'Users')

@section('content')

    <x-layout.page-header
        title="Users"
        description="Manage people with access to this application."
    >
        <x-slot:actions>
            @can('users.create')
                <a href="{{ route('admin.users.create') }}">
                    <x-ui.button>
                        Add User
                    </x-ui.button>
                </a>
            @endcan
        </x-slot:actions>
    </x-layout.page-header>

    @if (session('success'))
        <div class="mt-6 rounded-xl border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm text-emerald-700">
            {{ session('success') }}
        </div>
    @endif

    @if (session('error'))
        <div class="mt-6 rounded-xl border border-red-200 bg-red-50 px-4 py-3 text-sm text-red-700">
            {{ session('error') }}
        </div>
    @endif

    <x-ui.filter-panel :action="route('admin.users.index')" class="mt-6">

    <div class="md:col-span-2">
        <x-form.input
            name="search"
            label="Search"
            placeholder="Search by name or email..."
            :value="request('search')"
        />
    </div>

    <div>
        <x-form.select
            name="role"
            label="Role"
        >
            <option value="">All roles</option>

            @foreach ($roles as $role)
                <option
                    value="{{ $role->name }}"
                    @selected(request('role') === $role->name)
                >
                    {{ str($role->name)->replace('_', ' ')->title() }}
                </option>
            @endforeach
        </x-form.select>
    </div>

    <div>
        <x-form.select
            name="status"
            label="Status"
        >
            <option value="">All statuses</option>

            <option
                value="active"
                @selected(request('status') === 'active')
            >
                Active
            </option>

            <option
                value="inactive"
                @selected(request('status') === 'inactive')
            >
                Inactive
            </option>
        </x-form.select>
    </div>

    <div class="flex items-end gap-3 md:col-span-4">
        <x-ui.button type="submit">
            <x-heroicon-o-funnel class="mr-2 size-4" />
            Apply Filters
        </x-ui.button>

        @if (
            request()->filled('search')
            || request()->filled('role')
            || request()->filled('status')
        )
            <a
                href="{{ route('admin.users.index') }}"
                class="inline-flex items-center justify-center rounded-xl border border-slate-300 bg-white px-4 py-2.5 text-sm font-semibold text-slate-700 shadow-sm transition hover:bg-slate-50"
            >
                <x-heroicon-o-x-mark class="mr-2 size-4" />
                Clear
            </a>
        @endif
    </div>

</x-ui.filter-panel>

    <x-ui.card class="mt-6">

    
        <div class="overflow-x-auto">
@if ($users->count())
            <table class="min-w-full divide-y divide-slate-200">

                <thead>
                    <tr class="text-left text-xs font-semibold uppercase tracking-wide text-slate-500">
                        <th class="px-4 py-3">                            User                        </th>
                        <th class="px-4 py-3">                            Role                        </th>
                        <th class="px-4 py-3">                            Status                        </th>
                        <th class="px-4 py-3">                            Created                        </th>
                        <th class="px-4 py-3 text-right">                            Actions                        </th>
                    </tr>
                </thead>

                <tbody class="divide-y divide-slate-100">

                    @forelse ($users as $user)

                        <tr class="text-sm">

                            <td class="px-4 py-4">
                                <div class="font-medium text-slate-900">
                                    {{ $user->name }}
                                </div>

                                <div class="mt-1 text-slate-500">
                                    {{ $user->email }}
                                </div>
                            </td>

                            <td class="px-4 py-4">

                                @forelse($user->roles as $role)

                                    <x-ui.badge
                                        :variant="$role->name === 'super_admin'
                                            ? 'info'
                                            : ($role->name === 'admin'
                                                ? 'success'
                                                : 'neutral')"
                                    >
                                        {{ str($role->name)->replace('_', ' ')->title() }}
                                    </x-ui.badge>

                                @empty

                                    <x-ui.badge>
                                        No Role
                                    </x-ui.badge>

                                @endforelse

                            </td>

                            <td class="px-4 py-4">
    <x-ui.badge :variant="$user->is_active ? 'success' : 'danger'">
        {{ $user->is_active ? 'Active' : 'Inactive' }}
    </x-ui.badge>
</td>

                            <td class="px-4 py-4 text-slate-500">
                                {{ $user->created_at->format('d M Y') }}
                            </td>

                            <td class="px-4 py-4">

                                <div class="flex items-center justify-end gap-3">

                                    @can('users.update')

                                        <a
                                            href="{{ route('admin.users.edit', $user) }}"
                                            class="text-sm font-medium text-brand-600 hover:text-brand-700"
                                        >
                                            Edit
                                        </a>

                                    @endcan

                                    @can('users.delete')

                                        @if(! auth()->user()->is($user))

                                            <form
                                                method="POST"
                                                action="{{ route('admin.users.destroy', $user) }}"
                                                onsubmit="return confirm('Delete this user?')"
                                            >
                                                @csrf
                                                @method('DELETE')

                                                <button
                                                    type="submit"
                                                    class="text-sm font-medium text-red-600 hover:text-red-700"
                                                >
                                                    Delete
                                                </button>

                                            </form>

                                        @endif

                                    @endcan

                                </div>

                            </td>

                        </tr>

                    @empty

                        <tr>
                            <td
                                colspan="4"
                                class="px-4 py-12 text-center text-sm text-slate-500"
                            >
                                No users found.
                            </td>
                        </tr>

                    @endforelse

                </tbody>

            </table>
@else
    <div class="py-12 text-center">
        <x-heroicon-o-users class="mx-auto size-10 text-slate-300" />

        <h3 class="mt-3 text-sm font-semibold text-slate-900">
            No users found
        </h3>

        <p class="mt-1 text-sm text-slate-500">
            Try changing your search or filters.
        </p>
    </div>
@endif
        </div>

        @if($users->hasPages())
            <div class="mt-6 border-t border-slate-200 pt-5">
                {{ $users->links() }}
            </div>
        @endif

    </x-ui.card>

@endsection