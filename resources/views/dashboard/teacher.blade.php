<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-black leading-tight">
                Teacher Dashboard
            </h2>
            <a href="{{ route('courses.create') }}" class="bg-black hover:bg-white hover:text-black border-2 border-black text-white px-4 py-2 rounded">
                Create New Course
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

            <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-6">
                <div class="bg-white border border-black rounded-lg p-6">
                    <h3 class="text-lg font-semibold text-black mb-4">Recent Student Activity</h3>
                    @if($recentActivity->isEmpty())
                        <p class="text-black text-sm">No recent activity yet.</p>
                    @else
                        <div class="space-y-3">
                            @foreach($recentActivity as $activity)
                                <div class="border-b border-gray-200 pb-2">
                                    <p class="text-sm text-black">{{ $activity['message'] }}</p>
                                    <p class="text-xs text-gray-600">{{ $activity['time'] }}</p>
                                </div>
                            @endforeach
                        </div>
                    @endif
                </div>

                <div class="bg-white border border-black rounded-lg p-6">
                    <h3 class="text-lg font-semibold text-black mb-4">Student Progress Summary</h3>
                    @if($progressSummary->isEmpty())
                        <p class="text-black text-sm">No courses with enrolled students yet.</p>
                    @else
                        <div class="space-y-3">
                            @foreach($progressSummary as $summary)
                                <div class="border-b border-gray-200 pb-2">
                                    <p class="text-sm font-semibold text-black">{{ $summary['course_title'] }}</p>
                                    <p class="text-xs text-black">Enrolled: {{ $summary['enrolled_count'] }} students</p>
                                    <p class="text-xs text-black">Avg Progress: {{ $summary['avg_progress'] }}%</p>
                                    <p class="text-xs text-black">Completed: {{ $summary['completed_count'] }} students</p>
                                </div>
                            @endforeach
                        </div>
                    @endif
                </div>
            </div>

            <div class="bg-white overflow-hidden border border-black rounded-lg">
                <div class="p-6 text-black">
                    <h3 class="text-lg font-semibold mb-4">My Courses</h3>

                    @if($courses->isEmpty())
                        <p class="text-black">You haven't created any courses yet.</p>
                    @else
                        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                            @foreach($courses as $course)
                                <div class="border border-black rounded-lg overflow-hidden">
                                    @if($course->thumbnail)
                                        <img src="{{ asset('storage/' . $course->thumbnail) }}" alt="{{ $course->title }}" class="w-full h-48 object-cover">
                                    @else
                                        <div class="w-full h-48 bg-white border-b border-black flex items-center justify-center">
                                            <span class="text-black">No thumbnail</span>
                                        </div>
                                    @endif
                                    
                                    <div class="p-4">
                                        <h4 class="font-semibold text-lg mb-2 text-black">{{ $course->title }}</h4>
                                        <p class="text-sm text-black mb-2">{{ Str::limit($course->description, 100) }}</p>
                                        <div class="flex items-center justify-between text-sm text-black mb-4">
                                            <span>{{ $course->lessons_count }} lessons</span>
                                            <span>{{ $course->enrollments_count }} students</span>
                                        </div>
                                        <div class="flex gap-2">
                                            <a href="{{ route('courses.show', $course) }}" class="flex-1 text-center bg-black hover:bg-white hover:text-black border-2 border-black text-white px-3 py-2 rounded text-sm">
                                                Manage
                                            </a>
                                            <a href="{{ route('courses.edit', $course) }}" class="flex-1 text-center bg-white hover:bg-black hover:text-white border-2 border-black text-black px-3 py-2 rounded text-sm">
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
