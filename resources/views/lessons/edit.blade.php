<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                Edit Lesson: {{ $lesson->title }}
            </h2>
            <a href="{{ route('courses.show', $course) }}" class="text-black hover:underline border border-black px-4 py-2 rounded bg-white hover:bg-black hover:text-white">
                Back to Course
            </a>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-3xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden border border-black sm:rounded-lg">
                <div class="p-6">
                    <form action="{{ route('lessons.update', $lesson) }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        @method('PUT')

                        <div class="mb-4">
                            <x-input-label for="title" value="Lesson Title" />
                            <x-text-input id="title" name="title" type="text" class="mt-1 block w-full" :value="old('title', $lesson->title)" required />
                            <x-input-error class="mt-2" :messages="$errors->get('title')" />
                        </div>

                        <div class="mb-4">
                            <x-input-label for="content" value="Lesson Content" />
                            <textarea id="content" name="content" rows="8" class="mt-1 block w-full border-black rounded-md shadow-sm focus:border-black focus:ring-black" required>{{ old('content', $lesson->content) }}</textarea>
                            <x-input-error class="mt-2" :messages="$errors->get('content')" />
                        </div>

                        <div x-data="{ hasFile: {{ $lesson->attachment ? 'true' : 'false' }}, newFileSelected: false }">
                            <div class="mb-4">
                                <x-input-label for="attachment" value="Attachment (Optional)" />
                                @if($lesson->attachment)
                                    <p class="text-sm text-black mb-2">
                                        Current: <a href="{{ route('file.download', basename($lesson->attachment)) }}" class="text-black underline">{{ $lesson->attachment_name ?? basename($lesson->attachment) }}</a>
                                    </p>
                                @endif
                                <input 
                                    id="attachment" 
                                    name="attachment" 
                                    type="file" 
                                    accept=".jpg,.jpeg,.png,.gif,.pdf,.doc,.docx" 
                                    class="mt-1 block w-full border border-black rounded-md p-2"
                                    x-on:change="newFileSelected = $event.target.files.length > 0; hasFile = true"
                                />
                                <p class="text-xs text-black mt-1">Accepted: Images (JPG, PNG, GIF), PDF, Word (DOC, DOCX) - Max 10MB</p>
                                <p class="text-xs text-gray-600 mt-1" x-show="newFileSelected">New file selected - will replace current attachment</p>
                                <x-input-error class="mt-2" :messages="$errors->get('attachment')" />
                            </div>

                            <div class="mb-4" x-show="hasFile" x-transition>
                                <x-input-label for="attachment_name" value="File Display Name (optional)" />
                                <x-text-input id="attachment_name" name="attachment_name" type="text" placeholder="e.g., Lecture Notes, Assignment 1" class="mt-1 block w-full" :value="old('attachment_name', $lesson->attachment_name)" />
                                <p class="text-xs text-black mt-1">Give your file a friendly name students will see</p>
                                <x-input-error class="mt-2" :messages="$errors->get('attachment_name')" />
                            </div>
                        </div>

                        <div class="mb-4">
                            <x-input-label for="order_number" value="Order Number" />
                            <x-text-input id="order_number" name="order_number" type="number" min="1" class="mt-1 block w-full" :value="old('order_number', $lesson->order_number)" required />
                            <x-input-error class="mt-2" :messages="$errors->get('order_number')" />
                        </div>

                        <div class="mb-4">
                            <x-input-label for="duration" value="Duration (minutes)" />
                            <x-text-input id="duration" name="duration" type="number" min="1" class="mt-1 block w-full" :value="old('duration', $lesson->duration)" required />
                            <x-input-error class="mt-2" :messages="$errors->get('duration')" />
                        </div>

                        <div class="flex items-center justify-between mt-6">
                            <a href="{{ route('courses.show', $course) }}" class="text-black hover:underline flex items-center">
                                <svg class="w-5 h-5 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
                                </svg>
                                Back to Course
                            </a>
                            <div class="flex gap-4">
                                <a href="{{ route('courses.show', $course) }}" class="text-black hover:underline">Cancel</a>
                                <x-primary-button>Save Changes</x-primary-button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
