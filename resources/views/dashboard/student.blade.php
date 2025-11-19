<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                Student Dashboard
            </h2>
            <a href="{{ route('courses.index') }}" class="bg-black hover:bg-white hover:text-black border border-black text-white px-4 py-2 rounded-md text-sm font-medium">
                Browse Courses
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

            <div class="bg-white overflow-hidden border border-black sm:rounded-lg">
                <div class="p-6 text-black">
                    <h3 class="text-lg font-semibold mb-4">My Enrolled Courses</h3>

                    @if($enrolledCourses->isEmpty())
                        <p class="text-black">You haven't enrolled in any courses yet.</p>
                    @else
                        <div class="space-y-4">
                            @foreach($enrolledCourses as $course)
                                <div class="border rounded-lg p-4 hover:shadow-md transition">
                                    <div class="flex gap-4">
                                        @if($course->thumbnail)
                                            <img src="{{ asset('storage/' . $course->thumbnail) }}" alt="{{ $course->title }}" class="w-32 h-32 object-cover rounded">
                                        @else
                                            <div class="w-32 h-32 bg-white border border-black rounded flex items-center justify-center">
                                                <span class="text-black text-xs">No image</span>
                                            </div>
                                        @endif
                                        
                                        <div class="flex-1">
                                            <h4 class="font-semibold text-lg mb-2">{{ $course->title }}</h4>
                                            <p class="text-sm text-black mb-2">Teacher: {{ $course->teacher->name }}</p>
                                            <p class="text-sm text-black mb-2">Category: {{ $course->category->name }}</p>
                                            
                                            <div class="mb-2">
                                                <div class="flex justify-between text-sm mb-1">
                                                    <span>Progress</span>
                                                    <span>{{ $course->pivot->progress }}%</span>
                                                </div>
                                                <div class="w-full bg-white border border-black rounded-full h-2">
                                                    <div class="bg-black h-2 rounded-full" style="width: {{ $course->pivot->progress }}%"></div>
                                                </div>
                                            </div>
                                            
                                            @if($course->pivot->is_completed)
                                                <span class="inline-block bg-white border border-black text-black text-xs px-2 py-1 rounded font-semibold">✓ Completed</span>
                                            @endif
                                            
                                            <div class="mt-3">
                                                <a href="{{ route('courses.show', $course) }}" class="bg-black hover:bg-white hover:text-black border border-black text-white px-4 py-2 rounded text-sm">
                                                    Continue Learning
                                                </a>
                                            </div>
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
