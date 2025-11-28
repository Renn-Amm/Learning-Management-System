<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ $lesson->title }}
            </h2>
            <a href="{{ route('courses.show', $course) }}" class="text-black hover:underline border border-black px-4 py-2 rounded bg-white hover:bg-black hover:text-white">
                Back to Course
            </a>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
            @if(session('success'))
                <div class="mb-4 bg-white border-2 border-black text-black px-4 py-3 rounded">
                    ✓ {{ session('success') }}
                </div>
            @endif

            @if(session('info'))
                <div class="mb-4 bg-white border border-black text-black px-4 py-3 rounded">
                    {{ session('info') }}
                </div>
            @endif

            <div class="bg-white overflow-hidden border border-black sm:rounded-lg">
                <div class="p-6">
                    <div class="mb-4 pb-4 border-b border-black">
                        <h3 class="text-2xl font-bold mb-2 text-black">{{ $lesson->title }}</h3>
                        <div class="flex items-center gap-4 text-sm text-black">
                            <span>Lesson {{ $lesson->order_number }}</span>
                            <span>Duration: {{ $lesson->duration }} minutes</span>
                            <span>Course: {{ $course->title }}</span>
                        </div>
                    </div>

                    <div class="prose max-w-none">
                        <div class="text-black leading-relaxed whitespace-pre-wrap">{{ $lesson->content }}</div>
                    </div>

                    @if($lesson->attachment)
                        <div class="mt-6 p-4 bg-white border border-black rounded">
                            <h4 class="font-semibold text-black mb-2">📎 Lesson Attachment:</h4>
                            <a href="{{ route('file.download', basename($lesson->attachment)) }}" class="inline-flex items-center bg-black hover:bg-white hover:text-black border border-black text-white px-4 py-2 rounded transition-colors">
                                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                                </svg>
                                Download: {{ $lesson->attachment_name ?? basename($lesson->attachment) }}
                            </a>
                            <p class="text-xs text-gray-600 mt-2">
                                File type: {{ strtoupper(pathinfo($lesson->attachment, PATHINFO_EXTENSION)) }}
                            </p>
                        </div>
                    @endif

                    @if(auth()->user()->isStudent() && $course->isEnrolledBy(auth()->id()))
                        <div class="mt-6 pt-6 border-t border-black">
                            @if($isCompleted)
                                <div class="flex items-center justify-between">
                                    <p class="text-black font-semibold">✓ You have completed this lesson</p>
                                    @if($enrollment)
                                        <p class="text-sm text-black">Progress: {{ $enrollment->progress }}%</p>
                                    @endif
                                </div>
                            @else
                                <form action="{{ route('lessons.markDone', $lesson) }}" method="POST">
                                    @csrf
                                    <button type="submit" class="w-full bg-black hover:bg-white hover:text-black border-2 border-black text-white px-6 py-3 rounded font-semibold">
                                        ✓ Mark as Done
                                    </button>
                                    <p class="text-sm text-black mt-2 text-center">Click when you've finished this lesson to update your progress</p>
                                </form>
                            @endif
                        </div>
                    @endif

                    @if(auth()->user()->isTeacher() && $course->teacher_id === auth()->id())
                        <div class="mt-6 pt-6 border-t border-black flex gap-3">
                            <a href="{{ route('lessons.edit', $lesson) }}" class="bg-white hover:bg-black hover:text-white border border-black text-black px-4 py-2 rounded">
                                Edit Lesson
                            </a>
                            <form action="{{ route('lessons.destroy', $lesson) }}" method="POST" onsubmit="return confirm('Are you sure you want to delete this lesson?')">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="bg-white hover:bg-black hover:text-white border border-black text-black px-4 py-2 rounded">
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
                        <a href="{{ route('lessons.show', $previousLesson) }}" class="bg-white hover:bg-black hover:text-white border border-black text-black px-6 py-3 rounded inline-flex items-center">
                            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
                            </svg>
                            Previous Lesson
                        </a>
                    @else
                        <div></div>
                    @endif

                    @if($nextLesson)
                        <a href="{{ route('lessons.show', $nextLesson) }}" class="bg-black hover:bg-white hover:text-black border border-black text-white px-6 py-3 rounded inline-flex items-center">
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
