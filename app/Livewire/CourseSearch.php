<?php

namespace App\Livewire;

use App\Models\Course;
use App\Models\Category;
use Livewire\Component;
use Livewire\WithPagination;

class CourseSearch extends Component
{
    use WithPagination;

    public $search = '';
    public $categoryFilter = '';
    public $levelFilter = '';
    public $sortBy = 'latest';

    protected $queryString = [
        'search' => ['except' => ''],
        'categoryFilter' => ['except' => ''],
        'levelFilter' => ['except' => ''],
    ];

    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function updatingCategoryFilter()
    {
        $this->resetPage();
    }

    public function updatingLevelFilter()
    {
        $this->resetPage();
    }

    public function clearFilters()
    {
        $this->reset(['search', 'categoryFilter', 'levelFilter']);
        $this->resetPage();
    }

    public function render()
    {
        $query = Course::with(['teacher', 'category', 'skills'])
            ->withCount('lessons', 'enrollments');

        if (auth()->user()->isStudent()) {
            $query->where('is_published', true);
        }

        if ($this->search) {
            $query->where(function ($q) {
                $q->where('title', 'LIKE', "%{$this->search}%")
                  ->orWhere('description', 'LIKE', "%{$this->search}%")
                  ->orWhereHas('category', function ($q) {
                      $q->where('name', 'LIKE', "%{$this->search}%");
                  })
                  ->orWhereHas('skills', function ($q) {
                      $q->where('name', 'LIKE', "%{$this->search}%");
                  });
            });
        }

        if ($this->categoryFilter) {
            $query->where('category_id', $this->categoryFilter);
        }

        if ($this->levelFilter) {
            $query->where('level', $this->levelFilter);
        }

        switch ($this->sortBy) {
            case 'popular':
                $query->orderBy('enrollments_count', 'desc');
                break;
            case 'title':
                $query->orderBy('title', 'asc');
                break;
            default:
                $query->latest();
        }

        $courses = $query->paginate(12);

        if (auth()->user()->isStudent()) {
            $enrolledCourseIds = auth()->user()->enrolledCourses()->pluck('courses.id')->toArray();
            $courses->getCollection()->transform(function ($course) use ($enrolledCourseIds) {
                $course->is_enrolled = in_array($course->id, $enrolledCourseIds);
                return $course;
            });
        }

        $categories = Category::all();

        return view('livewire.course-search', [
            'courses' => $courses,
            'categories' => $categories,
        ]);
    }
}
