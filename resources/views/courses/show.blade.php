<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ $course->title }}
            </h2>
            @if(auth()->user()->isTeacher() && $course->teacher_id === auth()->id())
                <div class="flex gap-2">
                    <a href="{{ route('lessons.create', $course) }}" class="bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded-md text-sm">
                        Add Lesson
                    </a>
                    <a href="{{ route('courses.edit', $course) }}" class="bg-gray-600 hover:bg-gray-700 text-white px-4 py-2 rounded-md text-sm">
                        Edit Course
                    </a>
                    <form action="{{ route('courses.destroy', $course) }}" method="POST" onsubmit="return confirm('Are you sure you want to delete this course?')">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="bg-red-600 hover:bg-red-700 text-white px-4 py-2 rounded-md text-sm">
                            Delete Course
                        </button>
                    </form>
                </div>
            @endif
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

            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                <div class="lg:col-span-2">
                    <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg mb-6">
                        <div class="p-6">
                            @if($course->thumbnail)
                                <img src="{{ asset('storage/' . $course->thumbnail) }}" alt="{{ $course->title }}" class="w-full h-64 object-cover rounded mb-4">
                            @endif

                            <div class="flex items-center gap-2 mb-4">
                                <span class="text-sm px-3 py-1 bg-indigo-100 text-indigo-800 rounded">{{ ucfirst($course->level) }}</span>
                                <span class="text-sm px-3 py-1 bg-gray-100 text-gray-800 rounded">{{ $course->category->name }}</span>
                            </div>

                            <h3 class="text-lg font-semibold mb-2">About this course</h3>
                            <p class="text-gray-700 mb-4">{{ $course->description }}</p>

                            <div class="border-t pt-4">
                                <p class="text-sm text-gray-600">Teacher: <span class="font-semibold">{{ $course->teacher->name }}</span></p>
                            </div>
                        </div>
                    </div>

                    <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                        <div class="p-6">
                            <h3 class="text-lg font-semibold mb-4">Course Lessons</h3>

                            @if($course->lessons->isEmpty())
                                <p class="text-gray-500">No lessons added yet.</p>
                            @else
                                <div class="space-y-3">
                                    @foreach($course->lessons as $lesson)
                                        <div class="border rounded-lg p-4 hover:bg-gray-50 transition">
                                            <div class="flex justify-between items-start">
                                                <div class="flex-1">
                                                    <h4 class="font-semibold">{{ $lesson->order_number }}. {{ $lesson->title }}</h4>
                                                    <p class="text-sm text-gray-600 mt-1">Duration: {{ $lesson->duration }} minutes</p>
                                                </div>
                                                <div class="flex gap-2">
                                                    @if($isEnrolled || ($course->teacher_id === auth()->id()))
                                                        <a href="{{ route('lessons.show', $lesson) }}" class="bg-indigo-600 hover:bg-indigo-700 text-white px-3 py-1 rounded text-sm">
                                                            View
                                                        </a>
                                                    @endif
                                                    @if(auth()->user()->isTeacher() && $course->teacher_id === auth()->id())
                                                        <a href="{{ route('lessons.edit', $lesson) }}" class="bg-gray-600 hover:bg-gray-700 text-white px-3 py-1 rounded text-sm">
                                                            Edit
                                                        </a>
                                                        <form action="{{ route('lessons.destroy', $lesson) }}" method="POST" onsubmit="return confirm('Are you sure?')">
                                                            @csrf
                                                            @method('DELETE')
                                                            <button type="submit" class="bg-red-600 hover:bg-red-700 text-white px-3 py-1 rounded text-sm">
                                                                Delete
                                                            </button>
                                                        </form>
                                                    @endif
                                                </div>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            @endif
                        </div>
                    </div>
                </div>

                <div class="lg:col-span-1">
                    <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg sticky top-6">
                        <div class="p-6">
                            @if(auth()->user()->isStudent())
                                @if(!$isEnrolled)
                                    <form action="{{ route('courses.enroll', $course) }}" method="POST">
                                        @csrf
                                        <button type="submit" class="w-full bg-indigo-600 hover:bg-indigo-700 text-white px-4 py-3 rounded-lg font-semibold">
                                            Enroll Now
                                        </button>
                                    </form>
                                @else
                                    <div class="mb-4">
                                        <div class="flex justify-between text-sm mb-2">
                                            <span class="font-semibold">Your Progress</span>
                                            <span>{{ $enrollment->progress }}%</span>
                                        </div>
                                        <div class="w-full bg-gray-200 rounded-full h-3">
                                            <div class="bg-indigo-600 h-3 rounded-full" style="width: {{ $enrollment->progress }}%"></div>
                                        </div>
                                        @if($enrollment->is_completed)
                                            <p class="text-green-600 text-sm mt-2 font-semibold">Course Completed!</p>
                                        @endif
                                    </div>

                                    <p class="text-sm text-black mb-4">Progress updates automatically when you view lessons.</p>

                                    <span class="inline-block w-full text-center bg-white border border-black text-black px-4 py-2 rounded font-semibold">
                                        Enrolled
                                    </span>
                                @endif
                            @endif

                            @if(auth()->user()->isTeacher() && $course->teacher_id === auth()->id())
                                <div>
                                    <p class="text-black font-semibold mb-4 text-center">Student Progress</p>
                                    @if($course->enrollments->count() > 0)
                                        <div class="space-y-3">
                                            @foreach($course->enrollments as $enrollment)
                                                <div class="border border-black p-3 rounded">
                                                    <p class="text-sm font-semibold text-black">{{ $enrollment->user->name }}</p>
                                                    <div class="mt-2">
                                                        <div class="flex justify-between text-xs mb-1">
                                                            <span class="text-black">Progress</span>
                                                            <span class="text-black">{{ $enrollment->progress }}%</span>
                                                        </div>
                                                        <div class="w-full bg-white border border-black rounded-full h-2">
                                                            <div class="bg-black h-2 rounded-full" style="width: {{ $enrollment->progress }}%"></div>
                                                        </div>
                                                        @if($enrollment->is_completed)
                                                            <p class="text-xs text-black mt-1">✓ Completed</p>
                                                        @endif
                                                    </div>
                                                </div>
                                            @endforeach
                                        </div>
                                    @else
                                        <p class="text-sm text-black text-center">No students enrolled yet</p>
                                    @endif
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
