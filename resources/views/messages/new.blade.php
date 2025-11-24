<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center space-x-4">
            <a href="{{ route('messages.index') }}" class="text-black hover:text-gray-700">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
                </svg>
            </a>
            <h2 class="font-semibold text-xl text-black leading-tight">
                New Conversation
            </h2>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-3xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden border border-black sm:rounded-lg">
                <div class="p-6">
                    <p class="text-gray-700 mb-6">
                        Select a {{ auth()->user()->isStudent() ? 'teacher' : 'student' }} to start a conversation:
                    </p>

                    @if($users->count() > 0)
                        <div class="space-y-2">
                            @foreach($users as $user)
                                <a href="{{ route('messages.conversation', $user) }}" class="block p-4 border border-black rounded hover:bg-gray-50 transition">
                                    <div class="flex items-center space-x-4">
                                        <div class="w-12 h-12 rounded-full bg-black text-white flex items-center justify-center font-bold text-lg">
                                            {{ strtoupper(substr($user->name, 0, 1)) }}
                                        </div>
                                        <div>
                                            <p class="font-semibold text-black">{{ $user->name }}</p>
                                            <p class="text-sm text-gray-600">{{ $user->email }}</p>
                                        </div>
                                    </div>
                                </a>
                            @endforeach
                        </div>
                    @else
                        <div class="text-center py-12">
                            <p class="text-gray-600">No {{ auth()->user()->isStudent() ? 'teachers' : 'students' }} available.</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
