<div>
    @if($loading)
        <div class="text-sm text-gray-600">Loading skills...</div>
    @else
        @if(!empty($learnedSkills))
            <div class="bg-white border border-black rounded-lg p-4 mb-4">
                <h3 class="text-lg font-semibold text-black mb-3">Your Skills</h3>
                <div class="flex flex-wrap gap-2">
                    @foreach($learnedSkills as $skill)
                        <span class="px-3 py-1 bg-green-100 text-green-800 rounded text-sm border border-green-300">
                            {{ $skill }}
                        </span>
                    @endforeach
                </div>
            </div>

            @if(!empty($relatedSkills))
                <div class="bg-white border border-gray-300 rounded-lg p-4">
                    <h4 class="text-sm font-semibold text-gray-700 mb-2">Recommended Skills to Learn Next</h4>
                    <p class="text-xs text-gray-600 mb-3">Based on your current skills, consider learning these related skills</p>
                    <div class="flex flex-wrap gap-2">
                        @foreach($relatedSkills as $skill)
                            <span class="px-3 py-1 bg-blue-50 text-blue-700 rounded text-sm border border-blue-200">
                                {{ $skill }}
                            </span>
                        @endforeach
                    </div>
                </div>
            @endif
        @else
            <div class="bg-gray-50 border border-gray-300 rounded-lg p-4 text-center">
                <p class="text-sm text-gray-600">Enroll in courses to start building your skill set</p>
            </div>
        @endif
    @endif
</div>
