<?php

namespace App\Policies;

use App\Models\Lesson;
use App\Models\User;

class LessonPolicy
{
    /**
     * Determine if the user can view the lesson.
     * Students can view if enrolled in the course, teachers can view their own.
     */
    public function view(User $user, Lesson $lesson): bool
    {
        $course = $lesson->course;
        
        // Teacher who owns the course can view
        if ($user->isTeacher() && $course->teacher_id === $user->id) {
            return true;
        }
        
        // Student who is enrolled can view
        if ($user->isStudent() && $course->isEnrolledBy($user->id)) {
            return true;
        }
        
        return false;
    }

    /**
     * Determine if the user can create lessons.
     * Only teachers can create lessons for their courses.
     */
    public function create(User $user, $course): bool
    {
        return $user->isTeacher() && $user->id === $course->teacher_id;
    }

    /**
     * Determine if the user can update the lesson.
     * Only the teacher who owns the course can update its lessons.
     */
    public function update(User $user, Lesson $lesson): bool
    {
        return $user->isTeacher() && $user->id === $lesson->course->teacher_id;
    }

    /**
     * Determine if the user can delete the lesson.
     * Only the teacher who owns the course can delete its lessons.
     */
    public function delete(User $user, Lesson $lesson): bool
    {
        return $user->isTeacher() && $user->id === $lesson->course->teacher_id;
    }

    /**
     * Determine if the user can mark the lesson as done.
     * Only students enrolled in the course can mark lessons as done.
     */
    public function markDone(User $user, Lesson $lesson): bool
    {
        return $user->isStudent() && $lesson->course->isEnrolledBy($user->id);
    }

    /**
     * Determine if the user can download lesson files.
     * Students enrolled in the course or the teacher who owns it can download.
     */
    public function downloadFile(User $user, Lesson $lesson): bool
    {
        $course = $lesson->course;
        
        // Teacher who owns the course can download
        if ($user->isTeacher() && $course->teacher_id === $user->id) {
            return true;
        }
        
        // Student who is enrolled can download
        if ($user->isStudent() && $course->isEnrolledBy($user->id)) {
            return true;
        }
        
        return false;
    }
}
