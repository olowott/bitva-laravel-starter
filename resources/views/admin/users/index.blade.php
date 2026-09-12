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

<x-ui.active-filters
    class="mt-3"
    :filters="[
        'Search' => request('search'),
        'Role' => request('role')
            ? str(request('role'))->replace('_', ' ')->title()
            : null,
        'Status' => request('status')
            ? str(request('status'))->title()
            : null,
    ]"
    :clear-url="route('admin.users.index')"
/>

 <div class="mt-6">

    <x-table.index
        :empty="$users->isEmpty()"
        empty-title="No users found"
        empty-description="Try changing your search or filters."
    >

        <table class="min-w-full">

            <thead>
                <x-table.header>

                    <x-table.head>
                        <x-table.sortable
        column="name"
        label="User"
    />
                    </x-table.head>

                    <x-table.head>
                        Role
                    </x-table.head>

                    <x-table.head>
                        Status
                    </x-table.head>

                    <x-table.head>
                         <x-table.sortable
        column="created_at"
        label="Created"
    />
                    </x-table.head>

                    <x-table.head align="right">
                        Actions
                    </x-table.head>

                </x-table.header>
            </thead>

            <tbody>

                @foreach ($users as $user)

                    <x-table.row>

                        <x-table.cell>

                            <div class="font-medium text-slate-900">
                                {{ $user->name }}
                            </div>

                            <div class="mt-1 text-slate-500">
                                {{ $user->email }}
                            </div>

                        </x-table.cell>


                        <x-table.cell>

                            <div class="flex flex-wrap gap-2">

                                @forelse ($user->roles as $role)

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

                            </div>

                        </x-table.cell>


                        <x-table.cell>

                            <x-ui.badge
                                :variant="$user->is_active ? 'success' : 'danger'"
                            >
                                {{ $user->is_active ? 'Active' : 'Inactive' }}
                            </x-ui.badge>

                        </x-table.cell>


                        <x-table.cell class="whitespace-nowrap text-slate-500">

                            {{ $user->created_at->format('d M Y') }}

                        </x-table.cell>


                        <x-table.cell align="right">

                            <div class="flex items-center justify-end gap-3">

                                @can('users.update')

                                    <a
                                        href="{{ route('admin.users.edit', $user) }}"
                                        class="text-sm font-medium text-brand-600 transition hover:text-brand-700"
                                    >
                                        Edit
                                    </a>

                                @endcan


                                @can('users.delete')

                                    @if (! auth()->user()->is($user))

                                        <form
                                            method="POST"
                                            action="{{ route('admin.users.destroy', $user) }}"
                                            onsubmit="return confirm('Delete this user?')"
                                        >
                                            @csrf
                                            @method('DELETE')

                                            <button
                                                type="submit"
                                                class="text-sm font-medium text-red-600 transition hover:text-red-700"
                                            >
                                                Delete
                                            </button>

                                        </form>

                                    @endif

                                @endcan

                            </div>

                        </x-table.cell>

                    </x-table.row>

                @endforeach

            </tbody>

        </table>

            <x-slot:footer>
        <x-table.footer :paginator="$users" />
    </x-slot:footer>

    </x-table.index>


</div>

@endsection
