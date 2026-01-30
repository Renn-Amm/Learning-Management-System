<?php

namespace Database\Factories;

use App\Models\Lesson;
use App\Models\Course;
use Illuminate\Database\Eloquent\Factories\Factory;

class LessonFactory extends Factory
{
    protected $model = Lesson::class;

    public function definition(): array
    {
        return [
            'course_id' => Course::factory(),
            'title' => fake()->sentence(3),
            'content' => fake()->paragraphs(3, true),
            'order_number' => fake()->numberBetween(1, 10),
            'duration' => fake()->numberBetween(10, 60),
            'attachment' => null,
            'attachment_name' => null,
        ];
    }
}
