<div>
    <div class="bg-gray-50 border border-gray-300 rounded-md p-4">
        <h4 class="text-sm font-semibold text-gray-700 mb-2">Skill Suggestions</h4>
        <p class="text-xs text-gray-600 mb-3">Search for industry-standard skills to add to your course</p>
        
        <div class="mb-3">
            <input 
                type="text" 
                wire:model.live.debounce.500ms="searchQuery"
                placeholder="Search skills (e.g., Laravel, React, Python)..."
                class="w-full text-sm border-gray-300 rounded-md shadow-sm focus:border-blue-500 focus:ring-blue-500"
            />
        </div>

        <div wire:loading wire:target="searchSkills" class="text-sm text-gray-600 mb-2">
            Searching skills...
        </div>

        @if(!empty($suggestions))
            <div class="space-y-2 max-h-48 overflow-y-auto">
                @foreach($suggestions as $skill)
                    <div class="bg-white border border-gray-200 rounded p-2 hover:bg-gray-50 transition">
                        <div class="flex justify-between items-start">
                            <div class="flex-1">
                                <h5 class="text-sm font-medium text-gray-900">{{ $skill['name'] }}</h5>
                                @if(!empty($skill['description']))
                                    <p class="text-xs text-gray-600 mt-1">{{ Str::limit($skill['description'], 100) }}</p>
                                @endif
                                <div class="flex gap-2 mt-1">
                                    <span class="text-xs text-gray-500">{{ $skill['type'] }}</span>
                                    <span class="text-xs text-gray-400">|</span>
                                    <span class="text-xs text-gray-500">{{ $skill['category'] }}</span>
                                </div>
                            </div>
                            <button 
                                type="button"
                                data-skill-name="{{ $skill['name'] }}"
                                onclick="addSkillToInput(this.dataset.skillName)"
                                class="ml-2 text-xs bg-blue-500 hover:bg-blue-600 text-white px-3 py-1 rounded"
                            >
                                Add
                            </button>
                        </div>
                    </div>
                @endforeach
            </div>
        @elseif(!empty($searchQuery) && !$loading && empty($suggestions))
            <p class="text-sm text-gray-500">No skills found. Try a different search term.</p>
        @endif
    </div>

    <script>
        function addSkillToInput(skillName) {
            const skillsInput = document.getElementById('skills');
            const currentValue = skillsInput.value.trim();
            
            // Check if skill already exists
            const skills = currentValue.split(',').map(s => s.trim().toLowerCase());
            if (skills.includes(skillName.toLowerCase())) {
                return; // Skill already added
            }
            
            // Add skill
            if (currentValue === '') {
                skillsInput.value = skillName;
            } else {
                skillsInput.value = currentValue + ', ' + skillName;
            }
            
            // Trigger change event
            skillsInput.dispatchEvent(new Event('input'));
        }
    </script>
</div>
