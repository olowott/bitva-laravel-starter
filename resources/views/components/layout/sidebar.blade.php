{{-- Mobile backdrop --}}
<div x-cloak x-show="sidebarOpen" x-transition.opacity class="fixed inset-0 z-40 bg-slate-950/40 lg:hidden"
    @click="sidebarOpen = false"></div>

<aside
    class="fixed inset-y-0 left-0 z-50 flex w-72 flex-col border-r border-slate-200 bg-white
           transition-transform duration-200 ease-in-out
           lg:translate-x-0"
    :class="sidebarOpen ? 'translate-x-0' : '-translate-x-full lg:translate-x-0'">


    {{-- Brand --}}
    <div class="flex h-16 shrink-0 items-center border-b border-slate-200 px-6">

        <a href="{{ route('dashboard') }}" class="flex items-center gap-3">
            <div
                class="flex size-9 items-center justify-center rounded-xl
                       bg-brand-600 text-sm font-bold text-white">
                B
            </div>

            <div>
                <p class="text-sm font-semibold text-slate-900">
                    {{ setting('app_name', 'BitVa') }}
                </p>

                @if (setting('app_tagline'))
                    <p class="text-xs text-slate-500">
                        {{ setting('app_tagline') }}
                    </p>
                @endif
            </div>
        </a>

        {{-- Mobile close --}}
        <button type="button"
            class="ml-auto rounded-lg p-2 text-slate-400 hover:bg-slate-100
                   hover:text-slate-600 lg:hidden"
            @click="sidebarOpen = false">
            <span class="sr-only">Close sidebar</span>

            <svg class="size-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <path d="M6 18L18 6M6 6l12 12" />
            </svg>
        </button>

    </div>


    {{-- Navigation --}}
    <nav class="flex-1 overflow-y-auto px-4 py-6">

        <div class="space-y-1">

            <x-layout.sidebar-link :href="route('dashboard')" :active="request()->routeIs('dashboard')">
                <x-heroicon-o-squares-2x2 class="size-5 shrink-0" />
                Dashboard
            </x-layout.sidebar-link>

        </div>


        <div class="mt-8 space-y-1">

            <p class="px-3 text-xs font-semibold uppercase tracking-wider text-slate-400">
                Management
            </p>

            @can('users.view')
                <x-layout.sidebar-link :href="route('admin.users.index')" :active="request()->routeIs('admin.users.*')">
                    <x-heroicon-o-users class="size-5 shrink-0" />

                    <span>Users</span>
                </x-layout.sidebar-link>
            @endcan

            @can('roles.view')
                <x-layout.sidebar-link :href="route('admin.roles.index')" :active="request()->routeIs('admin.roles.*')">
                    <x-heroicon-o-shield-check class="size-5 shrink-0" />

                    <span>Roles & Permissions</span>
                </x-layout.sidebar-link>
            @endcan

        </div>


        <div class="mt-8">

            <p class="px-3 text-xs font-semibold uppercase tracking-wider text-slate-400">
                System
            </p>

            <div class="mt-3 space-y-1">

                <x-layout.sidebar-link href="#">
                    <x-heroicon-o-clock class="size-5 shrink-0" />
                    <span>Activity</span>
                </x-layout.sidebar-link>

                @can('settings.view')
                    <x-layout.sidebar-link :href="route('admin.settings.edit')" :active="request()->routeIs('admin.settings.*')">
                        <x-heroicon-o-cog-6-tooth class="size-5 shrink-0" />

                        <span>Settings</span>
                    </x-layout.sidebar-link>
                @endcan




            </div>

        </div>

    </nav>


    {{-- Current user  + Logout --}}
    <div class="shrink-0 border-t border-slate-200 p-4">

        <div class="flex items-center gap-3">

            <div
                class="flex size-10 shrink-0 items-center justify-center
                       rounded-full bg-brand-50 text-sm font-semibold text-brand-700">
                {{ strtoupper(substr(auth()->user()->name, 0, 1)) }}
            </div>

            <div class="min-w-0 flex-1">

                <p class="truncate text-sm font-medium text-slate-900">
                    {{ auth()->user()->name }}
                </p>

                <p class="truncate text-xs text-slate-500">
                    {{ auth()->user()->email }}
                </p>

            </div>

        </div>

        <form method="POST" action="{{ route('logout') }}">
            @csrf

            <button type="submit"
                class="flex w-full items-center gap-3 rounded-xl px-3 py-2.5 text-sm font-medium text-slate-600 transition hover:bg-red-50 hover:text-red-700">
                <x-heroicon-o-arrow-left-on-rectangle class="size-5 shrink-0" />

                <span>Logout</span>
            </button>
        </form>

    </div>

</aside>
