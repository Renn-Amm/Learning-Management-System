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
            ->with(['category', 'lessons', 'enrollments'])
            ->withCount('enrollments', 'lessons')
            ->get();
        
        $totalStudents = auth()->user()->courses()
            ->withCount('enrollments')
            ->get()
            ->sum('enrollments_count');
        
        $totalLessons = $courses->sum('lessons_count');
        
        $courseIds = $courses->pluck('id');
        $recentActivity = collect();
        
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
        
        $recentActivity = $recentActivity->sortByDesc('timestamp')->take(10)->values();
        
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
            ->with(['teacher', 'category', 'lessons'])
            ->get();
        
        $userEnrollments = Enrollment::where('user_id', auth()->id())
            ->whereIn('course_id', $enrolledCourses->pluck('id'))
            ->get()
            ->keyBy('course_id');
        
        $enrolledCoursesWithProgress = $enrolledCourses->map(function ($course) use ($userEnrollments) {
            $enrollment = $userEnrollments->get($course->id);
            $course->progress = $enrollment ? $enrollment->progress : 0;
            $course->enrollment_id = $enrollment ? $enrollment->id : null;
            
            $viewedLessonIds = $enrollment && $enrollment->viewed_lessons ? $enrollment->viewed_lessons : [];
            
            $nextLesson = $course->lessons
                ->whereNotIn('id', $viewedLessonIds)
                ->sortBy('created_at')
                ->first();
            
            if (!$nextLesson && $course->lessons->count() > 0) {
                $nextLesson = $course->lessons->sortBy('created_at')->first();
            }
            
            $course->next_lesson = $nextLesson;
            return $course;
        });
        
        $latestCourses = Course::where('is_published', true)
            ->whereNotIn('id', function ($query) {
                $query->select('course_id')
                    ->from('enrollments')
                    ->where('user_id', auth()->id());
            })
            ->with('teacher', 'category')
            ->latest('created_at')
            ->limit(6)
            ->get();
        
        $suggestedCourses = $latestCourses->groupBy('category_id')->map(function ($courses, $categoryId) {
            $category = Category::find($categoryId);
            $category->courses = $courses;
            return $category;
        })->values();
        
        $recentLessons = collect();
        foreach ($enrolledCoursesWithProgress as $course) {
            $enrollment = $userEnrollments->get($course->id);
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
        
        $completedCoursesCount = $userEnrollments->where('progress', '>=', 100)->count();
        
        $totalLessonsViewed = 0;
        foreach ($userEnrollments as $enrollment) {
            if ($enrollment->viewed_lessons) {
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
