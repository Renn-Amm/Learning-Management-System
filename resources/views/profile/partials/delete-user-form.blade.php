<section class="space-y-6">
    <header>
        <h2 class="text-lg font-medium text-black">
            Delete Account
        </h2>

        <p class="mt-1 text-sm text-gray-600">
            Once your account is deleted, all of its resources and data will be permanently deleted. This includes:
        </p>
        <ul class="mt-2 text-sm text-gray-600 list-disc list-inside">
            <li>Your profile and personal information</li>
            <li>All courses you created (if teacher)</li>
            <li>Your course enrollments and progress (if student)</li>
            <li>All your messages</li>
        </ul>
    </header>

    <button
        type="button"
        x-data=""
        x-on:click.prevent="$dispatch('open-modal', 'confirm-user-deletion')"
        class="bg-white hover:bg-black hover:text-white border-2 border-black text-black px-4 py-2 rounded"
    >Delete Account</button>

    <x-modal name="confirm-user-deletion" :show="$errors->userDeletion->isNotEmpty()" focusable>
        <form method="post" action="{{ route('profile.destroy') }}" class="p-6 bg-white" onsubmit="return true;">
            @csrf
            @method('delete')

            <h2 class="text-lg font-medium text-black">
                Are you sure you want to delete your account?
            </h2>

            <p class="mt-1 text-sm text-black">
                Once your account is deleted, all of its resources and data will be permanently deleted. Please enter your password to confirm you would like to permanently delete your account.
            </p>

            <div class="mt-6">
                <x-input-label for="password" value="Password" class="text-black" />

                <input
                    id="password"
                    name="password"
                    type="password"
                    class="mt-1 block w-full border-2 border-black rounded-md p-2 bg-white text-black focus:border-black focus:ring-black"
                    placeholder="Enter your password"
                    required
                />

                <x-input-error :messages="$errors->userDeletion->get('password')" class="mt-2" />
            </div>

            <div class="mt-6 flex justify-end gap-3">
                <button type="button" x-on:click="$dispatch('close')" class="bg-white hover:bg-black hover:text-white border-2 border-black text-black px-4 py-2 rounded">
                    Cancel
                </button>

                <button type="submit" class="bg-black hover:bg-white hover:text-black border-2 border-black text-white px-4 py-2 rounded">
                    Delete Account
                </button>
            </div>
        </form>
    </x-modal>
</section>
