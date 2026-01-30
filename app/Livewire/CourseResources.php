<?php

namespace App\Livewire;

use App\Services\OpenLibraryService;
use Livewire\Component;
use Illuminate\Support\Facades\Log;

class CourseResources extends Component
{
    public $courseTitle = '';
    public $categoryName = '';
    public $skills = [];
    
    public $books = [];
    public $loading = false;
    public $error = null;

    protected $rules = [
        'courseTitle' => 'nullable|string|max:255',
        'categoryName' => 'nullable|string|max:255',
    ];

    public function mount($courseTitle = '', $categoryName = '', $skills = [])
    {
        $this->courseTitle = $courseTitle;
        $this->categoryName = $categoryName;
        $this->skills = is_array($skills) ? $skills : [];
        
        $this->loadResources();
    }

    public function loadResources()
    {
        $this->loading = true;
        $this->error = null;
        
        try {
            $service = app(OpenLibraryService::class);
            
            if (!empty($this->categoryName)) {
                $result = $service->getResourcesByCategory($this->categoryName, 5);
            } elseif (!empty($this->skills)) {
                $result = $service->getResourcesBySkill($this->skills[0], 5);
            } else {
                $result = $service->searchBooks($this->courseTitle ?: 'programming', 5);
            }
            
            if ($result['success']) {
                $this->books = $result['data'];
            } else {
                $this->error = $result['message'] ?? 'Unable to load book recommendations at this time.';
            }
            
        } catch (\Exception $e) {
            $this->error = 'Unable to load educational resources. Please try again later.';
            Log::error('CourseResources component error', [
                'error' => $e->getMessage(),
                'course' => $this->courseTitle,
            ]);
        } finally {
            $this->loading = false;
        }
    }

    public function refresh()
    {
        $this->loadResources();
    }

    public function render()
    {
        return view('livewire.course-resources');
    }
}
