<div class="category-resources">
    <div style="background: white; padding: 20px; border: 1px solid #ddd; margin-bottom: 20px;">
        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 15px;">
            <h3 style="margin: 0; color: black;">{{ $categoryName }} Resources</h3>
            <button wire:click="refresh" style="padding: 5px 15px; background: #333; color: white; border: none; cursor: pointer;">
                Refresh
            </button>
        </div>

        @if($error)
            <div style="background: #fee; border: 1px solid #fcc; padding: 10px; margin-bottom: 15px; color: #c00;">
                {{ $error }}
            </div>
        @endif

        @if($loading)
            <div style="text-align: center; padding: 40px; color: #666;">
                Loading resources...
            </div>
        @else
            @if(count($resources) > 0)
                <div style="display: grid; grid-template-columns: repeat(auto-fill, minmax(200px, 1fr)); gap: 15px;">
                    @foreach($resources as $resource)
                        <div style="border: 1px solid #ddd; padding: 15px; background: #fafafa;">
                            @if(isset($resource['cover_id']) && $resource['cover_id'])
                                <img src="https://covers.openlibrary.org/b/id/{{ $resource['cover_id'] }}-M.jpg" 
                                     alt="{{ $resource['title'] }}" 
                                     style="width: 100%; height: 200px; object-fit: cover; margin-bottom: 10px;">
                            @else
                                <div style="width: 100%; height: 200px; background: #e0e0e0; display: flex; align-items: center; justify-content: center; margin-bottom: 10px; color: #666; font-size: 12px; text-align: center; padding: 10px;">
                                    {{ $resource['title'] }}
                                </div>
                            @endif
                            <h4 style="margin: 0 0 5px 0; font-size: 14px; color: black;">{{ $resource['title'] }}</h4>
                            <p style="margin: 0; font-size: 12px; color: #666;">
                                @if(isset($resource['authors']))
                                    {{ is_array($resource['authors']) ? implode(', ', $resource['authors']) : $resource['authors'] }}
                                @else
                                    Unknown Author
                                @endif
                            </p>
                            @if(isset($resource['first_publish_year']) && $resource['first_publish_year'])
                                <p style="margin: 5px 0 0 0; font-size: 11px; color: #999;">{{ $resource['first_publish_year'] }}</p>
                            @endif
                        </div>
                    @endforeach
                </div>
            @else
                <p style="color: #666; text-align: center; padding: 20px;">No resources available for this category.</p>
            @endif
        @endif
    </div>
</div>
