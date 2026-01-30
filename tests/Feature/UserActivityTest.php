<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\Course;
use App\Models\Lesson;
use App\Models\User;
use App\Models\UserActivity;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * MWA2 REQUIREMENT: Testing for Usage Tracking (Challenging Level)
 * 
 * Tests that user activities are properly tracked for analytics
 */
class UserActivityTest extends TestCase
{
    use RefreshDatabase;

    public function test_enrollment_creates_activity_log(): void
    {
        $student = User::factory()->create(['role' => 'student']);
        $teacher = User::factory()->create(['role' => 'teacher']);
        $category = Category::factory()->create(['user_id' => $teacher->id]);
        $course = Course::factory()->create([
            'teacher_id' => $teacher->id,
            'category_id' => $category->id,
            'is_published' => true,
        ]);

        $this->actingAs($student);
        $this->post(route('courses.enroll', $course));

        $this->assertDatabaseHas('user_activities', [
            'user_id' => $student->id,
            'action' => UserActivity::ACTION_COURSE_ENROLLED,
            'trackable_type' => Course::class,
            'trackable_id' => $course->id,
        ]);
    }

    public function test_lesson_view_creates_activity_log(): void
    {
        $student = User::factory()->create(['role' => 'student']);
        $teacher = User::factory()->create(['role' => 'teacher']);
        $category = Category::factory()->create(['user_id' => $teacher->id]);
        $course = Course::factory()->create([
            'teacher_id' => $teacher->id,
            'category_id' => $category->id,
            'is_published' => true,
        ]);
        $lesson = Lesson::factory()->create([
            'course_id' => $course->id,
            'order_number' => 1,
        ]);

        $this->actingAs($student);
        $this->post(route('courses.enroll', $course));
        $this->post(route('lessons.markDone', $lesson));

        $this->assertDatabaseHas('user_activities', [
            'user_id' => $student->id,
            'action' => UserActivity::ACTION_LESSON_VIEWED,
            'trackable_type' => Lesson::class,
            'trackable_id' => $lesson->id,
        ]);
    }

    public function test_course_completion_creates_activity_log(): void
    {
        $student = User::factory()->create(['role' => 'student']);
        $teacher = User::factory()->create(['role' => 'teacher']);
        $category = Category::factory()->create(['user_id' => $teacher->id]);
        $course = Course::factory()->create([
            'teacher_id' => $teacher->id,
            'category_id' => $category->id,
            'is_published' => true,
        ]);
        $lesson = Lesson::factory()->create([
            'course_id' => $course->id,
            'order_number' => 1,
        ]);

        $this->actingAs($student);
        $this->post(route('courses.enroll', $course));
        $this->post(route('lessons.markDone', $lesson));

        $this->assertDatabaseHas('user_activities', [
            'user_id' => $student->id,
            'action' => UserActivity::ACTION_COURSE_COMPLETED,
            'trackable_type' => Course::class,
            'trackable_id' => $course->id,
        ]);
    }

    public function test_activity_log_stores_metadata(): void
    {
        $student = User::factory()->create(['role' => 'student']);
        $teacher = User::factory()->create(['role' => 'teacher']);
        $category = Category::factory()->create(['user_id' => $teacher->id]);
        $course = Course::factory()->create([
            'teacher_id' => $teacher->id,
            'category_id' => $category->id,
            'is_published' => true,
            'title' => 'Test Course for Metadata',
        ]);

        $this->actingAs($student);
        $this->post(route('courses.enroll', $course));

        $activity = UserActivity::where('user_id', $student->id)
            ->where('action', UserActivity::ACTION_COURSE_ENROLLED)
            ->first();

        $this->assertNotNull($activity);
        $this->assertIsArray($activity->metadata);
        $this->assertEquals('Test Course for Metadata', $activity->metadata['course_title']);
    }

    public function test_user_can_query_their_activities(): void
    {
        $student = User::factory()->create(['role' => 'student']);
        
        UserActivity::log($student->id, UserActivity::ACTION_COURSE_ENROLLED, null, ['test' => 'data']);
        UserActivity::log($student->id, UserActivity::ACTION_LESSON_VIEWED, null, ['test' => 'data']);

        $activities = UserActivity::forUser($student->id)->get();

        $this->assertCount(2, $activities);
    }

    public function test_activities_can_be_filtered_by_action(): void
    {
        $student = User::factory()->create(['role' => 'student']);
        
        UserActivity::log($student->id, UserActivity::ACTION_COURSE_ENROLLED);
        UserActivity::log($student->id, UserActivity::ACTION_LESSON_VIEWED);
        UserActivity::log($student->id, UserActivity::ACTION_MESSAGE_SENT);

        $enrollments = UserActivity::byAction(UserActivity::ACTION_COURSE_ENROLLED)->get();

        $this->assertCount(1, $enrollments);
        $this->assertEquals(UserActivity::ACTION_COURSE_ENROLLED, $enrollments->first()->action);
    }
}
