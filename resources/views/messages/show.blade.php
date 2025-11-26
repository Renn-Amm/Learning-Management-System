<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                Message Details
            </h2>
            <a href="{{ route('messages.index') }}" class="bg-white hover:bg-black hover:text-white border border-black text-black px-4 py-2 rounded-md text-sm">
                Back to Messages
            </a>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden border border-black sm:rounded-lg">
                <div class="p-6">
                    <div class="mb-4">
                        <p class="text-sm text-gray-600">From:</p>
                        <p class="text-lg font-semibold text-black">{{ $message->sender->name }}</p>
                    </div>

                    <div class="mb-4">
                        <p class="text-sm text-gray-600">To:</p>
                        <p class="text-lg font-semibold text-black">{{ $message->recipient->name }}</p>
                    </div>

                    <div class="mb-4">
                        <p class="text-sm text-gray-600">Date:</p>
                        <p class="text-black">{{ $message->created_at->format('M d, Y h:i A') }}</p>
                    </div>

                    <div class="mb-6">
                        <p class="text-sm text-gray-600 mb-2">Message:</p>
                        <div class="bg-white border border-black rounded p-4">
                            <p class="text-black whitespace-pre-wrap">{{ $message->message_text }}</p>
                        </div>
                    </div>

                    <div class="flex gap-2">
                        <a href="{{ route('messages.conversation', $message->sender->id === auth()->id() ? $message->recipient : $message->sender) }}" class="bg-black hover:bg-white hover:text-black border border-black text-white px-4 py-2 rounded-md">
                            View Conversation
                        </a>
                        @if($message->from_id === auth()->id())
                            <a href="{{ route('messages.edit', $message) }}" class="bg-white hover:bg-black hover:text-white border border-black text-black px-4 py-2 rounded-md">
                                Edit Message
                            </a>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
