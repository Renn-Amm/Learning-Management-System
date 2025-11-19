<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            Category: {{ $category->name }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    <h3 class="text-lg font-semibold mb-4">Courses in this category</h3>

                    @if($courses->isEmpty())
                        <p class="text-gray-500">No courses in this category yet.</p>
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
                                        <span class="text-xs px-2 py-1 bg-indigo-100 text-indigo-800 rounded">{{ ucfirst($course->level) }}</span>
                                        <h4 class="font-semibold text-lg mb-2 mt-2">{{ $course->title }}</h4>
                                        <p class="text-sm text-gray-600 mb-2">{{ Str::limit($course->description, 100) }}</p>
                                        <p class="text-sm text-gray-500 mb-2">Teacher: {{ $course->teacher->name }}</p>
                                        <div class="flex items-center justify-between text-sm text-gray-500 mb-4">
                                            <span>{{ $course->lessons_count }} lessons</span>
                                            <span>{{ $course->enrollments_count }} students</span>
                                        </div>
                                        <a href="{{ route('courses.show', $course) }}" class="block text-center bg-indigo-600 hover:bg-indigo-700 text-white px-4 py-2 rounded">
                                            View Course
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
