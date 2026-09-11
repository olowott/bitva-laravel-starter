<section>

    <header>
        <h2 class="text-base font-semibold text-slate-900">
            Profile Information
        </h2>

        <p class="mt-1 text-sm leading-6 text-slate-500">
            Update your account's profile information and email address.
        </p>
    </header>


    <form id="send-verification" method="POST" action="{{ route('verification.send') }}">
        @csrf
    </form>


    <form method="POST" action="{{ route('profile.update') }}" class="mt-6 space-y-5">
        @csrf
        @method('patch')


        <x-form.input name="name" label="Name" :value="$user->name" required autofocus autocomplete="name" />


        <div>

            <x-form.input name="email" label="Email Address" type="email" :value="$user->email" required
                autocomplete="username" />


            @if ($user instanceof \Illuminate\Contracts\Auth\MustVerifyEmail && !$user->hasVerifiedEmail())

                <div class="mt-3 rounded-xl border border-amber-200 bg-amber-50 p-4">

                    <p class="text-sm text-amber-800">
                        Your email address has not been verified.
                    </p>

                    <button form="send-verification"
                        class="mt-2 text-sm font-semibold text-amber-800
                               underline underline-offset-4
                               hover:text-amber-900">
                        Re-send verification email
                    </button>

                </div>


                @if (session('status') === 'verification-link-sent')
                    <div
                        class="mt-3 rounded-xl border border-emerald-200
                               bg-emerald-50 p-4">
                        <p class="text-sm font-medium text-emerald-700">
                            A new verification link has been sent to your email address.
                        </p>
                    </div>
                @endif

            @endif

        </div>


        <div class="flex items-center gap-4">

            <x-ui.button type="submit">
                Save Changes
            </x-ui.button>


            @if (session('status') === 'profile-updated')
                <p x-data="{ show: true }" x-show="show" x-transition x-init="setTimeout(() => show = false, 2500)"
                    class="text-sm font-medium text-emerald-600">
                    Saved successfully.
                </p>
            @endif

        </div>

    </form>

</section>
