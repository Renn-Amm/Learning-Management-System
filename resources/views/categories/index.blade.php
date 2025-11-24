<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                Categories
            </h2>
            <a href="{{ route('categories.create') }}" class="bg-black hover:bg-white hover:text-black border border-black text-white px-4 py-2 rounded-md text-sm font-medium">
                Create Category
            </a>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            @if(session('success'))
                <div class="mb-4 bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded">
                    {{ session('success') }}
                </div>
            @endif

            @if(session('error'))
                <div class="mb-4 bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded">
                    {{ session('error') }}
                </div>
            @endif

            <div class="bg-white overflow-hidden border border-black sm:rounded-lg">
                <div class="p-6 text-black">
                    @if($categories->isEmpty())
                        <p class="text-black">No categories created yet.</p>
                    @else
                        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                            @foreach($categories as $category)
                                <div class="border border-black rounded-lg p-6 hover:shadow-lg transition">
                                    <h3 class="text-lg font-semibold mb-2 text-black">{{ $category->name }}</h3>
                                    <p class="text-sm text-black mb-2">{{ $category->courses_count }} courses</p>
                                    @if($category->user)
                                        <p class="text-xs text-black mb-4">Created by: {{ $category->user->name }}</p>
                                    @else
                                        <p class="text-xs text-black mb-4">System Category</p>
                                    @endif
                                    <div class="flex gap-2">
                                        <a href="{{ route('categories.show', $category) }}" class="flex-1 text-center bg-black hover:bg-white hover:text-black border border-black text-white px-3 py-2 rounded text-sm">
                                            View
                                        </a>
                                        @if($category->user_id && $category->user_id === auth()->id())
                                            <a href="{{ route('categories.edit', $category) }}" class="flex-1 text-center bg-white hover:bg-black hover:text-white border border-black text-black px-3 py-2 rounded text-sm">
                                                Edit
                                            </a>
                                            <form action="{{ route('categories.destroy', $category) }}" method="POST" onsubmit="return confirm('Are you sure?')">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="bg-white hover:bg-black hover:text-white border border-black text-black px-3 py-2 rounded text-sm">
                                                    Delete
                                                </button>
                                            </form>
                                        @endif
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
