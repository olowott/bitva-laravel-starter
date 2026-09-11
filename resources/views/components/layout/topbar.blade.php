<header
    class="sticky top-0 z-30 flex h-16 items-center border-b border-slate-200 bg-white-90 px-4 backdrop-blur sm:px-6 lg:px-8">

    <button type="button"
        class="mr-3 rounded-xl p-2 text-slate-500
           hover:bg-slate-100 hover:text-slate-900 lg:hidden"
        @click="sidebarOpen = true">
        <span class="sr-only">Open sidebar</span>

        <svg class="size-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
            <path d="M4 6h16M4 12h16M4 18h16" />
        </svg>
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

                <svg class="size-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <path d="M18 8a6 6 0 00-12 0c0 7-3 7-3 9h18c0-2-3-2-3-9" />
                    <path d="M13.73 21a2 2 0 01-3.46 0" />
                </svg>
            </button>

            <a href="{{ route('profile.edit') }}"
                class="flex size-9 items-center justify-center rounded-full bg-brand-600 text-sm font-semibold text-white">
                {{ strtoupper(substr(auth()->user()->name, 0, 1)) }}
            </a>

        </div>

    </div>

</header>
