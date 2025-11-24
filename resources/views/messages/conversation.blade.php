<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center space-x-4">
            <a href="{{ route('messages.index') }}" class="text-black hover:text-gray-700">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
                </svg>
            </a>
            <div>
                <h2 class="font-semibold text-xl text-black leading-tight">{{ $user->name }}</h2>
                <p class="text-sm text-gray-600">{{ ucfirst($user->role) }}</p>
            </div>
        </div>
    </x-slot>

    <div class="py-6">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white border border-black rounded-lg overflow-hidden">
                <div class="h-[500px] overflow-y-auto p-6 space-y-4" id="messageContainer">
                    @if($messages->count() > 0)
                        @foreach($messages as $message)
                            @if($message->from_id === auth()->id())
                                <div class="flex justify-end">
                                    <div class="max-w-xs lg:max-w-md">
                                        <div class="bg-black text-white rounded-lg px-4 py-3">
                                            <p class="text-sm break-words">{{ $message->message_text }}</p>
                                        </div>
                                        <p class="text-xs text-gray-500 mt-1 text-right">
                                            {{ $message->created_at->format('M d, h:i A') }}
                                        </p>
                                    </div>
                                </div>
                            @else
                                <div class="flex justify-start">
                                    <div class="max-w-xs lg:max-w-md">
                                        <div class="bg-gray-200 text-black rounded-lg px-4 py-3 border border-gray-300">
                                            <p class="text-sm break-words">{{ $message->message_text }}</p>
                                        </div>
                                        <p class="text-xs text-gray-500 mt-1">
                                            {{ $message->created_at->format('M d, h:i A') }}
                                        </p>
                                    </div>
                                </div>
                            @endif
                        @endforeach
                    @else
                        <div class="text-center py-12 text-gray-500">
                            <p>No messages yet. Start the conversation!</p>
                        </div>
                    @endif
                </div>

                <div class="border-t border-black p-4 bg-white">
                    <form action="{{ route('messages.store', $user) }}" method="POST" class="flex space-x-2">
                        @csrf
                        <textarea 
                            name="message_text" 
                            rows="2"
                            placeholder="Type your message..."
                            class="flex-1 border border-black rounded-lg px-4 py-2 focus:outline-none focus:ring-2 focus:ring-black resize-none"
                            required
                        ></textarea>
                        <button 
                            type="submit" 
                            class="bg-black hover:bg-white hover:text-black border-2 border-black text-white px-6 py-2 rounded-lg font-semibold"
                        >
                            Send
                        </button>
                    </form>
                    @error('message_text')
                        <p class="text-red-600 text-sm mt-2">{{ $message }}</p>
                    @enderror
                </div>
            </div>
        </div>
    </div>

    <script>
        // Auto-scroll to bottom on page load
        document.addEventListener('DOMContentLoaded', function() {
            const container = document.getElementById('messageContainer');
            container.scrollTop = container.scrollHeight;
        });
    </script>
</x-app-layout>
