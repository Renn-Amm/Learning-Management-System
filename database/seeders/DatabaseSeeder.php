<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\Course;
use App\Models\Enrollment;
use App\Models\Lesson;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // Define categories with specific colors
        $categories = [
            'Programming' => '#4ECDC4',  // Teal/Blue
            'Math' => '#52B788',         // Green
            'Business' => '#FB5607',     // Orange
            'Design' => '#8338EC',       // Purple
        ];

        $createdCategories = [];
        foreach ($categories as $categoryName => $colorCode) {
            $createdCategories[$categoryName] = Category::create([
                'name' => $categoryName,
                'color_code' => $colorCode
            ]);
        }

        $teacher = User::create([
            'name' => 'John Teacher',
            'email' => 'teacher@example.com',
            'password' => Hash::make('password'),
            'role' => 'teacher',
        ]);

        $student1 = User::create([
            'name' => 'Alice Student',
            'email' => 'student1@example.com',
            'password' => Hash::make('password'),
            'role' => 'student',
        ]);

        $student2 = User::create([
            'name' => 'Bob Student',
            'email' => 'student2@example.com',
            'password' => Hash::make('password'),
            'role' => 'student',
        ]);

        $coursesData = [
            'Programming' => [
                ['title' => 'Python for Beginners', 'description' => 'Learn Python programming from scratch. Cover variables, data types, functions, and object-oriented programming.', 'level' => 'beginner'],
                ['title' => 'JavaScript Essentials', 'description' => 'Master JavaScript fundamentals including ES6 features, async programming, and modern best practices.', 'level' => 'beginner'],
                ['title' => 'Web Development with Laravel', 'description' => 'Build modern web applications with Laravel. Learn MVC, routing, Eloquent ORM, and authentication.', 'level' => 'intermediate'],
                ['title' => 'React for Frontend Development', 'description' => 'Create interactive user interfaces with React. Learn components, hooks, state management, and routing.', 'level' => 'intermediate'],
                ['title' => 'Advanced Algorithm Design', 'description' => 'Master algorithms and data structures. Learn sorting, searching, dynamic programming, and optimization.', 'level' => 'advanced'],
            ],
            'Math' => [
                ['title' => 'Algebra Fundamentals', 'description' => 'Learn core algebra concepts including equations, functions, and graphing techniques.', 'level' => 'beginner'],
                ['title' => 'Calculus I', 'description' => 'Introduction to differential and integral calculus. Learn limits, derivatives, and their applications.', 'level' => 'intermediate'],
                ['title' => 'Linear Algebra', 'description' => 'Study vectors, matrices, linear transformations, and their applications in computer science.', 'level' => 'intermediate'],
                ['title' => 'Statistics and Probability', 'description' => 'Learn statistical analysis, probability theory, distributions, and hypothesis testing.', 'level' => 'intermediate'],
                ['title' => 'Discrete Mathematics', 'description' => 'Explore logic, set theory, combinatorics, and graph theory essential for computer science.', 'level' => 'advanced'],
            ],
            'Business' => [
                ['title' => 'Introduction to Marketing', 'description' => 'Learn marketing fundamentals, consumer behavior, branding, and digital marketing strategies.', 'level' => 'beginner'],
                ['title' => 'Financial Accounting', 'description' => 'Understand financial statements, accounting principles, and business financial management.', 'level' => 'beginner'],
                ['title' => 'Project Management Basics', 'description' => 'Learn project planning, execution, risk management, and team leadership skills.', 'level' => 'intermediate'],
                ['title' => 'Entrepreneurship Essentials', 'description' => 'Discover how to start and grow a business. Learn business models, funding, and scaling strategies.', 'level' => 'intermediate'],
                ['title' => 'Strategic Management', 'description' => 'Master business strategy, competitive analysis, and organizational leadership at executive level.', 'level' => 'advanced'],
            ],
            'Design' => [
                ['title' => 'Graphic Design Basics', 'description' => 'Learn design principles, color theory, typography, and layout fundamentals.', 'level' => 'beginner'],
                ['title' => 'UI Design with Figma', 'description' => 'Create user interfaces using Figma. Learn prototyping, design systems, and collaboration tools.', 'level' => 'beginner'],
                ['title' => 'UX Research and Testing', 'description' => 'Master user research methods, usability testing, and data-driven design decisions.', 'level' => 'intermediate'],
                ['title' => 'Motion Graphics', 'description' => 'Learn animation principles and create engaging motion graphics for web and mobile.', 'level' => 'intermediate'],
                ['title' => 'Brand Identity Design', 'description' => 'Design complete brand identities including logos, style guides, and brand strategy.', 'level' => 'advanced'],
            ],
        ];

        $courses = [];
        foreach ($coursesData as $categoryName => $categoryCoursesData) {
            foreach ($categoryCoursesData as $courseData) {
                $course = Course::create([
                    'title' => $courseData['title'],
                    'description' => $courseData['description'],
                    'level' => $courseData['level'],
                    'teacher_id' => $teacher->id,
                    'category_id' => $createdCategories[$categoryName]->id,
                ]);
                
                $courses[] = $course;
                
                for ($i = 1; $i <= 3; $i++) {
                    Lesson::create([
                        'course_id' => $course->id,
                        'title' => "Lesson {$i}: " . substr($courseData['title'], 0, 30),
                        'content' => "This is lesson {$i} of {$courseData['title']}. Learn key concepts and practical skills.",
                        'order_number' => $i,
                        'duration' => 30 + ($i * 10),
                    ]);
                }
            }
        }

        Enrollment::create([
            'user_id' => $student1->id,
            'course_id' => $courses[0]->id,
            'progress' => 0,
            'is_completed' => false,
            'viewed_lessons' => [],
        ]);

        Enrollment::create([
            'user_id' => $student2->id,
            'course_id' => $courses[1]->id,
            'progress' => 0,
            'is_completed' => false,
            'viewed_lessons' => [],
        ]);
    }
}
