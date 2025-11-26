<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            All Courses
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            @if(session('success'))
                <div class="mb-4 bg-white border-2 border-black text-black px-4 py-3 rounded">
                    {{ session('success') }}
                </div>
            @endif

            <div class="bg-white overflow-hidden border border-black sm:rounded-lg mb-6">
                <div class="p-6">
                    <h3 class="font-semibold text-lg mb-4 text-black">Search Courses</h3>
                    <form method="GET" action="{{ route('courses.index') }}" class="flex gap-2">
                        <input 
                            type="text" 
                            name="search" 
                            placeholder="Search by title, description, category, or skills..."
                            value="{{ request('search') }}"
                            class="flex-1 border border-black rounded px-4 py-2 focus:outline-none focus:ring-2 focus:ring-black"
                        >
                        <button type="submit" class="bg-black hover:bg-white hover:text-black border-2 border-black text-white px-6 py-2 rounded font-semibold">
                            Search
                        </button>
                        @if(request('search'))
                            <a href="{{ route('courses.index') }}" class="bg-white hover:bg-black hover:text-white border-2 border-black text-black px-4 py-2 rounded font-semibold">
                                Clear
                            </a>
                        @endif
                    </form>
                </div>
            </div>

            <div class="bg-white overflow-hidden border border-black sm:rounded-lg mb-6">
                <div class="p-6">
                    <h3 class="font-semibold text-lg mb-4 text-black">Filter by Category</h3>
                    <div class="flex flex-wrap gap-2">
                        <a href="{{ route('courses.index', request()->only('search')) }}" class="px-4 py-2 rounded border {{ !request('category') ? 'bg-black text-white border-black' : 'bg-white text-black border-black hover:bg-black hover:text-white' }}">
                            All Courses
                        </a>
                        @foreach($categories as $category)
                            <a href="{{ route('courses.index', array_merge(request()->only('search'), ['category' => $category->id])) }}" class="px-4 py-2 rounded border {{ request('category') == $category->id ? 'bg-black text-white border-black' : 'bg-white text-black border-black hover:bg-black hover:text-white' }}">
                                {{ $category->name }}
                            </a>
                        @endforeach
                    </div>
                </div>
            </div>

            <div class="bg-white overflow-hidden border border-black sm:rounded-lg">
                <div class="p-6 text-black">
                    @if($courses->isEmpty())
                        <p class="text-black">No courses available in this category.</p>
                    @else
                        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                            @foreach($courses as $course)
                                <div class="border rounded-lg overflow-hidden hover:shadow-lg transition flex flex-col h-full">
                                    @if($course->thumbnail)
                                        <img src="{{ asset('storage/' . $course->thumbnail) }}" alt="{{ $course->title }}" class="w-full h-48 object-cover">
                                    @else
                                        {{-- Thumbnail fallback: Display category name --}}
                                        <div class="w-full h-48 bg-white border-2 border-black flex items-center justify-center">
                                            <div class="text-center px-4">
                                                <p class="text-2xl font-bold text-black">{{ $course->category->name }}</p>
                                                <p class="text-sm text-gray-600 mt-2">{{ $course->title }}</p>
                                            </div>
                                        </div>
                                    @endif
                                    
                                    <div class="p-4 flex flex-col flex-1">
                                        <div class="flex items-center gap-2 mb-2">
                                            <span class="text-xs px-2 py-1 bg-white border border-black text-black rounded">{{ ucfirst($course->level) }}</span>
                                            <span class="text-xs px-2 py-1 bg-white border border-black text-black rounded">{{ $course->category->name }}</span>
                                        </div>
                                        <h4 class="font-semibold text-lg mb-2">{{ $course->title }}</h4>
                                        <p class="text-sm text-black mb-2">{{ Str::limit($course->description, 100) }}</p>
                                        <p class="text-sm text-black mb-2">Teacher: {{ $course->teacher->name }}</p>
                                        
                                        @if($course->skills->count() > 0)
                                            <div class="flex flex-wrap gap-1 mb-3">
                                                @foreach($course->skills as $skill)
                                                    <span class="text-xs px-2 py-1 rounded" style="background-color: {{ $course->category->color_code }}; color: {{ $course->category->getTextColor() }};">
                                                        {{ $skill->name }}
                                                    </span>
                                                @endforeach
                                            </div>
                                        @endif
                                        
                                        <div class="flex items-center justify-between text-sm text-black mb-4">
                                            <span>{{ $course->lessons_count }} lessons</span>
                                            <span>{{ $course->enrollments_count }} students</span>
                                        </div>
                                        
                                        <div class="mt-auto">
                                        @if(auth()->user()->isStudent())
                                            @if(isset($course->is_enrolled) && $course->is_enrolled)
                                                <div class="mb-2">
                                                    <span class="inline-block bg-white border border-black text-black text-xs px-2 py-1 rounded font-semibold">✓ Enrolled</span>
                                                </div>
                                                <a href="{{ route('courses.show', $course) }}" class="block text-center bg-black hover:bg-white hover:text-black border border-black text-white px-4 py-2 rounded">
                                                    View Course
                                                </a>
                                            @else
                                                <div class="flex gap-2">
                                                    <a href="{{ route('courses.show', $course) }}" class="flex-1 text-center bg-white hover:bg-black hover:text-white border border-black text-black px-4 py-2 rounded">
                                                        View Details
                                                    </a>
                                                    <form action="{{ route('courses.enroll', $course) }}" method="POST" class="flex-1">
                                                        @csrf
                                                        <button type="submit" class="w-full bg-black hover:bg-white hover:text-black border border-black text-white px-4 py-2 rounded">
                                                            Enroll
                                                        </button>
                                                    </form>
                                                </div>
                                            @endif
                                        @else
                                            <a href="{{ route('courses.show', $course) }}" class="block text-center bg-black hover:bg-white hover:text-black border border-black text-white px-4 py-2 rounded">
                                                View Details
                                            </a>
                                        @endif
                                        </div>
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
