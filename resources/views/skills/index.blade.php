<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-black leading-tight">
                Skills
            </h2>
            <a href="{{ route('skills.create') }}" class="bg-black text-white px-4 py-2 rounded border border-black hover:bg-white hover:text-black">
                Create Skill
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

            <div class="bg-white overflow-hidden border border-black sm:rounded-lg">
                <div class="p-6">
                    @if($skills->isEmpty())
                        <p class="text-black">No skills created yet.</p>
                    @else
                        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                            @foreach($skills as $skill)
                                <div class="p-4 border border-black rounded">
                                    <div class="flex items-center justify-between mb-2">
                                        <span class="px-3 py-1 rounded text-sm" style="background-color: {{ $skill->color_code }}; color: {{ $skill->getTextColor() }};">
                                            {{ $skill->name }}
                                        </span>
                                    </div>
                                    <p class="text-xs text-black mb-3">Color: {{ $skill->color_code }}</p>
                                    <div class="flex gap-2">
                                        <a href="{{ route('skills.edit', $skill) }}" class="text-xs bg-white text-black px-3 py-1 rounded border border-black hover:bg-black hover:text-white">
                                            Edit
                                        </a>
                                        <form action="{{ route('skills.destroy', $skill) }}" method="POST" onsubmit="return confirm('Are you sure?')">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="text-xs bg-white text-black px-3 py-1 rounded border border-black hover:bg-black hover:text-white">
                                                Delete
                                            </button>
                                        </form>
                                    </div>
                                </div>
                            @endforeach
                        </div>

                        <div class="mt-6">
                            {{ $skills->links() }}
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
