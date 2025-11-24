<?php

namespace App\Http\Controllers;

use App\Models\Course;
use App\Models\Category;
use App\Models\Skill;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class CourseController extends Controller
{
    public function index(Request $request)
    {
        $categories = Category::all();
        
        $query = Course::with('teacher', 'category', 'skills')
            ->withCount('lessons', 'enrollments');
        
        // Search functionality
        if ($request->filled('search')) {
            $searchTerm = $request->search;
            
            $query->where(function ($q) use ($searchTerm) {
                // Search in course title and description
                $q->where('title', 'LIKE', "%{$searchTerm}%")
                  ->orWhere('description', 'LIKE', "%{$searchTerm}%")
                  // Search in category name
                  ->orWhereHas('category', function ($q) use ($searchTerm) {
                      $q->where('name', 'LIKE', "%{$searchTerm}%");
                  })
                  // Search in skills
                  ->orWhereHas('skills', function ($q) use ($searchTerm) {
                      $q->where('name', 'LIKE', "%{$searchTerm}%");
                  });
            });
        }
        
        // Category filter
        if ($request->filled('category')) {
            $query->where('category_id', $request->category);
        }
        
        $courses = $query->latest()->paginate(12)->withQueryString();

        // Add enrollment status for students
        if (auth()->user()->isStudent()) {
            $enrolledCourseIds = auth()->user()->enrolledCourses()->pluck('courses.id')->toArray();
            $courses->getCollection()->transform(function ($course) use ($enrolledCourseIds) {
                $course->is_enrolled = in_array($course->id, $enrolledCourseIds);
                return $course;
            });
        }

        return view('courses.index', compact('courses', 'categories'));
    }

    public function create()
    {
        $categories = Category::all();
        $skills = Skill::all();
        
        if ($categories->isEmpty()) {
            return redirect()->route('dashboard')
                ->with('error', 'No categories available. Please create a category first.');
        }
        
        return view('courses.create', compact('categories', 'skills'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'level' => 'required|in:beginner,intermediate,advanced',
            'category_id' => 'required|exists:categories,id',
            'thumbnail' => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:2048',
            'skills' => 'nullable|string',
        ]);

        $validated['teacher_id'] = auth()->id();

        if ($request->hasFile('thumbnail')) {
            $validated['thumbnail'] = $request->file('thumbnail')->store('thumbnails', 'public');
        }

        unset($validated['skills']);
        $course = Course::create($validated);

        // Handle comma-separated skills
        if ($request->filled('skills')) {
            $skillNames = array_map('trim', explode(',', $request->skills));
            $skillIds = [];
            
            foreach ($skillNames as $skillName) {
                if (!empty($skillName)) {
                    $skill = Skill::firstOrCreate(['name' => $skillName]);
                    $skillIds[] = $skill->id;
                }
            }
            
            $course->skills()->sync($skillIds);
        }

        return redirect()->route('courses.show', $course)->with('success', 'Course created successfully.');
    }

    public function show(Course $course)
    {
        $course->load('teacher', 'category', 'lessons', 'skills');
        $isEnrolled = auth()->check() && $course->isEnrolledBy(auth()->id());
        $enrollment = null;

        if ($isEnrolled) {
            $enrollment = $course->enrollments()->where('user_id', auth()->id())->first();
        }

        return view('courses.show', compact('course', 'isEnrolled', 'enrollment'));
    }

    public function edit(Course $course)
    {
        if ($course->teacher_id !== auth()->id()) {
            abort(403, 'Unauthorized action.');
        }

        $categories = Category::all();
        $skills = Skill::all();
        return view('courses.edit', compact('course', 'categories', 'skills'));
    }

    public function update(Request $request, Course $course)
    {
        if ($course->teacher_id !== auth()->id()) {
            abort(403, 'Unauthorized action.');
        }

        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'level' => 'required|in:beginner,intermediate,advanced',
            'category_id' => 'required|exists:categories,id',
            'thumbnail' => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:2048',
            'skills' => 'nullable|string',
        ]);

        if ($request->hasFile('thumbnail')) {
            if ($course->thumbnail) {
                Storage::disk('public')->delete($course->thumbnail);
            }
            $validated['thumbnail'] = $request->file('thumbnail')->store('thumbnails', 'public');
        }

        unset($validated['skills']);
        $course->update($validated);

        // Handle comma-separated skills
        if ($request->filled('skills')) {
            $skillNames = array_map('trim', explode(',', $request->skills));
            $skillIds = [];
            
            foreach ($skillNames as $skillName) {
                if (!empty($skillName)) {
                    $skill = Skill::firstOrCreate(['name' => $skillName]);
                    $skillIds[] = $skill->id;
                }
            }
            
            $course->skills()->sync($skillIds);
        } else {
            $course->skills()->sync([]);
        }

        return redirect()->route('courses.show', $course)->with('success', 'Course updated successfully.');
    }

    public function destroy(Course $course)
    {
        if ($course->teacher_id !== auth()->id()) {
            abort(403, 'Unauthorized action.');
        }

        if ($course->thumbnail) {
            Storage::disk('public')->delete($course->thumbnail);
        }

        $course->delete();

        return redirect()->route('dashboard')->with('success', 'Course deleted successfully.');
    }
}
