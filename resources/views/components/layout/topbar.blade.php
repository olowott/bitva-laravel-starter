<header
    class="sticky top-0 z-30 flex h-16 items-center border-b border-slate-200 bg-white-90 px-4 backdrop-blur sm:px-6 lg:px-8">

    <button type="button"
        class="mr-3 rounded-xl p-2 text-slate-500
           hover:bg-slate-100 hover:text-slate-900 lg:hidden"
        @click="sidebarOpen = true">
        <span class="sr-only">Open sidebar</span>

        <x-heroicon-o-bars-3 class="size-5" />
    </button>

    <div class="flex flex-1 items-center justify-between">

        <div>
            <p class="text-sm text-slate-500">
                {{ now()->format('l, j F Y') }}
            </p>
        </div>

        <div class="flex items-center gap-3">

            <button type="button" class="rounded-xl p-2 text-slate-500 hover:bg-slate-100 hover:text-slate-900">
                <span class="sr-only">Notifications</span>

                <x-heroicon-o-bell class="size-5" />
            </button>

            <a href="{{ route('profile.edit') }}"
                class="flex size-9 items-center justify-center rounded-full bg-brand-600 text-sm font-semibold text-white">
                {{ strtoupper(substr(auth()->user()->name, 0, 1)) }}
            </a>

        </div>

    </div>

</header>
