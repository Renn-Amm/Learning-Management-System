<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-black leading-tight">
                Student Dashboard
            </h2>
            <a href="{{ route('courses.index') }}" class="bg-black hover:bg-white hover:text-black border-2 border-black text-white px-4 py-2 rounded">
                Browse Courses
            </a>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            @if(session('success'))
                <div class="mb-4 bg-white border-2 border-black text-black px-4 py-3 rounded">
                    {{ session('success') }}
                </div>
            @endif

            {{-- Daily Motivational Quote --}}
            <div class="mb-6">
                @livewire('daily-quote')
            </div>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-6">
                <div class="bg-white border border-black rounded-lg p-6">
                    <h3 class="text-sm font-semibold text-black mb-2">Completed Courses</h3>
                    <p class="text-3xl font-bold text-black">{{ $completedCoursesCount }}</p>
                </div>
                <div class="bg-white border border-black rounded-lg p-6">
                    <h3 class="text-sm font-semibold text-black mb-2">Total Lessons Viewed</h3>
                    <p class="text-3xl font-bold text-black">{{ $totalLessonsViewed }}</p>
                </div>
                <div class="bg-white border border-black rounded-lg p-6">
                    <h3 class="text-sm font-semibold text-black mb-2">Enrolled Courses</h3>
                    <p class="text-3xl font-bold text-black">{{ $enrolledCoursesWithProgress->count() }}</p>
                </div>
            </div>

            <div class="bg-white overflow-hidden border border-black rounded-lg mb-6">
                <div class="p-6 text-black">
                    <h3 class="text-lg font-semibold mb-4">My Enrolled Courses</h3>

                    @if($enrolledCoursesWithProgress->isEmpty())
                        <p class="text-black">You haven't enrolled in any courses yet.</p>
                    @else
                        <div class="space-y-4">
                            @foreach($enrolledCoursesWithProgress as $course)
                                <div class="border border-black rounded-lg p-4">
                                    <div class="flex gap-4">
                                        @if($course->thumbnail)
                                            <img src="{{ asset('storage/' . $course->thumbnail) }}" alt="{{ $course->title }}" class="w-32 h-32 object-cover rounded">
                                        @else
                                            {{-- Thumbnail fallback: Display category name --}}
                                            <div class="w-32 h-32 bg-white border-2 border-black rounded flex items-center justify-center">
                                                <div class="text-center px-2">
                                                    <p class="text-sm font-bold text-black leading-tight">{{ $course->category->name }}</p>
                                                    <p class="text-xs text-gray-600 mt-1">{{ Str::limit($course->title, 30) }}</p>
                                                </div>
                                            </div>
                                        @endif
                                        
                                        <div class="flex-1">
                                            <h4 class="font-semibold text-lg mb-2 text-black">{{ $course->title }}</h4>
                                            <p class="text-sm text-black mb-2">Teacher: {{ $course->teacher->name }}</p>
                                            
                                            <div class="mb-2">
                                                <div class="flex justify-between text-sm mb-1 text-black">
                                                    <span>Progress</span>
                                                    <span>{{ $course->progress }}%</span>
                                                </div>
                                                <div class="w-full bg-white border border-black rounded-full h-2">
                                                    <div class="bg-black h-2 rounded-full" style="width: {{ $course->progress }}%"></div>
                                                </div>
                                            </div>
                                            
                                            @if($course->progress >= 100)
                                                <span class="inline-block bg-white border border-black text-black text-xs px-2 py-1 rounded font-semibold">Completed</span>
                                            @endif
                                            
                                            <div class="mt-3">
                                                @if($course->progress >= 100)
                                                    <a href="{{ route('courses.show', $course) }}" class="bg-black hover:bg-white hover:text-black border-2 border-black text-white px-4 py-2 rounded text-sm">
                                                        Review
                                                    </a>
                                                @elseif($course->next_lesson)
                                                    <a href="{{ route('lessons.show', $course->next_lesson) }}" class="bg-black hover:bg-white hover:text-black border-2 border-black text-white px-4 py-2 rounded text-sm">
                                                        Continue
                                                    </a>
                                                @else
                                                    <a href="{{ route('courses.show', $course) }}" class="bg-black hover:bg-white hover:text-black border-2 border-black text-white px-4 py-2 rounded text-sm">
                                                        View Course
                                                    </a>
                                                @endif
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @endif
                </div>
            </div>

            <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-6">
                <div class="bg-white border border-black rounded-lg p-6">
                    <h3 class="text-lg font-semibold text-black mb-4">Recent Lessons Viewed</h3>
                    @if($recentLessons->isEmpty())
                        <p class="text-black text-sm">No lessons viewed yet.</p>
                    @else
                        <div class="space-y-2">
                            @foreach($recentLessons as $lesson)
                                <div class="border-b border-gray-200 pb-2">
                                    <p class="text-sm text-black font-semibold">{{ $lesson->title }}</p>
                                    <p class="text-xs text-gray-600">{{ $lesson->course->title }}</p>
                                </div>
                            @endforeach
                        </div>
                    @endif
                </div>

                <div class="bg-white border border-black rounded-lg p-6">
                    <h3 class="text-lg font-semibold text-black mb-4">Achievements</h3>
                    <div class="space-y-2 text-black">
                        <p class="text-sm"><span class="font-semibold">Completed Courses:</span> {{ $completedCoursesCount }}</p>
                        <p class="text-sm"><span class="font-semibold">Total Lessons Viewed:</span> {{ $totalLessonsViewed }}</p>
                    </div>
                </div>
            </div>

            <div class="bg-white overflow-hidden border border-black rounded-lg">
                <div class="p-6 text-black">
                    <h3 class="text-lg font-semibold mb-4">New Courses</h3>

                    @if($suggestedCourses->isEmpty())
                        <p class="text-black">No new courses available.</p>
                    @else
                        @foreach($suggestedCourses as $category)
                            <div class="mb-6">
                                <h4 class="font-semibold text-black mb-3">{{ $category->name }}</h4>
                                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                    @foreach($category->courses as $course)
                                        <div class="border border-black rounded-lg p-4">
                                            <h5 class="font-semibold text-black mb-2">{{ $course->title }}</h5>
                                            <p class="text-sm text-black mb-2">{{ Str::limit($course->description, 80) }}</p>
                                            <p class="text-xs text-black mb-3">Teacher: {{ $course->teacher->name }}</p>
                                            <a href="{{ route('courses.show', $course) }}" class="text-sm bg-white hover:bg-black hover:text-white border-2 border-black text-black px-3 py-1 rounded">
                                                View Details
                                            </a>
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                        @endforeach
                    @endif
                </div>
            </div>

            {{-- Skills API Integration: Student Skill Tracker --}}
            <div class="mt-6">
                @livewire('student-skill-tracker')
            </div>

            {{-- Recommended Books for Learning --}}
            <div class="bg-white border border-black rounded-lg p-6 mt-6">
                <h3 class="text-lg font-semibold text-black mb-4">Recommended Books</h3>
                <p class="text-sm text-gray-600 mb-4">Discover books to enhance your learning journey</p>
                @livewire('book-recommendations')
            </div>
        </div>
    </div>
</x-app-layout>
