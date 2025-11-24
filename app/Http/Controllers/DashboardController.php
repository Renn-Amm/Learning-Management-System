<?php

namespace App\Http\Controllers;

use App\Models\Course;
use App\Models\Enrollment;
use App\Models\Category;
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
        $teacherId = auth()->id();
        
        $courses = auth()->user()->courses()
            ->with('category', 'lessons')
            ->withCount('enrollments', 'lessons')
            ->get();
        
        $totalStudents = auth()->user()->courses()
            ->withCount('enrollments')
            ->get()
            ->sum('enrollments_count');
        
        $totalLessons = $courses->sum('lessons_count');
        
        // Recent Student Activity (enrollments and progress updates)
        $courseIds = $courses->pluck('id');
        $recentActivity = collect();
        
        // Get recent enrollments
        $recentEnrollments = Enrollment::whereIn('course_id', $courseIds)
            ->with(['user', 'course'])
            ->orderBy('created_at', 'desc')
            ->limit(10)
            ->get();
        
        foreach ($recentEnrollments as $enrollment) {
            if ($enrollment->is_completed) {
                $recentActivity->push([
                    'message' => "{$enrollment->user->name} completed {$enrollment->course->title}",
                    'time' => $enrollment->updated_at->diffForHumans(),
                    'timestamp' => $enrollment->updated_at,
                ]);
            } else {
                $recentActivity->push([
                    'message' => "{$enrollment->user->name} enrolled in {$enrollment->course->title}",
                    'time' => $enrollment->created_at->diffForHumans(),
                    'timestamp' => $enrollment->created_at,
                ]);
            }
        }
        
        // Sort by timestamp and limit to 10
        $recentActivity = $recentActivity->sortByDesc('timestamp')->take(10)->values();
        
        // Student Progress Summary
        $progressSummary = collect();
        foreach ($courses as $course) {
            $enrollments = $course->enrollments;
            $enrolledCount = $enrollments->count();
            
            if ($enrolledCount > 0) {
                $totalProgress = 0;
                $completedCount = 0;
                
                foreach ($enrollments as $enrollment) {
                    $totalProgress += $enrollment->progress;
                    if ($enrollment->progress >= 100) {
                        $completedCount++;
                    }
                }
                
                $avgProgress = round($totalProgress / $enrolledCount);
                
                $progressSummary->push([
                    'course_title' => $course->title,
                    'enrolled_count' => $enrolledCount,
                    'avg_progress' => $avgProgress,
                    'completed_count' => $completedCount,
                ]);
            }
        }
        
        return view('dashboard.teacher', compact('courses', 'totalStudents', 'totalLessons', 'recentActivity', 'progressSummary'));
    }

    private function studentDashboard()
    {
        $enrolledCourses = auth()->user()->enrolledCourses()
            ->with('teacher', 'category', 'lessons')
            ->get();
        
        // Get enrollment data with progress
        $enrolledCoursesWithProgress = $enrolledCourses->map(function ($course) {
            $enrollment = $course->enrollments()->where('user_id', auth()->id())->first();
            $course->progress = $enrollment ? $enrollment->progress : 0;
            $course->enrollment_id = $enrollment ? $enrollment->id : null;
            
            // Find next unfinished lesson using viewed_lessons from enrollment
            $viewedLessonIds = $enrollment && $enrollment->viewed_lessons ? $enrollment->viewed_lessons : [];
            
            $nextLesson = $course->lessons()
                ->whereNotIn('id', $viewedLessonIds)
                ->orderBy('created_at')
                ->first();
            
            // If all lessons viewed, get first lesson
            if (!$nextLesson && $course->lessons->count() > 0) {
                $nextLesson = $course->lessons()->orderBy('created_at')->first();
            }
            
            $course->next_lesson = $nextLesson;
            return $course;
        });
        
        // Suggested courses grouped by category
        $allCategories = Category::with(['courses' => function ($q) {
            $q->whereNotIn('id', function ($query) {
                $query->select('course_id')
                    ->from('enrollments')
                    ->where('user_id', auth()->id());
            })
            ->with('teacher', 'category')
            ->limit(2);
        }])->get();
        
        $suggestedCourses = $allCategories->filter(function ($category) {
            return $category->courses->count() > 0;
        });
        
        // Recent lessons viewed - get from enrollments
        $recentLessons = collect();
        foreach ($enrolledCoursesWithProgress as $course) {
            $enrollment = $course->enrollments()->where('user_id', auth()->id())->first();
            if ($enrollment && $enrollment->viewed_lessons) {
                foreach ($enrollment->viewed_lessons as $lessonId) {
                    $lesson = $course->lessons->where('id', $lessonId)->first();
                    if ($lesson) {
                        $lesson->course = $course;
                        $recentLessons->push($lesson);
                    }
                }
            }
        }
        $recentLessons = $recentLessons->take(5);
        
        // Achievements
        $completedCoursesCount = auth()->user()->enrolledCourses()
            ->whereHas('enrollments', function ($q) {
                $q->where('user_id', auth()->id())
                  ->where('progress', '>=', 100);
            })
            ->count();
        
        $totalLessonsViewed = 0;
        foreach ($enrolledCourses as $course) {
            $enrollment = $course->enrollments()->where('user_id', auth()->id())->first();
            if ($enrollment && $enrollment->viewed_lessons) {
                $totalLessonsViewed += count($enrollment->viewed_lessons);
            }
        }
        
        return view('dashboard.student', compact(
            'enrolledCoursesWithProgress',
            'suggestedCourses',
            'recentLessons',
            'completedCoursesCount',
            'totalLessonsViewed'
        ));
    }
}
