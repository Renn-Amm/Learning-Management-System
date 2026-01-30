<?php

namespace App\Livewire;

use App\Services\OpenLibraryService;
use Livewire\Component;
use Illuminate\Support\Facades\Log;

class CategoryResources extends Component
{
    public $categoryName = '';
    public $limit = 10;
    
    public $resources = [];
    public $loading = false;
    public $error = null;

    protected $rules = [
        'categoryName' => 'required|string|max:255',
        'limit' => 'integer|min:1|max:50',
    ];

    public function mount($categoryName, $limit = 10)
    {
        $this->categoryName = $categoryName;
        $this->limit = $limit;
        
        $this->loadResources();
    }

    public function loadResources()
    {
        $this->loading = true;
        $this->error = null;
        
        try {
            $service = app(OpenLibraryService::class);
            $result = $service->getResourcesByCategory($this->categoryName, $this->limit);
            
            if ($result['success']) {
                $this->resources = $result['data'];
            } else {
                $this->error = $result['message'] ?? 'Unable to load resources for this category.';
            }
            
        } catch (\Exception $e) {
            $this->error = 'Unable to load category resources. Please try again later.';
            Log::error('CategoryResources component error', [
                'error' => $e->getMessage(),
                'category' => $this->categoryName,
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
        return view('livewire.category-resources');
    }
}
