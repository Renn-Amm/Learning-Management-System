<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            Add Lesson to: {{ $course->title }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-3xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden border border-black sm:rounded-lg">
                <div class="p-6">
                    <form action="{{ route('lessons.store', $course) }}" method="POST" enctype="multipart/form-data">
                        @csrf

                        <div class="mb-4">
                            <x-input-label for="title" value="Lesson Title" />
                            <x-text-input id="title" name="title" type="text" class="mt-1 block w-full" :value="old('title')" required />
                            <x-input-error class="mt-2" :messages="$errors->get('title')" />
                        </div>

                        <div class="mb-4">
                            <x-input-label for="content" value="Lesson Content" />
                            <textarea id="content" name="content" rows="8" class="mt-1 block w-full border-black rounded-md shadow-sm focus:border-black focus:ring-black" required>{{ old('content') }}</textarea>
                            <x-input-error class="mt-2" :messages="$errors->get('content')" />
                        </div>

                        <div x-data="{ hasFile: false }">
                            <div class="mb-4">
                                <x-input-label for="attachment" value="Attachment (optional)" />
                                <input 
                                    id="attachment" 
                                    name="attachment" 
                                    type="file" 
                                    accept=".jpg,.jpeg,.png,.gif,.pdf,.doc,.docx" 
                                    class="mt-1 block w-full border border-black rounded-md p-2"
                                    x-on:change="hasFile = $event.target.files.length > 0"
                                >
                                <p class="text-xs text-black mt-1">Accepted: Images (JPG, PNG, GIF), PDF, Word (DOC, DOCX) - Max 10MB</p>
                                <x-input-error class="mt-2" :messages="$errors->get('attachment')" />
                            </div>

                            <div class="mb-4" x-show="hasFile" x-transition>
                                <x-input-label for="attachment_name" value="File Display Name (optional)" />
                                <x-text-input id="attachment_name" name="attachment_name" type="text" placeholder="e.g., Lecture Notes, Assignment 1" class="mt-1 block w-full" :value="old('attachment_name')" />
                                <p class="text-xs text-black mt-1">Give your file a friendly name students will see</p>
                                <x-input-error class="mt-2" :messages="$errors->get('attachment_name')" />
                            </div>
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
                            <a href="{{ route('courses.show', $course) }}" class="text-black hover:underline">Cancel</a>
                            <x-primary-button>Create Lesson</x-primary-button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
