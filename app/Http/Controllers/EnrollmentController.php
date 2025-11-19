<?php

namespace App\Http\Controllers;

use App\Models\Course;
use App\Models\Enrollment;
use Illuminate\Http\Request;

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

        return redirect()->route('courses.show', $course)->with('success', 'Successfully enrolled in the course.');
    }

}
