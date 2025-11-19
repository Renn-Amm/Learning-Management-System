<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ $lesson->title }}
            </h2>
            <a href="{{ route('courses.show', $course) }}" class="text-indigo-600 hover:text-indigo-800">
                Back to Course
            </a>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6">
                    <div class="mb-4 pb-4 border-b">
                        <h3 class="text-2xl font-bold mb-2">{{ $lesson->title }}</h3>
                        <div class="flex items-center gap-4 text-sm text-gray-600">
                            <span>Lesson {{ $lesson->order_number }}</span>
                            <span>Duration: {{ $lesson->duration }} minutes</span>
                            <span>Course: {{ $course->title }}</span>
                        </div>
                    </div>

                    <div class="prose max-w-none">
                        <div class="text-gray-700 leading-relaxed whitespace-pre-wrap">{{ $lesson->content }}</div>
                    </div>

                    @if(auth()->user()->isTeacher() && $course->teacher_id === auth()->id())
                        <div class="mt-6 pt-6 border-t flex gap-3">
                            <a href="{{ route('lessons.edit', $lesson) }}" class="bg-gray-600 hover:bg-gray-700 text-white px-4 py-2 rounded">
                                Edit Lesson
                            </a>
                            <form action="{{ route('lessons.destroy', $lesson) }}" method="POST" onsubmit="return confirm('Are you sure you want to delete this lesson?')">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="bg-red-600 hover:bg-red-700 text-white px-4 py-2 rounded">
                                    Delete Lesson
                                </button>
                            </form>
                        </div>
                    @endif
                </div>
            </div>

            @php
                $lessons = $course->lessons;
                $currentIndex = $lessons->search(function($item) use ($lesson) {
                    return $item->id === $lesson->id;
                });
                $previousLesson = $currentIndex > 0 ? $lessons[$currentIndex - 1] : null;
                $nextLesson = $currentIndex < $lessons->count() - 1 ? $lessons[$currentIndex + 1] : null;
            @endphp

            @if($previousLesson || $nextLesson)
                <div class="mt-6 flex justify-between">
                    @if($previousLesson)
                        <a href="{{ route('lessons.show', $previousLesson) }}" class="bg-gray-600 hover:bg-gray-700 text-white px-6 py-3 rounded inline-flex items-center">
                            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
                            </svg>
                            Previous Lesson
                        </a>
                    @else
                        <div></div>
                    @endif

                    @if($nextLesson)
                        <a href="{{ route('lessons.show', $nextLesson) }}" class="bg-indigo-600 hover:bg-indigo-700 text-white px-6 py-3 rounded inline-flex items-center">
                            Next Lesson
                            <svg class="w-5 h-5 ml-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                            </svg>
                        </a>
                    @endif
                </div>
            @endif
        </div>
    </div>
</x-app-layout>
