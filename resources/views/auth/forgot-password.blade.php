<x-guest-layout>

    <p class="mb-6 text-sm leading-6 text-slate-500">
        Enter your email address and we will send you a password reset link.
    </p>

    <x-auth-session-status
        class="mb-4"
        :status="session('status')"
    />

    <form
        method="POST"
        action="{{ route('password.email') }}"
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
        />

        <x-ui.button
            type="submit"
            class="w-full"
        >
            Email Password Reset Link
        </x-ui.button>

    </form>

</x-guest-layout>