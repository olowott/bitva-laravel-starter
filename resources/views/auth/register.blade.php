<x-guest-layout>

    <form
        method="POST"
        action="{{ route('register') }}"
        class="space-y-5"
    >
        @csrf

        <x-form.input
            name="name"
            label="Name"
            :value="old('name')"
            required
            autofocus
            autocomplete="name"
        />

        <x-form.input
            name="email"
            label="Email Address"
            type="email"
            :value="old('email')"
            required
            autocomplete="username"
        />

        <x-form.input
            name="password"
            label="Password"
            type="password"
            required
            autocomplete="new-password"
        />

        <x-form.input
            name="password_confirmation"
            label="Confirm Password"
            type="password"
            required
            autocomplete="new-password"
        />

        <div class="flex items-center justify-between gap-4">

            <a
                href="{{ route('login') }}"
                class="text-sm font-medium text-brand-600 hover:text-brand-700"
            >
                Already registered?
            </a>

            <x-ui.button type="submit">
                Register
            </x-ui.button>

        </div>

    </form>

</x-guest-layout>