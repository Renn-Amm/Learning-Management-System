<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            All Courses
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            @if(session('success'))
                <div class="mb-4 bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded">
                    {{ session('success') }}
                </div>
            @endif

            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg mb-6">
                <div class="p-6">
                    <h3 class="font-semibold text-lg mb-4">Filter by Category</h3>
                    <div class="flex flex-wrap gap-2">
                        <a href="{{ route('courses.index') }}" class="px-4 py-2 rounded border {{ !request('category') ? 'bg-black text-white border-black' : 'bg-white text-black border-black hover:bg-black hover:text-white' }}">
                            All Courses
                        </a>
                        @foreach($categories as $category)
                            <a href="{{ route('courses.index', ['category' => $category->id]) }}" class="px-4 py-2 rounded border {{ request('category') == $category->id ? 'bg-black text-white border-black' : 'bg-white text-black border-black hover:bg-black hover:text-white' }}">
                                {{ $category->name }}
                            </a>
                        @endforeach
                    </div>
                </div>
            </div>

            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    @if($courses->isEmpty())
                        <p class="text-gray-500">No courses available in this category.</p>
                    @else
                        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                            @foreach($courses as $course)
                                <div class="border rounded-lg overflow-hidden hover:shadow-lg transition">
                                    @if($course->thumbnail)
                                        <img src="{{ asset('storage/' . $course->thumbnail) }}" alt="{{ $course->title }}" class="w-full h-48 object-cover">
                                    @else
                                        <div class="w-full h-48 bg-gray-200 flex items-center justify-center">
                                            <span class="text-gray-400">No thumbnail</span>
                                        </div>
                                    @endif
                                    
                                    <div class="p-4">
                                        <div class="flex items-center gap-2 mb-2">
                                            <span class="text-xs px-2 py-1 bg-indigo-100 text-indigo-800 rounded">{{ ucfirst($course->level) }}</span>
                                            <span class="text-xs px-2 py-1 bg-gray-100 text-gray-800 rounded">{{ $course->category->name }}</span>
                                        </div>
                                        <h4 class="font-semibold text-lg mb-2">{{ $course->title }}</h4>
                                        <p class="text-sm text-gray-600 mb-2">{{ Str::limit($course->description, 100) }}</p>
                                        <p class="text-sm text-gray-500 mb-2">Teacher: {{ $course->teacher->name }}</p>
                                        <div class="flex items-center justify-between text-sm text-gray-500 mb-4">
                                            <span>{{ $course->lessons_count }} lessons</span>
                                            <span>{{ $course->enrollments_count }} students</span>
                                        </div>
                                        <a href="{{ route('courses.show', $course) }}" class="block text-center bg-indigo-600 hover:bg-indigo-700 text-white px-4 py-2 rounded">
                                            View Details
                                        </a>
                                    </div>
                                </div>
                            @endforeach
                        </div>

                        <div class="mt-6">
                            {{ $courses->links() }}
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
