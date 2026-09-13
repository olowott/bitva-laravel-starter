<section>

    <header>
        <h2 class="text-base font-semibold text-slate-900 dark:text-white">
            Delete Account
        </h2>

        <p class="mt-1 text-sm leading-6 text-slate-500 dark:text-slate-400">
            Permanently delete your account and all associated data.
        </p>
    </header>

    <div class="mt-6">
        <x-ui.button variant="danger" x-data @click="$dispatch('open-modal', 'confirm-user-deletion')">
            Delete Account
        </x-ui.button>
    </div>

    <x-ui.modal name="confirm-user-deletion" title="Delete Account">
        <form method="POST" action="{{ route('profile.destroy') }}" class="space-y-6">
            @csrf
            @method('DELETE')

            <div>
                <p class="text-sm leading-6 text-slate-600 dark:text-slate-300">
                    Once your account is deleted, all of its resources and data
                    will be permanently removed. Enter your password to confirm.
                </p>
            </div>

            <x-form.input id="delete_user_password" name="password" label="Password" type="password"
                autocomplete="current-password" :messages="$errors->userDeletion->get('password')" />

            <div class="flex justify-end gap-3">

                <x-ui.button type="button" variant="secondary" x-data
                    @click="$dispatch('close-modal', 'confirm-user-deletion')">
                    Cancel
                </x-ui.button>

                <x-ui.button type="submit" variant="danger">
                    Delete Account
                </x-ui.button>

            </div>

        </form>
    </x-ui.modal>

</section>
