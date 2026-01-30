<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\Course;
use App\Models\Category;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ApiTest extends TestCase
{
    use RefreshDatabase;

    public function test_api_returns_courses_list(): void
    {
        $courses = Course::factory()->count(3)->create();

        $response = $this->getJson('/api/v1/courses');

        $response->assertStatus(200)
            ->assertJsonStructure([
                'success',
                'data' => [
                    '*' => ['id', 'title', 'description', 'level']
                ],
                'pagination',
            ]);
    }

    public function test_api_returns_single_course(): void
    {
        $course = Course::factory()->create();

        $response = $this->getJson("/api/v1/courses/{$course->id}");

        $response->assertStatus(200)
            ->assertJsonStructure([
                'success',
                'data' => ['id', 'title', 'description', 'level', 'teacher', 'category'],
            ])
            ->assertJson([
                'success' => true,
                'data' => [
                    'id' => $course->id,
                    'title' => $course->title,
                ],
            ]);
    }

    public function test_api_filters_courses_by_level(): void
    {
        Course::factory()->create(['level' => 'beginner']);
        Course::factory()->create(['level' => 'advanced']);

        $response = $this->getJson('/api/v1/courses?level=beginner');

        $response->assertStatus(200);
        $data = $response->json('data');
        
        foreach ($data as $course) {
            $this->assertEquals('beginner', $course['level']);
        }
    }

    public function test_health_check_endpoint_returns_status(): void
    {
        $response = $this->getJson('/api/health');

        $response->assertStatus(200)
            ->assertJsonStructure([
                'status',
                'timestamp',
                'checks' => [
                    'database',
                    'cache',
                    'storage',
                ],
            ]);
    }

    public function test_authenticated_teacher_can_create_course_via_api(): void
    {
        $teacher = User::factory()->create(['role' => 'teacher']);
        $category = Category::factory()->create();

        $response = $this->actingAs($teacher)->postJson('/api/v1/courses', [
            'title' => 'API Test Course',
            'description' => 'Created via API',
            'level' => 'intermediate',
            'category_id' => $category->id,
        ]);

        $response->assertStatus(201)
            ->assertJson([
                'success' => true,
                'message' => 'Course created successfully',
            ]);

        $this->assertDatabaseHas('courses', [
            'title' => 'API Test Course',
            'teacher_id' => $teacher->id,
        ]);
    }

    public function test_unauthenticated_user_cannot_create_course_via_api(): void
    {
        $category = Category::factory()->create();

        $response = $this->postJson('/api/v1/courses', [
            'title' => 'API Test Course',
            'description' => 'Created via API',
            'level' => 'intermediate',
            'category_id' => $category->id,
        ]);

        $response->assertStatus(401);
    }
}
