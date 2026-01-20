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
                            <div class="flex items-center p-6 hover:bg-gray-50 transition">
                                <a href="{{ route('messages.conversation', $conv['partner']) }}" class="flex-1 flex items-start space-x-4">
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
                                </a>
                                <div class="relative ml-4" x-data="{ open: false }">
                                    <button @click.stop="open = !open" type="button" class="p-2 rounded-full hover:bg-gray-200 transition-colors">
                                        <svg class="w-5 h-5 text-gray-500" fill="currentColor" viewBox="0 0 20 20">
                                            <path d="M10 6a2 2 0 110-4 2 2 0 010 4zM10 12a2 2 0 110-4 2 2 0 010 4zM10 18a2 2 0 110-4 2 2 0 010 4z"/>
                                        </svg>
                                    </button>
                                    <div x-show="open" 
                                         x-cloak
                                         @click.away="open = false"
                                         class="absolute right-0 mt-2 w-48 bg-white border border-black rounded-lg shadow-lg z-50">
                                        <button type="button" 
                                                @click.stop="open = false; document.getElementById('deleteModal-{{ $conv['partner']->id }}').classList.remove('hidden')"
                                                class="w-full text-left px-4 py-3 text-red-600 hover:bg-gray-100 rounded-lg flex items-center space-x-2">
                                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                            </svg>
                                            <span>Delete Conversation</span>
                                        </button>
                                    </div>
                                </div>
                            </div>
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

    @if($conversations->count() > 0)
        @foreach($conversations as $conv)
            <div id="deleteModal-{{ $conv['partner']->id }}" class="hidden fixed inset-0 z-50 overflow-y-auto">
                <div class="flex items-center justify-center min-h-screen px-4 pt-4 pb-20 text-center sm:p-0">
                    <div class="fixed inset-0 bg-black opacity-40" onclick="document.getElementById('deleteModal-{{ $conv['partner']->id }}').classList.add('hidden')"></div>
                    
                    <div class="relative bg-white rounded-lg border border-black shadow-xl max-w-md w-full mx-auto p-6 z-10">
                        <div class="text-center">
                            <div class="mx-auto flex items-center justify-center h-12 w-12 rounded-full bg-red-100 mb-4">
                                <svg class="h-6 w-6 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                                </svg>
                            </div>
                            <h3 class="text-lg font-semibold text-black mb-2">Delete Conversation</h3>
                            <p class="text-gray-600 mb-6">Are you sure you want to delete this entire conversation with <strong>{{ $conv['partner']->name }}</strong>? This will remove all messages from your view. The other user will still be able to see the messages.</p>
                            
                            <div class="flex justify-center space-x-3">
                                <button type="button" 
                                        onclick="document.getElementById('deleteModal-{{ $conv['partner']->id }}').classList.add('hidden')"
                                        class="px-4 py-2 bg-white border border-black text-black rounded-lg hover:bg-gray-100 font-medium">
                                    Cancel
                                </button>
                                <form action="{{ route('messages.conversation.delete', $conv['partner']) }}" method="POST" class="inline">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" 
                                            class="px-4 py-2 bg-red-600 border border-red-600 text-white rounded-lg hover:bg-red-700 font-medium">
                                        Delete Conversation
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        @endforeach
    @endif
</x-app-layout>
