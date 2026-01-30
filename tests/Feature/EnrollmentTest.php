<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\Course;
use App\Models\Enrollment;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Mail;
use Tests\TestCase;

class EnrollmentTest extends TestCase
{
    use RefreshDatabase;

    public function test_student_can_enroll_in_published_course(): void
    {
        Mail::fake();
        
        $student = User::factory()->create(['role' => 'student']);
        $course = Course::factory()->create(['is_published' => true]);

        $response = $this->actingAs($student)->post(route('courses.enroll', $course));

        $response->assertRedirect();
        $this->assertDatabaseHas('enrollments', [
            'user_id' => $student->id,
            'course_id' => $course->id,
            'progress' => 0,
        ]);
    }

    public function test_teacher_cannot_enroll_in_course(): void
    {
        $teacher = User::factory()->create(['role' => 'teacher']);
        $course = Course::factory()->create(['is_published' => true]);

        $response = $this->actingAs($teacher)->post(route('courses.enroll', $course));

        $response->assertStatus(403);
        $this->assertDatabaseMissing('enrollments', [
            'user_id' => $teacher->id,
            'course_id' => $course->id,
        ]);
    }

    public function test_student_cannot_enroll_twice_in_same_course(): void
    {
        $student = User::factory()->create(['role' => 'student']);
        $course = Course::factory()->create(['is_published' => true]);

        Enrollment::create([
            'user_id' => $student->id,
            'course_id' => $course->id,
            'progress' => 0,
            'is_completed' => false,
        ]);

        $response = $this->actingAs($student)->post(route('courses.enroll', $course));

        $response->assertRedirect();
        $this->assertEquals(1, Enrollment::where('user_id', $student->id)
            ->where('course_id', $course->id)
            ->count());
    }

    public function test_enrollment_sends_email_to_teacher(): void
    {
        Mail::fake();
        
        $student = User::factory()->create(['role' => 'student']);
        $teacher = User::factory()->create(['role' => 'teacher']);
        $course = Course::factory()->create([
            'teacher_id' => $teacher->id,
            'is_published' => true,
        ]);

        $this->actingAs($student)->post(route('courses.enroll', $course));

        // MWA2 REQUIREMENT: Emails are now queued, not sent immediately
        Mail::assertQueued(\App\Mail\StudentJoinedCourse::class, function ($mail) use ($teacher) {
            return $mail->hasTo($teacher->email);
        });
    }
}
