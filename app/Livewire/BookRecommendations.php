<?php

namespace App\Livewire;

use App\Services\OpenLibraryService;
use Livewire\Component;

class BookRecommendations extends Component
{
    public $subject = '';
    public $books = [];
    public $loading = false;
    public $error = null;
    public $searchPerformed = false;

    protected $rules = [
        'subject' => 'required|string|min:2|max:50',
    ];

    public function mount()
    {
        // Don't load books automatically - wait for user to search
    }

    public function loadBooks()
    {
        $this->validate();
        
        $this->loading = true;
        $this->error = null;
        $this->searchPerformed = true;

        try {
            $service = app(OpenLibraryService::class);
            // Use searchBooks() method which exists in OpenLibraryService
            $result = $service->searchBooks($this->subject, 8);

            if ($result['success']) {
                $this->books = $result['data'];
                $this->error = null;
            } else {
                $this->books = [];
                $this->error = $result['message'] ?? 'Unable to fetch book recommendations';
            }
        } catch (\Exception $e) {
            $this->books = [];
            $this->error = 'An error occurred while fetching recommendations';
        } finally {
            $this->loading = false;
        }
    }

    public function updatedSubject()
    {
        $this->searchPerformed = false;
    }

    public function render()
    {
        return view('livewire.book-recommendations');
    }
}
