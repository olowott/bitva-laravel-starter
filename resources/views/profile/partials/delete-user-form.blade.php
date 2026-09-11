<section>

    <header>
        <h2 class="text-base font-semibold text-slate-900">
            Delete Account
        </h2>

        <p class="mt-1 text-sm leading-6 text-slate-500">
            Permanently delete your account and all associated data.
        </p>
    </header>

    <div class="mt-6">
        <x-ui.button
            variant="danger"
            x-data
            x-on:click.prevent="$dispatch('open-modal', 'confirm-user-deletion')"
        >
            Delete Account
        </x-ui.button>
    </div>

    <x-modal
        name="confirm-user-deletion"
        :show="$errors->userDeletion->isNotEmpty()"
        focusable
    >
        <form
            method="POST"
            action="{{ route('profile.destroy') }}"
            class="p-6"
        >
            @csrf
            @method('delete')

            <h2 class="text-lg font-semibold text-slate-900">
                Are you sure you want to delete your account?
            </h2>

            <p class="mt-2 text-sm leading-6 text-slate-500">
                Once your account is deleted, all of its resources and data
                will be permanently removed. Enter your password to confirm.
            </p>

            <div class="mt-6">
                <x-form.input
                    id="delete_user_password"
                    name="password"
                    label="Password"
                    type="password"
                    autocomplete="current-password"
                    :messages="$errors->userDeletion->get('password')"
                />
            </div>

            <div class="mt-6 flex justify-end gap-3">

                <x-ui.button
                    type="button"
                    variant="secondary"
                    x-on:click="$dispatch('close')"
                >
                    Cancel
                </x-ui.button>

                <x-ui.button
                    type="submit"
                    variant="danger"
                >
                    Delete Account
                </x-ui.button>

            </div>

        </form>
    </x-modal>

</section>