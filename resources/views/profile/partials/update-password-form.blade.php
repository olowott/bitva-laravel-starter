<section>

    <header>
        <h2 class="text-base font-semibold text-slate-900">
            Update Password
        </h2>

        <p class="mt-1 text-sm leading-6 text-slate-500">
            Use a strong password to help keep your account secure.
        </p>
    </header>


    <form method="POST" action="{{ route('password.update') }}" class="mt-6 space-y-5">
        @csrf
        @method('put')


        <x-form.input id="update_password_current_password" name="current_password" label="Current Password" type="password"
            autocomplete="current-password" :messages="$errors->updatePassword->get('current_password')" />


        <x-form.input id="update_password_password" name="password" label="New Password" type="password"
            autocomplete="new-password" :messages="$errors->updatePassword->get('password')" />


        <x-form.input id="update_password_password_confirmation" name="password_confirmation"
            label="Confirm New Password" type="password" autocomplete="new-password" :messages="$errors->updatePassword->get('password_confirmation')" />


        <div class="flex items-center gap-4">

            <x-ui.button type="submit">
                Update Password
            </x-ui.button>


            @if (session('status') === 'password-updated')
                <p x-data="{ show: true }" x-show="show" x-transition x-init="setTimeout(() => show = false, 2500)"
                    class="text-sm font-medium text-emerald-600">
                    Password updated.
                </p>
            @endif

        </div>

    </form>

</section>
