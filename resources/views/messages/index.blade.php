<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-black leading-tight">
                Messages
            </h2>
            <a href="{{ route('messages.create') }}" class="bg-black hover:bg-white hover:text-black border-2 border-black text-white px-4 py-2 rounded">
                New Conversation
            </a>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden border border-black sm:rounded-lg">
                @if($conversations->count() > 0)
                    <div class="divide-y divide-black">
                        @foreach($conversations as $conv)
                            <a href="{{ route('messages.conversation', $conv['partner']) }}" class="block p-6 hover:bg-gray-50 transition">
                                <div class="flex items-start space-x-4">
                                    <div class="w-12 h-12 rounded-full bg-black text-white flex items-center justify-center font-bold text-lg">
                                        {{ strtoupper(substr($conv['partner']->name, 0, 1)) }}
                                    </div>
                                    <div class="flex-1 min-w-0">
                                        <div class="flex justify-between items-start">
                                            <div class="flex items-center gap-2">
                                                <p class="text-lg font-semibold text-black">{{ $conv['partner']->name }}</p>
                                                @if($conv['unread_count'] > 0)
                                                    <span class="bg-red-600 text-white text-xs font-bold px-2 py-1 rounded-full">
                                                        {{ $conv['unread_count'] }}
                                                    </span>
                                                @endif
                                            </div>
                                            <span class="text-xs text-gray-500">
                                                {{ $conv['last_message']->created_at->diffForHumans() }}
                                            </span>
                                        </div>
                                        <p class="text-sm text-gray-600 mt-1">{{ ucfirst($conv['partner']->role) }}</p>
                                        <p class="text-sm text-gray-700 mt-2 truncate">
                                            {{ Str::limit($conv['last_message']->message_text, 80) }}
                                        </p>
                                    </div>
                                </div>
                            </a>
                        @endforeach
                    </div>
                @else
                    <div class="p-12 text-center">
                        <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z" />
                        </svg>
                        <h3 class="mt-4 text-lg font-semibold text-black">No conversations yet</h3>
                        <p class="mt-2 text-sm text-gray-600">Start a new conversation to get started.</p>
                        <a href="{{ route('messages.create') }}" class="mt-4 inline-block bg-black hover:bg-white hover:text-black border-2 border-black text-white px-6 py-2 rounded">
                            New Conversation
                        </a>
                    </div>
                @endif
            </div>
        </div>
    </div>
</x-app-layout>
