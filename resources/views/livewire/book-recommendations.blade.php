<div style="padding: 20px; background: white; border: 1px solid #ddd;">
    <h3 style="margin-top: 0;">Book Recommendations</h3>
    
    <div style="margin-bottom: 20px;">
        <label style="display: block; margin-bottom: 5px; font-weight: bold;">
            Search by Subject:
        </label>
        <div style="display: flex; gap: 10px;">
            <input 
                type="text" 
                wire:model="subject" 
                placeholder="e.g., programming, mathematics, science"
                style="flex: 1; padding: 8px; border: 1px solid #ddd;"
            >
            <button 
                wire:click="loadBooks" 
                wire:loading.attr="disabled"
                style="padding: 8px 20px; background: #333; color: white; border: none; cursor: pointer;"
            >
                <span wire:loading.remove>Search</span>
                <span wire:loading>Searching...</span>
            </button>
        </div>
        @error('subject')
            <div style="color: red; font-size: 14px; margin-top: 5px;">{{ $message }}</div>
        @enderror
    </div>

    @if($loading)
        <div style="padding: 40px; text-align: center; background: #f9f9f9; border: 1px solid #ddd;">
            <strong>Loading book recommendations...</strong>
        </div>
    @elseif($error)
        <div style="padding: 20px; background: #fff3cd; border: 1px solid #ffc107; color: #856404;">
            <strong>Notice:</strong> {{ $error }}
        </div>
    @elseif($searchPerformed && empty($books))
        <div style="padding: 20px; text-align: center; background: #f9f9f9; border: 1px solid #ddd;">
            <p>No books found for "{{ $subject }}". Try a different subject.</p>
        </div>
    @elseif(!empty($books))
        <div style="margin-top: 20px;">
            <h4 style="margin-bottom: 15px;">Recommended Books on "{{ ucfirst($subject) }}":</h4>
            <div style="display: grid; grid-template-columns: repeat(auto-fill, minmax(250px, 1fr)); gap: 20px;">
                @foreach($books as $book)
                    <div style="border: 1px solid #ddd; padding: 15px; display: flex; flex-direction: column; background: #fff;">
                        @if(isset($book['cover_id']))
                            <div style="text-align: center; margin-bottom: 12px;">
                                <img 
                                    src="https://covers.openlibrary.org/b/id/{{ $book['cover_id'] }}-M.jpg" 
                                    alt="{{ $book['title'] ?? 'Book cover' }}"
                                    style="max-width: 100%; height: 180px; object-fit: contain;"
                                >
                            </div>
                        @endif
                        
                        <div style="flex: 1;">
                            <h5 style="margin: 0 0 8px 0; font-size: 15px; font-weight: bold;">
                                {{ $book['title'] ?? 'Untitled' }}
                            </h5>
                            
                            @if(isset($book['author_name']) && is_array($book['author_name']))
                                <p style="margin: 0 0 8px 0; color: #666; font-size: 13px;">
                                    <strong>By:</strong> 
                                    {{ implode(', ', array_slice($book['author_name'], 0, 2)) }}
                                </p>
                            @endif
                            
                            @if(isset($book['first_publish_year']))
                                <p style="margin: 0; color: #999; font-size: 12px;">
                                    {{ $book['first_publish_year'] }}
                                </p>
                            @endif
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    @else
        <div style="padding: 20px; text-align: center; background: #f9f9f9; border: 1px solid #ddd;">
            <p>Enter a subject above to search for book recommendations</p>
        </div>
    @endif
</div>
