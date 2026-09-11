<x-guest-layout>

    <p class="mb-6 text-sm leading-6 text-slate-500">
        Confirm your password before continuing.
    </p>

    <form
        method="POST"
        action="{{ route('password.confirm') }}"
        class="space-y-5"
    >
        @csrf

        <x-form.input
            name="password"
            label="Password"
            type="password"
            required
            autocomplete="current-password"
        />

        <x-ui.button
            type="submit"
            class="w-full"
        >
            Confirm
        </x-ui.button>

    </form>

</x-guest-layout>