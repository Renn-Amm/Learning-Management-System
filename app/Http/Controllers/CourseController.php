<?php

namespace App\Http\Controllers;

use App\Models\Course;
use App\Models\Category;
use App\Models\Skill;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class CourseController extends Controller
{
    use AuthorizesRequests;
    public function index(Request $request)
    {
        $categories = Category::all();
        
        $query = Course::with(['teacher', 'category', 'skills'])
            ->withCount('lessons', 'enrollments');
        
        // Students see only published courses
        if (auth()->user()->isStudent()) {
            $query->where('is_published', true);
        }
        
        if ($request->filled('search')) {
            $searchTerm = $request->search;
            
            $query->where(function ($q) use ($searchTerm) {
                $q->where('title', 'LIKE', "%{$searchTerm}%")
                  ->orWhere('description', 'LIKE', "%{$searchTerm}%")
                  ->orWhereHas('category', function ($q) use ($searchTerm) {
                      $q->where('name', 'LIKE', "%{$searchTerm}%");
                  })
                  ->orWhereHas('skills', function ($q) use ($searchTerm) {
                      $q->where('name', 'LIKE', "%{$searchTerm}%");
                  });
            });
        }
        
        if ($request->filled('category')) {
            $query->where('category_id', $request->category);
        }
        
        $courses = $query->latest()->paginate(12)->withQueryString();

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
        $this->authorize('create', Course::class);

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
            'skills' => 'required|string|min:2',
        ]);

        $validated['teacher_id'] = auth()->id();

        if ($request->hasFile('thumbnail')) {
            $validated['thumbnail'] = $request->file('thumbnail')->store('thumbnails', 'public');
        }

        unset($validated['skills']);
        $course = Course::create($validated);

        if ($request->filled('skills')) {
            $skillNames = array_map('trim', explode(',', $request->skills));
            $skillIds = [];
            
            $category = Category::find($validated['category_id']);
            $colorCode = $category->color_code;
            
            foreach ($skillNames as $skillName) {
                if (!empty($skillName)) {
                    $skill = Skill::firstOrCreate(
                        ['name' => $skillName],
                        ['color_code' => $colorCode]
                    );
                    $skillIds[] = $skill->id;
                }
            }
            
            $course->skills()->sync($skillIds);
        }

        return redirect()->route('courses.show', $course)->with('success', 'Course created successfully.');
    }

    public function show(Course $course)
    {
        $course->load(['teacher', 'category', 'lessons', 'skills']);
        $isEnrolled = auth()->check() && $course->isEnrolledBy(auth()->id());
        $enrollment = null;

        if ($isEnrolled) {
            $enrollment = $course->enrollments()->where('user_id', auth()->id())->first();
        }

        return view('courses.show', compact('course', 'isEnrolled', 'enrollment'));
    }

    public function edit(Course $course)
    {
        $this->authorize('update', $course);

        $categories = Category::all();
        $skills = Skill::all();
        return view('courses.edit', compact('course', 'categories', 'skills'));
    }

    public function update(Request $request, Course $course)
    {
        $this->authorize('update', $course);

        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'level' => 'required|in:beginner,intermediate,advanced',
            'category_id' => 'required|exists:categories,id',
            'thumbnail' => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:2048',
            'skills' => 'required|string|min:2',
        ]);

        if ($request->hasFile('thumbnail')) {
            if ($course->thumbnail) {
                Storage::disk('public')->delete($course->thumbnail);
            }
            $validated['thumbnail'] = $request->file('thumbnail')->store('thumbnails', 'public');
        }

        unset($validated['skills']);
        $course->update($validated);

        if ($request->filled('skills')) {
            $skillNames = array_map('trim', explode(',', $request->skills));
            $skillIds = [];
            
            $category = Category::find($validated['category_id']);
            $colorCode = $category->color_code;
            
            foreach ($skillNames as $skillName) {
                if (!empty($skillName)) {
                    $skill = Skill::firstOrCreate(
                        ['name' => $skillName],
                        ['color_code' => $colorCode]
                    );
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
        $this->authorize('delete', $course);

        if ($course->thumbnail) {
            Storage::disk('public')->delete($course->thumbnail);
        }

        $course->delete();

        return redirect()->route('dashboard')->with('success', 'Course deleted successfully.');
    }

    // Publish course - makes visible to students
    public function publish(Course $course)
    {
        $this->authorize('publish', $course);

        if ($course->is_published) {
            return redirect()->back()->with('error', 'Course is already published.');
        }

        $course->update(['is_published' => true]);

        return redirect()->back()->with('success', 'Course published successfully. Students can now enroll.');
    }

    // Unpublish course - hides from students, keeps existing enrollments
    public function unpublish(Course $course)
    {
        $this->authorize('publish', $course);

        if (!$course->is_published) {
            return redirect()->back()->with('error', 'Course is already unpublished.');
        }

        $course->update(['is_published' => false]);

        return redirect()->back()->with('success', 'Course unpublished. No new students can enroll, but existing enrollments remain active.');
    }
}
