<x-guest-layout>

    <form
        method="POST"
        action="{{ route('password.store') }}"
        class="space-y-5"
    >
        @csrf

        <input
            type="hidden"
            name="token"
            value="{{ $request->route('token') }}"
        >

        <x-form.input
            name="email"
            label="Email Address"
            type="email"
            :value="old('email', $request->email)"
            required
            autofocus
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

        <x-ui.button
            type="submit"
            class="w-full"
        >
            Reset Password
        </x-ui.button>

    </form>

</x-guest-layout>