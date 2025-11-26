<section>
    <header>
        <h2 class="text-lg font-medium text-black">
            Update Name
        </h2>

        <p class="mt-1 text-sm text-gray-600">
            Change your display name.
        </p>
    </header>

    <form method="POST" action="{{ route('profile.updateName') }}" class="mt-6 space-y-6">
        @csrf
        @method('PATCH')

        <div>
            <x-input-label for="name" value="Name" />
            <x-text-input id="name" name="name" type="text" class="mt-1 block w-full" :value="old('name', $user->name)" required />
            <x-input-error class="mt-2" :messages="$errors->get('name')" />
        </div>

        <div class="flex items-center gap-4">
            <button type="submit" class="bg-black hover:bg-white hover:text-black border border-black text-white px-4 py-2 rounded">
                Save Name
            </button>
        </div>
    </form>
</section>
