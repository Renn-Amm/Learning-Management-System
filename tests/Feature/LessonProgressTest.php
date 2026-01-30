<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\Course;
use App\Models\Lesson;
use App\Models\Enrollment;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class LessonProgressTest extends TestCase
{
    use RefreshDatabase;

    public function test_student_progress_updates_when_viewing_lesson(): void
    {
        $student = User::factory()->create(['role' => 'student']);
        $course = Course::factory()->create(['is_published' => true]);
        
        $lesson1 = Lesson::factory()->create(['course_id' => $course->id, 'order_number' => 1]);
        $lesson2 = Lesson::factory()->create(['course_id' => $course->id, 'order_number' => 2]);

        $enrollment = Enrollment::create([
            'user_id' => $student->id,
            'course_id' => $course->id,
            'progress' => 0,
            'is_completed' => false,
        ]);

        $this->actingAs($student)->post(route('lessons.markDone', $lesson1));

        $enrollment->refresh();
        $this->assertEquals(50, $enrollment->progress);
        $this->assertContains($lesson1->id, $enrollment->viewed_lessons);
    }

    public function test_course_completion_when_all_lessons_viewed(): void
    {
        $student = User::factory()->create(['role' => 'student']);
        $course = Course::factory()->create(['is_published' => true]);
        
        $lesson1 = Lesson::factory()->create(['course_id' => $course->id, 'order_number' => 1]);
        $lesson2 = Lesson::factory()->create(['course_id' => $course->id, 'order_number' => 2]);

        $enrollment = Enrollment::create([
            'user_id' => $student->id,
            'course_id' => $course->id,
            'progress' => 0,
            'is_completed' => false,
        ]);

        $this->actingAs($student)->post(route('lessons.markDone', $lesson1));
        $this->actingAs($student)->post(route('lessons.markDone', $lesson2));

        $enrollment->refresh();
        $this->assertEquals(100, $enrollment->progress);
        $this->assertTrue($enrollment->is_completed);
    }

    public function test_unenrolled_student_cannot_mark_lesson_done(): void
    {
        $student = User::factory()->create(['role' => 'student']);
        $course = Course::factory()->create(['is_published' => true]);
        $lesson = Lesson::factory()->create(['course_id' => $course->id]);

        $response = $this->actingAs($student)->post(route('lessons.markDone', $lesson));

        $response->assertStatus(403);
        $this->assertDatabaseMissing('enrollments', [
            'user_id' => $student->id,
            'course_id' => $course->id,
        ]);
    }
}
