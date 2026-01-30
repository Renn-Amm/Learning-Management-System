<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\Course;
use App\Models\Category;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CourseManagementTest extends TestCase
{
    use RefreshDatabase;

    public function test_teacher_can_create_course(): void
    {
        $teacher = User::factory()->create(['role' => 'teacher']);
        $category = Category::factory()->create();

        $response = $this->actingAs($teacher)->post(route('courses.store'), [
            'title' => 'Test Course',
            'description' => 'This is a test course description',
            'level' => 'beginner',
            'category_id' => $category->id,
            'skills' => 'PHP, Laravel',
        ]);

        $response->assertRedirect();
        $this->assertDatabaseHas('courses', [
            'title' => 'Test Course',
            'teacher_id' => $teacher->id,
        ]);
    }

    public function test_student_cannot_create_course(): void
    {
        $student = User::factory()->create(['role' => 'student']);
        $category = Category::factory()->create();

        $response = $this->actingAs($student)->post(route('courses.store'), [
            'title' => 'Test Course',
            'description' => 'This is a test course description',
            'level' => 'beginner',
            'category_id' => $category->id,
            'skills' => 'PHP, Laravel',
        ]);

        $response->assertForbidden();
    }

    public function test_teacher_can_publish_course(): void
    {
        $teacher = User::factory()->create(['role' => 'teacher']);
        $course = Course::factory()->create([
            'teacher_id' => $teacher->id,
            'is_published' => false,
        ]);

        $response = $this->actingAs($teacher)->patch(route('courses.publish', $course));

        $response->assertRedirect();
        $this->assertTrue($course->fresh()->is_published);
    }

    public function test_teacher_can_unpublish_course(): void
    {
        $teacher = User::factory()->create(['role' => 'teacher']);
        $course = Course::factory()->create([
            'teacher_id' => $teacher->id,
            'is_published' => true,
        ]);

        $response = $this->actingAs($teacher)->patch(route('courses.unpublish', $course));

        $response->assertRedirect();
        $this->assertFalse($course->fresh()->is_published);
    }

    public function test_students_only_see_published_courses(): void
    {
        $student = User::factory()->create(['role' => 'student']);
        
        $publishedCourse = Course::factory()->create(['is_published' => true]);
        $unpublishedCourse = Course::factory()->create(['is_published' => false]);

        $response = $this->actingAs($student)->get(route('courses.index'));

        $response->assertSee($publishedCourse->title);
        $response->assertDontSee($unpublishedCourse->title);
    }
}
