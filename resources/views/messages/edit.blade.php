<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            Edit Message
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden border border-black sm:rounded-lg">
                <div class="p-6">
                    <form action="{{ route('messages.update', $message) }}" method="POST">
                        @csrf
                        @method('PUT')

                        <div class="mb-4">
                            <label class="block text-sm font-medium text-gray-700 mb-2">To:</label>
                            <p class="text-lg font-semibold text-black">{{ $message->recipient->name }}</p>
                        </div>

                        <div class="mb-6">
                            <label for="message_text" class="block text-sm font-medium text-gray-700 mb-2">
                                Message <span class="text-red-500">*</span>
                            </label>
                            <textarea 
                                id="message_text" 
                                name="message_text" 
                                rows="6"
                                class="w-full border border-black rounded px-3 py-2 focus:outline-none focus:ring-2 focus:ring-black @error('message_text') border-red-500 @enderror"
                                required
                            >{{ old('message_text', $message->message_text) }}</textarea>
                            @error('message_text')
                                <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <div class="flex gap-2">
                            <button type="submit" class="bg-black hover:bg-white hover:text-black border border-black text-white px-4 py-2 rounded-md">
                                Update Message
                            </button>
                            <a href="{{ route('messages.conversation', $message->recipient) }}" class="bg-white hover:bg-black hover:text-white border border-black text-black px-4 py-2 rounded-md">
                                Cancel
                            </a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
