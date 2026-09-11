<x-guest-layout>

    <p class="mb-6 text-sm leading-6 text-slate-500">
        Thanks for signing up. Please verify your email address by clicking
        the link we sent to you.
    </p>

    @if (session('status') === 'verification-link-sent')
        <div class="mb-4 rounded-xl border border-emerald-200 bg-emerald-50 p-4">
            <p class="text-sm font-medium text-emerald-700">
                A new verification link has been sent to your email address.
            </p>
        </div>
    @endif

    <div class="flex items-center justify-between gap-4">

        <form method="POST" action="{{ route('verification.send') }}">
            @csrf

            <x-ui.button type="submit">
                Resend Verification Email
            </x-ui.button>
        </form>

        <form method="POST" action="{{ route('logout') }}">
            @csrf

            <button type="submit" class="text-sm font-medium text-slate-600 hover:text-slate-900">
                Log Out
            </button>
        </form>

    </div>

</x-guest-layout>
