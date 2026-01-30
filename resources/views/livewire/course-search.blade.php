<div>
    <div style="margin-bottom: 20px; padding: 20px; background: white; border: 1px solid #ddd;">
        <div style="display: grid; grid-template-columns: 1fr 1fr 1fr auto; gap: 10px; margin-bottom: 15px;">
            <div>
                <input 
                    type="text" 
                    wire:model.live.debounce.300ms="search" 
                    placeholder="Search courses..."
                    style="width: 100%; padding: 8px; border: 1px solid #ddd;"
                >
            </div>
            
            <div>
                <select wire:model.live="categoryFilter" style="width: 100%; padding: 8px; border: 1px solid #ddd;">
                    <option value="">All Categories</option>
                    @foreach($categories as $category)
                        <option value="{{ $category->id }}">{{ $category->name }}</option>
                    @endforeach
                </select>
            </div>
            
            <div>
                <select wire:model.live="levelFilter" style="width: 100%; padding: 8px; border: 1px solid #ddd;">
                    <option value="">All Levels</option>
                    <option value="beginner">Beginner</option>
                    <option value="intermediate">Intermediate</option>
                    <option value="advanced">Advanced</option>
                </select>
            </div>
            
            <div>
                <button wire:click="clearFilters" style="padding: 8px 16px; background: #333; color: white; border: none; cursor: pointer;">
                    Clear Filters
                </button>
            </div>
        </div>
        
        <div style="display: flex; gap: 10px; align-items: center;">
            <span style="font-weight: bold;">Sort by:</span>
            <label style="cursor: pointer;">
                <input type="radio" wire:model.live="sortBy" value="latest"> Latest
            </label>
            <label style="cursor: pointer;">
                <input type="radio" wire:model.live="sortBy" value="popular"> Most Popular
            </label>
            <label style="cursor: pointer;">
                <input type="radio" wire:model.live="sortBy" value="title"> Title A-Z
            </label>
        </div>
    </div>

    <div wire:loading style="padding: 20px; text-align: center; background: #f9f9f9; border: 1px solid #ddd; margin-bottom: 20px;">
        <strong>Loading courses...</strong>
    </div>

    @if($courses->isEmpty())
        <div style="padding: 40px; text-align: center; background: white; border: 1px solid #ddd;">
            <p style="font-size: 18px; color: #666;">No courses found matching your criteria.</p>
        </div>
    @else
        <div style="display: grid; grid-template-columns: repeat(auto-fill, minmax(280px, 1fr)); gap: 20px; margin-bottom: 30px;">
            @foreach($courses as $course)
                <div style="border: 1px solid #ddd; padding: 15px; background: white;">
                    @if($course->thumbnail)
                        <img src="{{ asset('storage/' . $course->thumbnail) }}" alt="{{ $course->title }}" style="width: 100%; height: 150px; object-fit: cover; margin-bottom: 10px;">
                    @endif
                    
                    <h3 style="margin: 0 0 10px 0; font-size: 18px;">
                        <a href="{{ route('courses.show', $course) }}" style="color: black; text-decoration: none;">
                            {{ $course->title }}
                        </a>
                    </h3>
                    
                    <p style="color: #666; font-size: 14px; margin-bottom: 10px;">
                        {{ Str::limit($course->description, 100) }}
                    </p>
                    
                    <div style="margin-bottom: 10px;">
                        <span style="background: #f0f0f0; padding: 4px 8px; font-size: 12px; margin-right: 5px;">
                            {{ $course->category->name }}
                        </span>
                        <span style="background: #f0f0f0; padding: 4px 8px; font-size: 12px;">
                            {{ ucfirst($course->level) }}
                        </span>
                    </div>
                    
                    <div style="font-size: 13px; color: #666;">
                        <div>{{ $course->lessons_count }} lessons</div>
                        <div>{{ $course->enrollments_count }} students</div>
                    </div>
                </div>
            @endforeach
        </div>

        <div style="margin-top: 20px;">
            {{ $courses->links() }}
        </div>
    @endif
</div>
