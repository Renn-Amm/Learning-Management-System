<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <div class="flex items-center gap-4">
                @if(auth()->user()->isStudent())
                    <a href="{{ route('courses.index') }}" class="text-black hover:underline flex items-center">
                        <svg class="w-5 h-5 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
                        </svg>
                        Back to Courses
                    </a>
                @endif
                <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                    {{ $course->title }}
                </h2>
            </div>
            @if(auth()->user()->isTeacher() && $course->teacher_id === auth()->id())
                <div class="flex gap-2">
                    <a href="{{ route('courses.index') }}" class="bg-white hover:bg-black hover:text-white border border-black text-black px-4 py-2 rounded-md text-sm">
                        Back to Courses
                    </a>
                    <a href="{{ route('lessons.create', $course) }}" class="bg-black hover:bg-white hover:text-black border border-black text-white px-4 py-2 rounded-md text-sm">
                        Add Lesson
                    </a>
                    <a href="{{ route('courses.edit', $course) }}" class="bg-white hover:bg-black hover:text-white border border-black text-black px-4 py-2 rounded-md text-sm">
                        Edit Course
                    </a>
                    <form action="{{ route('courses.destroy', $course) }}" method="POST" onsubmit="return confirm('Are you sure you want to delete this course?')">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="bg-white hover:bg-black hover:text-white border border-black text-black px-4 py-2 rounded-md text-sm">
                            Delete Course
                        </button>
                    </form>
                </div>
            @elseif(auth()->user()->isTeacher())
                <a href="{{ route('courses.index') }}" class="text-black hover:underline">
                    Back to Courses
                </a>
            @endif
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            @if(session('success'))
                <div class="mb-4 bg-white border-2 border-black text-black px-4 py-3 rounded">
                    ✓ {{ session('success') }}
                </div>
            @endif

            @if(session('error'))
                <div class="mb-4 bg-white border-2 border-black text-black px-4 py-3 rounded">
                    ✗ {{ session('error') }}
                </div>
            @endif

            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                <div class="lg:col-span-2">
                    <div class="bg-white overflow-hidden border border-black sm:rounded-lg mb-6">
                        <div class="p-6">
                            @if($course->thumbnail)
                                <img src="{{ asset('storage/' . $course->thumbnail) }}" alt="{{ $course->title }}" class="w-full h-64 object-cover rounded mb-4">
                            @endif

                            <div class="flex items-center gap-2 mb-4">
                                <span class="text-sm px-3 py-1 bg-white border border-black text-black rounded">{{ ucfirst($course->level) }}</span>
                                <span class="text-sm px-3 py-1 bg-white border border-black text-black rounded">{{ $course->category->name }}</span>
                            </div>

                            @if($course->skills->isNotEmpty())
                                <div class="mb-4">
                                    <p class="text-sm text-black font-semibold mb-2">Skills you'll learn:</p>
                                    <div class="flex flex-wrap gap-2">
                                        @foreach($course->skills as $skill)
                                            <span class="px-3 py-1 rounded text-sm" style="background-color: {{ $course->category->color_code }}; color: {{ $course->category->getTextColor() }};">
                                                {{ $skill->name }}
                                            </span>
                                        @endforeach
                                    </div>
                                </div>
                            @endif

                            <h3 class="text-lg font-semibold mb-2 text-black">About this course</h3>
                            <p class="text-black mb-4">{{ $course->description }}</p>

                            <div class="border-t border-black pt-4 mb-4">
                                <p class="text-sm text-black">Teacher: <span class="font-semibold">{{ $course->teacher->name }}</span></p>
                                <p class="text-sm text-black mt-2">Total Duration: <span class="font-semibold">{{ $course->lessons->sum('duration') }} minutes</span></p>
                            </div>
                        </div>
                    </div>

                    <div class="bg-white overflow-hidden border border-black sm:rounded-lg">
                        <div class="p-6">
                            @if(auth()->user()->isStudent() && !$isEnrolled)
                                <h3 class="text-lg font-semibold mb-4 text-black">Lessons Overview</h3>
                                @if($course->lessons->isEmpty())
                                    <p class="text-black">No lessons added yet.</p>
                                @else
                                    <div class="space-y-2">
                                        @foreach($course->lessons as $lesson)
                                            <div class="border border-black rounded-lg p-3">
                                                <div class="flex justify-between items-center">
                                                    <h4 class="font-semibold text-black">{{ $lesson->order_number }}. {{ $lesson->title }}</h4>
                                                    <span class="text-sm text-black">{{ $lesson->duration }} min</span>
                                                </div>
                                            </div>
                                        @endforeach
                                    </div>
                                    <p class="text-sm text-black mt-4 italic">Enroll to access full lesson content</p>
                                @endif
                            @else
                                <h3 class="text-lg font-semibold mb-4 text-black">Course Lessons</h3>
                                @if($course->lessons->isEmpty())
                                    <p class="text-black">No lessons added yet.</p>
                                @else
                                    <div class="space-y-3">
                                        @foreach($course->lessons as $lesson)
                                            <div class="border border-black rounded-lg p-4 hover:bg-gray-50 transition">
                                                <div class="flex justify-between items-start">
                                                    <div class="flex-1">
                                                        <h4 class="font-semibold text-black">{{ $lesson->order_number }}. {{ $lesson->title }}</h4>
                                                        <p class="text-sm text-black mt-1">Duration: {{ $lesson->duration }} minutes</p>
                                                    </div>
                                                    <div class="flex gap-2">
                                                        @if($isEnrolled || ($course->teacher_id === auth()->id()))
                                                            <a href="{{ route('lessons.show', $lesson) }}" class="bg-black hover:bg-white hover:text-black border border-black text-white px-3 py-1 rounded text-sm">
                                                                View
                                                            </a>
                                                        @endif
                                                        @if(auth()->user()->isTeacher() && $course->teacher_id === auth()->id())
                                                            <a href="{{ route('lessons.edit', $lesson) }}" class="bg-white hover:bg-black hover:text-white border border-black text-black px-3 py-1 rounded text-sm">
                                                                Edit
                                                            </a>
                                                            <form action="{{ route('lessons.destroy', $lesson) }}" method="POST" onsubmit="return confirm('Are you sure?')">
                                                                @csrf
                                                                @method('DELETE')
                                                                <button type="submit" class="bg-white hover:bg-black hover:text-white border border-black text-black px-3 py-1 rounded text-sm">
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
                            @endif
                        </div>
                    </div>
                </div>

                <div class="lg:col-span-1">
                    <div class="bg-white overflow-hidden border border-black sm:rounded-lg sticky top-6">
                        <div class="p-6">
                            @if(auth()->user()->isStudent())
                                @if(!$isEnrolled)
                                    <h3 class="text-lg font-semibold mb-4 text-black text-center">Enroll to Access</h3>
                                    <form action="{{ route('courses.enroll', $course) }}" method="POST">
                                        @csrf
                                        <button type="submit" class="w-full bg-black hover:bg-white hover:text-black border border-black text-white px-4 py-3 rounded-lg font-semibold">
                                            Enroll Now
                                        </button>
                                    </form>
                                @else
                                    <div class="mb-4">
                                        <div class="flex justify-between text-sm mb-2 text-black">
                                            <span class="font-semibold">Your Progress</span>
                                            <span>{{ $enrollment->progress }}%</span>
                                        </div>
                                        <div class="w-full bg-white border border-black rounded-full h-3">
                                            <div class="bg-black h-3 rounded-full" style="width: {{ $enrollment->progress }}%"></div>
                                        </div>
                                        @if($enrollment->is_completed)
                                            <p class="text-black text-sm mt-2 font-semibold">✓ Course Completed!</p>
                                        @endif
                                    </div>

                                    <p class="text-sm text-black mb-4">Progress updates automatically when you view lessons.</p>

                                    <span class="inline-block w-full text-center bg-white border-2 border-black text-black px-4 py-2 rounded font-semibold">
                                        ✓ Enrolled
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
