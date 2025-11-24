<?php

namespace App\Http\Controllers;

use App\Models\Course;
use App\Models\Lesson;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class LessonController extends Controller
{
    public function index(Course $course)
    {
        $course->load('lessons');
        return view('lessons.index', compact('course'));
    }

    public function create(Course $course)
    {
        if ($course->teacher_id !== auth()->id()) {
            abort(403, 'Unauthorized action.');
        }

        return view('lessons.create', compact('course'));
    }

    public function store(Request $request, Course $course)
    {
        if ($course->teacher_id !== auth()->id()) {
            abort(403, 'Unauthorized action.');
        }

        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'content' => 'required|string',
            'attachment' => 'nullable|file|mimes:jpg,jpeg,png,gif,pdf,doc,docx|max:10240',
            'order_number' => 'required|integer|min:1',
            'duration' => 'required|integer|min:1',
        ]);

        // Check for duplicate order number in this course
        $existingLesson = $course->lessons()->where('order_number', $validated['order_number'])->first();
        if ($existingLesson) {
            return redirect()->back()->withErrors(['order_number' => 'This order number is already used in this course.'])->withInput();
        }

        // Handle file upload
        if ($request->hasFile('attachment')) {
            $validated['attachment'] = $request->file('attachment')->store('lesson-attachments', 'private');
        }

        $validated['course_id'] = $course->id;

        $lesson = Lesson::create($validated);

        return redirect()->route('courses.show', $course)->with('success', 'Lesson created successfully.');
    }

    public function show(Lesson $lesson)
    {
        $lesson->load('course.teacher');
        $course = $lesson->course;

        if (!$course->isEnrolledBy(auth()->id()) && $course->teacher_id !== auth()->id()) {
            abort(403, 'You must be enrolled in this course to view lessons.');
        }

        // Check if student has completed this lesson
        $isCompleted = false;
        $enrollment = null;
        if (auth()->user()->isStudent() && $course->isEnrolledBy(auth()->id())) {
            $enrollment = $course->enrollments()->where('user_id', auth()->id())->first();
            if ($enrollment) {
                $viewedLessons = $enrollment->viewed_lessons ?? [];
                $isCompleted = in_array($lesson->id, $viewedLessons);
            }
        }

        return view('lessons.show', compact('lesson', 'course', 'isCompleted', 'enrollment'));
    }

    public function markDone(Request $request, Lesson $lesson)
    {
        if (!auth()->user()->isStudent()) {
            abort(403, 'Only students can mark lessons as done.');
        }

        $course = $lesson->course;
        
        if (!$course->isEnrolledBy(auth()->id())) {
            abort(403, 'You must be enrolled in this course.');
        }

        $enrollment = $course->enrollments()->where('user_id', auth()->id())->first();
        
        if ($enrollment) {
            $viewedLessons = $enrollment->viewed_lessons ?? [];
            
            if (!in_array($lesson->id, $viewedLessons)) {
                $viewedLessons[] = $lesson->id;
                $totalLessons = $course->lessons()->count();
                $viewedCount = count($viewedLessons);
                $progress = $totalLessons > 0 ? round(($viewedCount / $totalLessons) * 100) : 0;
                $isCompleted = $viewedCount >= $totalLessons;
                
                $enrollment->update([
                    'viewed_lessons' => $viewedLessons,
                    'progress' => $progress,
                    'is_completed' => $isCompleted,
                ]);

                return redirect()->back()->with('success', 'Lesson marked as done! Progress updated.');
            }

            return redirect()->back()->with('info', 'You have already completed this lesson.');
        }

        return redirect()->back()->with('error', 'Enrollment not found.');
    }

    public function edit(Lesson $lesson)
    {
        if ($lesson->course->teacher_id !== auth()->id()) {
            abort(403, 'Unauthorized action.');
        }

        $course = $lesson->course;
        return view('lessons.edit', compact('lesson', 'course'));
    }

    public function update(Request $request, Lesson $lesson)
    {
        if ($lesson->course->teacher_id !== auth()->id()) {
            abort(403, 'Unauthorized action.');
        }

        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'content' => 'required|string',
            'attachment' => 'nullable|file|mimes:jpg,jpeg,png,gif,pdf,doc,docx|max:10240',
            'order_number' => 'required|integer|min:1',
            'duration' => 'required|integer|min:1',
        ]);

        // Check for duplicate order number in this course (excluding current lesson)
        $existingLesson = $lesson->course->lessons()
            ->where('order_number', $validated['order_number'])
            ->where('id', '!=', $lesson->id)
            ->first();
        
        if ($existingLesson) {
            return redirect()->back()->withErrors(['order_number' => 'This order number is already used in this course.'])->withInput();
        }

        // Handle file upload
        if ($request->hasFile('attachment')) {
            // Delete old file if exists
            if ($lesson->attachment) {
                Storage::disk('private')->delete($lesson->attachment);
            }
            $validated['attachment'] = $request->file('attachment')->store('lesson-attachments', 'private');
        }

        $lesson->update($validated);

        return redirect()->route('courses.show', $lesson->course)->with('success', 'Lesson updated successfully.');
    }

    public function destroy(Lesson $lesson)
    {
        if ($lesson->course->teacher_id !== auth()->id()) {
            abort(403, 'Unauthorized action.');
        }

        $course = $lesson->course;
        
        // Delete attachment file if exists
        if ($lesson->attachment) {
            Storage::disk('private')->delete($lesson->attachment);
        }
        
        $lesson->delete();

        return redirect()->route('courses.show', $course)->with('success', 'Lesson deleted successfully.');
    }
}
