<x-guest-layout>

    <x-auth-session-status
        class="mb-4"
        :status="session('status')"
    />

    <form
        method="POST"
        action="{{ route('login') }}"
        class="space-y-5"
    >
        @csrf

        <x-form.input
            name="email"
            label="Email Address"
            type="email"
            :value="old('email')"
            required
            autofocus
            autocomplete="username"
        />

        <x-form.input
            name="password"
            label="Password"
            type="password"
            required
            autocomplete="current-password"
        />

        <div class="flex items-center justify-between gap-4">

            <x-form.checkbox
                name="remember"
                label="Remember me"
                :checked="old('remember')"
            />

            @if (Route::has('password.request'))
                <a
                    href="{{ route('password.request') }}"
                    class="text-sm font-medium text-brand-600 hover:text-brand-700"
                >
                    Forgot password?
                </a>
            @endif

        </div>

        <x-ui.button
            type="submit"
            class="w-full"
        >
            Sign In
        </x-ui.button>

    </form>

</x-guest-layout>