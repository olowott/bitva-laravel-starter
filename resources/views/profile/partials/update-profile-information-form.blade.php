<section>
    <div class="border-b border-slate-200 pb-5 dark:border-slate-800">

        <h2 class="text-base font-semibold text-slate-900 dark:text-white">
            Profile Information
        </h2>

        <p class="mt-1 text-sm text-slate-500 dark:text-slate-400">
            Update your profile photo and personal information.
        </p>

    </div>

    <form method="POST" action="{{ route('profile.update') }}" enctype="multipart/form-data" class="mt-6">
        @csrf
        @method('PATCH')

        {{-- Avatar --}}
        <div
            class="flex flex-col gap-5 border-b border-slate-200 pb-6
        dark:border-slate-800
        sm:flex-row sm:items-center">

            <x-ui.avatar :user="$user" size="xl" />

            <div>
                <div class="flex flex-wrap items-center gap-3">

                    <label for="avatar"
                        class="inline-flex cursor-pointer items-center rounded-lg border
        border-slate-300 bg-white px-3 py-2
        text-sm font-medium text-slate-700 shadow-sm transition
        hover:bg-slate-50
        dark:border-slate-700 dark:bg-slate-900 dark:text-slate-300
        dark:hover:bg-slate-800">
                        <x-heroicon-o-camera class="mr-2 h-4 w-4" />

                        Change photo
                    </label>

                    <input id="avatar" name="avatar" type="file" accept=".jpg,.jpeg,.png,.webp" class="hidden">

                    @if ($user->avatar)
                        <button type="submit" form="remove-avatar-form"
                            class="text-sm font-medium text-red-600 hover:text-red-700
    dark:text-red-400 dark:hover:text-red-300">
                            Remove
                        </button>
                    @endif

                </div>

                <p class="mt-2 text-xs text-slate-500 dark:text-slate-400">
                    JPG, PNG or WebP. Maximum 2 MB.
                </p>

                <x-form.error name="avatar" class="mt-2" />
            </div>
        </div>

        {{-- Fields --}}
        <div class="mt-6 grid gap-6 md:grid-cols-2">

            <x-form.input name="name" label="Full name" type="text" :value="old('name', $user->name)" required autofocus />

            <x-form.input name="email" label="Email address" type="email" :value="old('email', $user->email)" required />

            <x-form.input name="phone" label="Phone" type="text" :value="old('phone', $user->phone)" />

            <x-form.input name="job_title" label="Job title" type="text" :value="old('job_title', $user->job_title)" />

            <div class="md:col-span-2">

                <x-form.textarea name="bio" label="Bio" rows="4" :value="old('bio', $user->bio)" />

                <div class="mt-1 flex justify-end">
                    <span class="text-xs text-slate-400 dark:text-slate-500">
                        Maximum 1,000 characters
                    </span>
                </div>

            </div>

        </div>

        {{-- Email verification --}}
        @if ($user instanceof \Illuminate\Contracts\Auth\MustVerifyEmail && !$user->hasVerifiedEmail())
            <x-ui.alert type="warning" class="mt-6">
                Your email address has not been verified.

                <button form="send-verification" class="ml-1 font-medium underline">
                    Resend verification email
                </button>
            </x-ui.alert>
        @endif

        <div class="mt-6 flex items-center gap-4">
            <x-ui.button type="submit">
                Save changes
            </x-ui.button>
        </div>

    </form>

    @if ($user->avatar)
        <form id="remove-avatar-form" method="POST" action="{{ route('profile.avatar.destroy') }}" class="hidden">
            @csrf
            @method('DELETE')
        </form>
    @endif

    @if ($user instanceof \Illuminate\Contracts\Auth\MustVerifyEmail && !$user->hasVerifiedEmail())
        <form id="send-verification" method="POST" action="{{ route('verification.send') }}" class="hidden">
            @csrf
        </form>
    @endif
</section>
