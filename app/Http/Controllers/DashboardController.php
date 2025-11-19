<?php

namespace App\Http\Controllers;

use App\Models\Course;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index()
    {
        $user = auth()->user();

        if ($user->isTeacher()) {
            return $this->teacherDashboard();
        }

        return $this->studentDashboard();
    }

    private function teacherDashboard()
    {
        $courses = auth()->user()->courses()
            ->with('category', 'lessons')
            ->withCount('enrollments', 'lessons')
            ->get();
        
        $totalStudents = auth()->user()->courses()
            ->withCount('enrollments')
            ->get()
            ->sum('enrollments_count');
        
        $totalLessons = $courses->sum('lessons_count');
        
        return view('dashboard.teacher', compact('courses', 'totalStudents', 'totalLessons'));
    }

    private function studentDashboard()
    {
        $enrolledCourses = auth()->user()->enrolledCourses()
            ->with('teacher', 'category')
            ->get();

        return view('dashboard.student', compact('enrolledCourses'));
    }
}
