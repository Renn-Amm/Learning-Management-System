<section>
    <header>
        <h2 class="text-lg font-medium text-black">
            Profile Picture
        </h2>

        <p class="mt-1 text-sm text-gray-600">
            Upload or update your profile picture.
        </p>
    </header>

    <div class="mt-6">
        @if(auth()->user()->profile_image)
            <div class="mb-4">
                <img src="{{ asset('storage/' . auth()->user()->profile_image) }}" alt="Profile Image" class="w-32 h-32 rounded-full object-cover border-2 border-black">
            </div>
            <form method="POST" action="{{ route('profile.deleteImage') }}" class="mb-4">
                @csrf
                @method('DELETE')
                <button type="submit" class="bg-white hover:bg-black hover:text-white border border-black text-black px-4 py-2 rounded text-sm">
                    Remove Profile Picture
                </button>
            </form>
        @else
            <div class="mb-4">
                <div class="w-32 h-32 rounded-full bg-white border-2 border-black flex items-center justify-center">
                    <span class="text-4xl text-black">{{ strtoupper(substr(auth()->user()->name, 0, 1)) }}</span>
                </div>
            </div>
        @endif

        <form method="POST" action="{{ route('profile.updateImage') }}" enctype="multipart/form-data">
            @csrf
            
            <div>
                <x-input-label for="profile_image" value="Choose New Picture" />
                <input id="profile_image" name="profile_image" type="file" accept="image/*" class="mt-1 block w-full border border-black rounded-md p-2" required>
                <x-input-error class="mt-2" :messages="$errors->get('profile_image')" />
                <p class="text-xs text-gray-600 mt-1">JPG, PNG, GIF up to 2MB</p>
            </div>

            <div class="flex items-center gap-4 mt-4">
                <button type="submit" class="bg-black hover:bg-white hover:text-black border border-black text-white px-4 py-2 rounded">
                    Upload Picture
                </button>
            </div>
        </form>
    </div>
</section>
