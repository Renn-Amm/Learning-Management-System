<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            Add Lesson to: {{ $course->title }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-3xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6">
                    <form action="{{ route('lessons.store', $course) }}" method="POST">
                        @csrf

                        <div class="mb-4">
                            <x-input-label for="title" value="Lesson Title" />
                            <x-text-input id="title" name="title" type="text" class="mt-1 block w-full" :value="old('title')" required />
                            <x-input-error class="mt-2" :messages="$errors->get('title')" />
                        </div>

                        <div class="mb-4">
                            <x-input-label for="content" value="Lesson Content" />
                            <textarea id="content" name="content" rows="8" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:border-indigo-500 focus:ring-indigo-500" required>{{ old('content') }}</textarea>
                            <x-input-error class="mt-2" :messages="$errors->get('content')" />
                        </div>

                        <div class="mb-4">
                            <x-input-label for="order_number" value="Order Number" />
                            <x-text-input id="order_number" name="order_number" type="number" min="1" class="mt-1 block w-full" :value="old('order_number', 1)" required />
                            <x-input-error class="mt-2" :messages="$errors->get('order_number')" />
                        </div>

                        <div class="mb-4">
                            <x-input-label for="duration" value="Duration (minutes)" />
                            <x-text-input id="duration" name="duration" type="number" min="1" class="mt-1 block w-full" :value="old('duration')" required />
                            <x-input-error class="mt-2" :messages="$errors->get('duration')" />
                        </div>

                        <div class="flex items-center justify-end gap-4 mt-6">
                            <a href="{{ route('courses.show', $course) }}" class="text-gray-600 hover:text-gray-900">Cancel</a>
                            <x-primary-button>Create Lesson</x-primary-button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
