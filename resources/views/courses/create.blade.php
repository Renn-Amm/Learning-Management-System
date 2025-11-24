<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                Create New Course
            </h2>
            <a href="{{ route('courses.index') }}" class="text-black hover:underline border border-black px-4 py-2 rounded bg-white hover:bg-black hover:text-white">
                Back to Courses
            </a>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-3xl mx-auto sm:px-6 lg:px-8">
            @if($errors->any())
                <div class="mb-4 bg-white border-2 border-black text-black px-4 py-3 rounded">
                    <strong>Please fix the following errors:</strong>
                    <ul class="mt-2 list-disc list-inside">
                        @foreach($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <div class="bg-white overflow-hidden border border-black sm:rounded-lg">
                <div class="p-6">
                    <form action="{{ route('courses.store') }}" method="POST" enctype="multipart/form-data">
                        @csrf

                        <div class="mb-4">
                            <x-input-label for="title" value="Course Title" />
                            <x-text-input id="title" name="title" type="text" class="mt-1 block w-full" :value="old('title')" required />
                            <x-input-error class="mt-2" :messages="$errors->get('title')" />
                        </div>

                        <div class="mb-4">
                            <x-input-label for="description" value="Description" />
                            <textarea id="description" name="description" rows="4" class="mt-1 block w-full border-black rounded-md shadow-sm focus:border-black focus:ring-black" required>{{ old('description') }}</textarea>
                            <x-input-error class="mt-2" :messages="$errors->get('description')" />
                        </div>

                        <div class="mb-4">
                            <x-input-label for="category_id" value="Category" />
                            <select id="category_id" name="category_id" class="mt-1 block w-full border-black rounded-md shadow-sm focus:border-black focus:ring-black" required>
                                <option value="">Select a category</option>
                                @foreach($categories as $category)
                                    <option value="{{ $category->id }}" {{ old('category_id') == $category->id ? 'selected' : '' }}>
                                        {{ $category->name }}
                                    </option>
                                @endforeach
                            </select>
                            <x-input-error class="mt-2" :messages="$errors->get('category_id')" />
                        </div>

                        <div class="mb-4">
                            <x-input-label for="level" value="Level" />
                            <select id="level" name="level" class="mt-1 block w-full border-black rounded-md shadow-sm focus:border-black focus:ring-black" required>
                                <option value="">Select level</option>
                                <option value="beginner" {{ old('level') == 'beginner' ? 'selected' : '' }}>Beginner</option>
                                <option value="intermediate" {{ old('level') == 'intermediate' ? 'selected' : '' }}>Intermediate</option>
                                <option value="advanced" {{ old('level') == 'advanced' ? 'selected' : '' }}>Advanced</option>
                            </select>
                            <x-input-error class="mt-2" :messages="$errors->get('level')" />
                        </div>

                        <div class="mb-4">
                            <x-input-label for="skills" value="Skills (optional - separate with commas)" />
                            <input 
                                id="skills" 
                                name="skills" 
                                type="text" 
                                placeholder="e.g., Laravel, Vue.js, TailwindCSS"
                                value="{{ old('skills') }}"
                                class="mt-1 block w-full border border-black rounded-md p-2"
                            >
                            <p class="text-xs text-gray-600 mt-1">Enter skill names separated by commas. Each skill will be displayed with a unique color.</p>
                            <x-input-error class="mt-2" :messages="$errors->get('skills')" />
                        </div>

                        <div class="mb-4">
                            <x-input-label for="thumbnail" value="Thumbnail Image (optional)" />
                            <input id="thumbnail" name="thumbnail" type="file" accept="image/*" class="mt-1 block w-full border border-black rounded-md p-2">
                            <x-input-error class="mt-2" :messages="$errors->get('thumbnail')" />
                        </div>

                        <div class="flex items-center justify-between mt-6">
                            <a href="{{ route('courses.index') }}" class="text-black hover:underline flex items-center">
                                <svg class="w-5 h-5 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
                                </svg>
                                Back to Courses
                            </a>
                            <div class="flex gap-4">
                                <a href="{{ route('courses.index') }}" class="text-black hover:underline">Cancel</a>
                                <x-primary-button>Create Course</x-primary-button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
