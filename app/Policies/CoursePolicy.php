<?php

namespace App\Policies;

use App\Models\Course;
use App\Models\User;

class CoursePolicy
{
    /**
     * Determine if the user can view any courses.
     */
    public function viewAny(User $user): bool
    {
        return true; // All authenticated users can view courses
    }

    /**
     * Determine if the user can view the course.
     */
    public function view(User $user, Course $course): bool
    {
        return true; // All authenticated users can view course details
    }

    /**
     * Determine if the user can create courses.
     * Only teachers can create courses.
     */
    public function create(User $user): bool
    {
        return $user->isTeacher();
    }

    /**
     * Determine if the user can update the course.
     * Only the teacher who owns the course can update it.
     */
    public function update(User $user, Course $course): bool
    {
        return $user->isTeacher() && $user->id === $course->teacher_id;
    }

    /**
     * Determine if the user can delete the course.
     * Only the teacher who owns the course can delete it.
     */
    public function delete(User $user, Course $course): bool
    {
        return $user->isTeacher() && $user->id === $course->teacher_id;
    }

    /**
     * Determine if the user can publish/unpublish the course.
     * Only the teacher who owns the course can publish/unpublish it.
     */
    public function publish(User $user, Course $course): bool
    {
        return $user->isTeacher() && $user->id === $course->teacher_id;
    }

    /**
     * Determine if the user can enroll in the course.
     * Only students can enroll in courses.
     */
    public function enroll(User $user, Course $course): bool
    {
        return $user->isStudent();
    }
}
