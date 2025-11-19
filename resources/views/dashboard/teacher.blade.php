<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                Teacher Dashboard
            </h2>
            <a href="{{ route('courses.create') }}" class="bg-black hover:bg-white hover:text-black border border-black text-white px-4 py-2 rounded-md text-sm font-medium">
                Create New Course
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

            <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-6">
                <div class="bg-white border border-black rounded-lg p-6">
                    <h3 class="text-sm font-semibold text-black mb-2">Total Courses</h3>
                    <p class="text-3xl font-bold text-black">{{ $courses->count() }}</p>
                </div>
                <div class="bg-white border border-black rounded-lg p-6">
                    <h3 class="text-sm font-semibold text-black mb-2">Total Students</h3>
                    <p class="text-3xl font-bold text-black">{{ $totalStudents }}</p>
                </div>
                <div class="bg-white border border-black rounded-lg p-6">
                    <h3 class="text-sm font-semibold text-black mb-2">Total Lessons</h3>
                    <p class="text-3xl font-bold text-black">{{ $totalLessons }}</p>
                </div>
            </div>

            <div class="bg-white overflow-hidden border border-black sm:rounded-lg">
                <div class="p-6 text-black">
                    <h3 class="text-lg font-semibold mb-4">My Courses</h3>

                    @if($courses->isEmpty())
                        <p class="text-black">You haven't created any courses yet.</p>
                    @else
                        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                            @foreach($courses as $course)
                                <div class="border rounded-lg overflow-hidden hover:shadow-lg transition">
                                    @if($course->thumbnail)
                                        <img src="{{ asset('storage/' . $course->thumbnail) }}" alt="{{ $course->title }}" class="w-full h-48 object-cover">
                                    @else
                                        <div class="w-full h-48 bg-white border border-black flex items-center justify-center">
                                            <span class="text-black">No thumbnail</span>
                                        </div>
                                    @endif
                                    
                                    <div class="p-4">
                                        <h4 class="font-semibold text-lg mb-2">{{ $course->title }}</h4>
                                        <p class="text-sm text-black mb-2">{{ Str::limit($course->description, 100) }}</p>
                                        <div class="flex items-center justify-between text-sm text-black mb-4">
                                            <span>{{ $course->lessons_count }} lessons</span>
                                            <span>{{ $course->enrollments_count }} students</span>
                                        </div>
                                        <div class="flex gap-2">
                                            <a href="{{ route('courses.show', $course) }}" class="flex-1 text-center bg-black hover:bg-white hover:text-black border border-black text-white px-3 py-2 rounded text-sm">
                                                Manage
                                            </a>
                                            <a href="{{ route('courses.edit', $course) }}" class="flex-1 text-center bg-white hover:bg-black hover:text-white border border-black text-black px-3 py-2 rounded text-sm">
                                                Edit
                                            </a>
                                        </div>
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
