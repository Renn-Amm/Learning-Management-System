<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                Skill: {{ $skill->name }}
            </h2>
            <div class="flex gap-2">
                <a href="{{ route('skills.index') }}" class="bg-white hover:bg-black hover:text-white border border-black text-black px-4 py-2 rounded-md text-sm">
                    Back to Skills
                </a>
                <a href="{{ route('skills.edit', $skill) }}" class="bg-black hover:bg-white hover:text-black border border-black text-white px-4 py-2 rounded-md text-sm">
                    Edit Skill
                </a>
            </div>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden border border-black sm:rounded-lg">
                <div class="p-6">
                    <h3 class="font-semibold text-lg mb-4 text-black">Skill Details</h3>
                    
                    <div class="mb-6">
                        <p class="text-sm text-gray-600 mb-2">Skill Name:</p>
                        <p class="text-2xl font-bold text-black">{{ $skill->name }}</p>
                    </div>

                    <div class="mb-6">
                        <h4 class="font-semibold text-lg mb-3 text-black">Courses using this skill ({{ $skill->courses->count() }})</h4>
                        @if($skill->courses->isEmpty())
                            <p class="text-gray-600">This skill is not used in any courses yet.</p>
                        @else
                            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                                @foreach($skill->courses as $course)
                                    <div class="border border-black rounded-lg p-4 hover:shadow-lg transition">
                                        <h5 class="font-semibold text-black mb-2">{{ $course->title }}</h5>
                                        <p class="text-sm text-gray-600 mb-2">{{ $course->category->name }}</p>
                                        <p class="text-sm text-gray-600 mb-3">By {{ $course->teacher->name }}</p>
                                        <a href="{{ route('courses.show', $course) }}" class="text-sm bg-black hover:bg-white hover:text-black border border-black text-white px-3 py-1 rounded">
                                            View Course
                                        </a>
                                    </div>
                                @endforeach
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
