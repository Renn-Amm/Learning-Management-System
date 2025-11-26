<?php

namespace App\Http\Controllers;

use App\Models\Course;
use App\Models\Lesson;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class LessonController extends Controller
{
    use AuthorizesRequests;
    public function index(Course $course)
    {
        $course->load('lessons');
        return view('lessons.index', compact('course'));
    }

    public function create(Course $course)
    {
        // Authorization: Only course owner can create lessons
        $this->authorize('create', [Lesson::class, $course]);

        return view('lessons.create', compact('course'));
    }

    public function store(Request $request, Course $course)
    {
        // Authorization: Only course owner can add lessons
        $this->authorize('create', [Lesson::class, $course]);

        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'content' => 'required|string',
            'attachment' => 'nullable|file|mimes:jpg,jpeg,png,gif,pdf,doc,docx|max:10240',
            'attachment_name' => 'nullable|string|max:255',
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

    /**
     * Display a lesson with authorization check.
     * Students can view if enrolled, teachers if they own the course.
     */
    public function show(Lesson $lesson)
    {
        // Authorization: Check if user can view this lesson
        $this->authorize('view', $lesson);

        // Eager load course and teacher to prevent N+1 queries
        $lesson->load('course.teacher');
        $course = $lesson->course;

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

    /**
     * Mark a lesson as done (non-CRUD action for students).
     * Updates enrollment progress and viewed_lessons array.
     */
    public function markDone(Request $request, Lesson $lesson)
    {
        // Authorization: Only enrolled students can mark lessons as done
        $this->authorize('markDone', $lesson);

        $course = $lesson->course;

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
        // Authorization: Only course owner can edit lessons
        $this->authorize('update', $lesson);

        $course = $lesson->course;
        return view('lessons.edit', compact('lesson', 'course'));
    }

    public function update(Request $request, Lesson $lesson)
    {
        // Authorization: Only course owner can update lessons
        $this->authorize('update', $lesson);

        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'content' => 'required|string',
            'attachment' => 'nullable|file|mimes:jpg,jpeg,png,gif,pdf,doc,docx|max:10240',
            'attachment_name' => 'nullable|string|max:255',
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
        // Authorization: Only course owner can delete lessons
        $this->authorize('delete', $lesson);

        $course = $lesson->course;
        
        // Delete attachment file if exists (private file handling)
        if ($lesson->attachment) {
            Storage::disk('private')->delete($lesson->attachment);
        }
        
        $lesson->delete();

        return redirect()->route('courses.show', $course)->with('success', 'Lesson deleted successfully.');
    }
}
