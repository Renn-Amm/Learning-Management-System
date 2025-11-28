<?php

namespace App\Http\Controllers;

use App\Mail\StudentJoinedCourse;
use App\Models\Course;
use App\Models\Enrollment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;

class EnrollmentController extends Controller
{
    public function enroll(Course $course)
    {
        $user = auth()->user();

        if ($user->isTeacher()) {
            return redirect()->back()->with('error', 'Teachers cannot enroll in courses.');
        }

        if ($course->isEnrolledBy($user->id)) {
            return redirect()->back()->with('error', 'You are already enrolled in this course.');
        }

        Enrollment::create([
            'user_id' => $user->id,
            'course_id' => $course->id,
            'progress' => 0,
            'is_completed' => false,
        ]);

        try {
            $course->load('teacher');
            Mail::to($course->teacher->email)->send(new StudentJoinedCourse($user, $course));
            \Log::info('Enrollment email sent', [
                'teacher' => $course->teacher->email,
                'student' => $user->name,
                'course' => $course->title
            ]);
        } catch (\Exception $e) {
            \Log::error('Failed to send enrollment email', [
                'error' => $e->getMessage(),
                'teacher' => $course->teacher->email ?? 'N/A'
            ]);
        }

        return redirect()->route('courses.show', $course)->with('success', 'Successfully enrolled in the course.');
    }

}
