{{-- Mobile backdrop --}}
<div x-cloak x-show="sidebarOpen" x-transition.opacity class="fixed inset-0 z-40 bg-slate-950/40 lg:hidden"
    @click="sidebarOpen = false"></div>

{{-- Sidebar --}}
<aside
    class="fixed inset-y-0 left-0 z-50 flex w-72 -translate-x-full flex-col border-r border-slate-200 bg-white transition-transform duration-200 lg:translate-x-0"
    :class="{ 'translate-x-0': sidebarOpen, '-translate-x-full': !sidebarOpen }">

    {{-- Brand --}}
    <div class="flex h-16 items-center border-b border-slate-200 px-6">
        <a href="{{ route('dashboard') }}" class="flex items-center gap-3">
            <div class="flex size-9 items-center justify-center rounded-xl bg-brand-600 font-bold text-white">
                B
            </div>

            <div>
                <div class="text-sm font-semibold text-slate-900">
                    BitVa
                </div>

                <div class="text-xs text-slate-500">
                    Application Suite
                </div>
            </div>
        </a>
    </div>

    {{-- Navigation --}}
    <nav class="flex-1 overflow-y-auto px-4 py-6">

        <div class="space-y-1">

            <x-layout.sidebar-link :href="route('dashboard')" :active="request()->routeIs('dashboard')">
                Dashboard
            </x-layout.sidebar-link>

        </div>

        <div class="mt-8">

            <p class="px-3 text-xs font-semibold uppercase tracking-wider text-slate-400">
                Management
            </p>

            <div class="mt-3 space-y-1">

                <x-layout.sidebar-link href="#">
                    Users
                </x-layout.sidebar-link>

            </div>

        </div>

        <div class="mt-8">

            <p class="px-3 text-xs font-semibold uppercase tracking-wider text-slate-400">
                System
            </p>

            <div class="mt-3 space-y-1">

                <x-layout.sidebar-link href="#">
                    Activity
                </x-layout.sidebar-link>

                <x-layout.sidebar-link href="#">
                    Settings
                </x-layout.sidebar-link>

            </div>

        </div>

    </nav>

    {{-- User --}}
    <div class="border-t border-slate-200 p-4">

        <div class="flex items-center gap-3">

            <div
                class="flex size-10 items-center justify-center rounded-full bg-slate-100 text-sm font-semibold text-slate-700">
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

    </div>

</aside>
