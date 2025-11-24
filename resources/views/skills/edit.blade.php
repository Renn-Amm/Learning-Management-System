<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-black leading-tight">
            Edit Skill: {{ $skill->name }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-3xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden border border-black sm:rounded-lg">
                <div class="p-6">
                    <form action="{{ route('skills.update', $skill) }}" method="POST">
                        @csrf
                        @method('PUT')

                        <div class="mb-4">
                            <x-input-label for="name" value="Skill Name" />
                            <x-text-input id="name" name="name" type="text" class="mt-1 block w-full" :value="old('name', $skill->name)" required />
                            <x-input-error class="mt-2" :messages="$errors->get('name')" />
                        </div>

                        <div class="mb-4">
                            <x-input-label for="color_code" value="Background Color" />
                            <div class="flex gap-4 items-center mt-1">
                                <input id="color_code" name="color_code" type="color" value="{{ old('color_code', $skill->color_code) }}" class="h-10 w-20 border border-black rounded cursor-pointer" required />
                                <span class="text-sm text-black">Choose a background color for this skill tag</span>
                            </div>
                            <p class="text-xs text-black mt-2">Text color will automatically adjust for readability</p>
                            <x-input-error class="mt-2" :messages="$errors->get('color_code')" />
                        </div>

                        <div class="mb-4">
                            <p class="text-sm text-black font-semibold mb-2">Preview:</p>
                            <div id="preview" class="inline-block px-4 py-2 rounded text-sm" style="background-color: {{ $skill->color_code }}; color: {{ $skill->getTextColor() }};">
                                {{ $skill->name }}
                            </div>
                        </div>

                        <div class="flex items-center justify-end gap-4 mt-6">
                            <a href="{{ route('skills.index') }}" class="text-black hover:underline">Cancel</a>
                            <x-primary-button>Update Skill</x-primary-button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <script>
        const colorInput = document.getElementById('color_code');
        const nameInput = document.getElementById('name');
        const preview = document.getElementById('preview');

        function getTextColor(bgColor) {
            const hex = bgColor.replace('#', '');
            const r = parseInt(hex.substr(0, 2), 16);
            const g = parseInt(hex.substr(2, 2), 16);
            const b = parseInt(hex.substr(4, 2), 16);
            const brightness = ((r * 299) + (g * 587) + (b * 114)) / 1000;
            return brightness > 155 ? '#000000' : '#FFFFFF';
        }

        function updatePreview() {
            const bgColor = colorInput.value;
            const textColor = getTextColor(bgColor);
            const name = nameInput.value || 'Skill Preview';
            preview.style.backgroundColor = bgColor;
            preview.style.color = textColor;
            preview.textContent = name;
        }

        colorInput.addEventListener('input', updatePreview);
        nameInput.addEventListener('input', updatePreview);
    </script>
</x-app-layout>
