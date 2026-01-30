<?php

namespace Database\Factories;

use App\Models\Course;
use App\Models\User;
use App\Models\Category;
use Illuminate\Database\Eloquent\Factories\Factory;

class CourseFactory extends Factory
{
    protected $model = Course::class;

    public function definition(): array
    {
        return [
            'title' => fake()->sentence(4),
            'description' => fake()->paragraph(),
            'thumbnail' => null,
            'level' => fake()->randomElement(['beginner', 'intermediate', 'advanced']),
            'teacher_id' => User::factory()->create(['role' => 'teacher'])->id,
            'category_id' => Category::factory(),
            'is_published' => false,
            'duration_hours' => fake()->numberBetween(5, 50),
        ];
    }

    public function published(): static
    {
        return $this->state(fn (array $attributes) => [
            'is_published' => true,
        ]);
    }
}
