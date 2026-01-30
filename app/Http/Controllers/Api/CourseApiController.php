<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Course;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class CourseApiController extends Controller
{
    public function index(Request $request)
    {
        $query = Course::with(['teacher:id,name,email', 'category:id,name', 'skills:id,name'])
            ->withCount('lessons', 'enrollments');

        if ($request->has('published')) {
            $query->where('is_published', $request->boolean('published'));
        }

        if ($request->has('category_id')) {
            $query->where('category_id', $request->category_id);
        }

        if ($request->has('level')) {
            $query->where('level', $request->level);
        }

        $perPage = $request->get('per_page', 15);
        $courses = $query->latest()->paginate($perPage);

        return response()->json([
            'success' => true,
            'data' => $courses->items(),
            'pagination' => [
                'total' => $courses->total(),
                'per_page' => $courses->perPage(),
                'current_page' => $courses->currentPage(),
                'last_page' => $courses->lastPage(),
            ],
        ], 200);
    }

    public function show(Course $course)
    {
        $course->load(['teacher:id,name,email', 'category:id,name', 'lessons', 'skills:id,name'])
            ->loadCount('enrollments');

        return response()->json([
            'success' => true,
            'data' => $course,
        ], 200);
    }

    public function store(Request $request)
    {
        if (!auth()->check() || !auth()->user()->isTeacher()) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized. Only teachers can create courses.',
            ], 403);
        }

        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'level' => 'required|in:beginner,intermediate,advanced',
            'category_id' => 'required|exists:categories,id',
            'thumbnail' => 'nullable|image|max:2048',
            'duration_hours' => 'nullable|integer|min:1',
        ]);

        $validated['teacher_id'] = auth()->id();

        if ($request->hasFile('thumbnail')) {
            $validated['thumbnail'] = $request->file('thumbnail')->store('thumbnails', 'public');
        }

        $course = Course::create($validated);

        return response()->json([
            'success' => true,
            'message' => 'Course created successfully',
            'data' => $course->load('teacher', 'category'),
        ], 201);
    }

    public function update(Request $request, Course $course)
    {
        if (!auth()->check() || !auth()->user()->isTeacher() || $course->teacher_id !== auth()->id()) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized. You can only update your own courses.',
            ], 403);
        }

        $validated = $request->validate([
            'title' => 'sometimes|required|string|max:255',
            'description' => 'sometimes|required|string',
            'level' => 'sometimes|required|in:beginner,intermediate,advanced',
            'category_id' => 'sometimes|required|exists:categories,id',
            'thumbnail' => 'nullable|image|max:2048',
            'duration_hours' => 'nullable|integer|min:1',
        ]);

        if ($request->hasFile('thumbnail')) {
            if ($course->thumbnail) {
                Storage::disk('public')->delete($course->thumbnail);
            }
            $validated['thumbnail'] = $request->file('thumbnail')->store('thumbnails', 'public');
        }

        $course->update($validated);

        return response()->json([
            'success' => true,
            'message' => 'Course updated successfully',
            'data' => $course->fresh(['teacher', 'category']),
        ], 200);
    }

    public function destroy(Course $course)
    {
        if (!auth()->check() || !auth()->user()->isTeacher() || $course->teacher_id !== auth()->id()) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized. You can only delete your own courses.',
            ], 403);
        }

        if ($course->thumbnail) {
            Storage::disk('public')->delete($course->thumbnail);
        }

        $course->delete();

        return response()->json([
            'success' => true,
            'message' => 'Course deleted successfully',
        ], 200);
    }
}
