<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\Course;
use App\Models\Enrollment;
use App\Models\Lesson;
use App\Models\Skill;
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
                ['title' => 'Python for Beginners', 'description' => 'Learn Python programming from scratch. Cover variables, data types, functions, and object-oriented programming.', 'level' => 'beginner', 'skills' => ['Python', 'OOP', 'Data Types', 'Functions']],
                ['title' => 'JavaScript Essentials', 'description' => 'Master JavaScript fundamentals including ES6 features, async programming, and modern best practices.', 'level' => 'beginner', 'skills' => ['JavaScript', 'ES6', 'Async Programming', 'DOM']],
                ['title' => 'Web Development with Laravel', 'description' => 'Build modern web applications with Laravel. Learn MVC, routing, Eloquent ORM, and authentication.', 'level' => 'intermediate', 'skills' => ['PHP', 'Laravel', 'MVC', 'Eloquent ORM', 'Authentication']],
                ['title' => 'React for Frontend Development', 'description' => 'Create interactive user interfaces with React. Learn components, hooks, state management, and routing.', 'level' => 'intermediate', 'skills' => ['React', 'Hooks', 'State Management', 'JSX']],
                ['title' => 'Advanced Algorithm Design', 'description' => 'Master algorithms and data structures. Learn sorting, searching, dynamic programming, and optimization.', 'level' => 'advanced', 'skills' => ['Algorithms', 'Data Structures', 'Dynamic Programming', 'Optimization']],
            ],
            'Math' => [
                ['title' => 'Algebra Fundamentals', 'description' => 'Learn core algebra concepts including equations, functions, and graphing techniques.', 'level' => 'beginner', 'skills' => ['Algebra', 'Equations', 'Functions', 'Graphing']],
                ['title' => 'Calculus I', 'description' => 'Introduction to differential and integral calculus. Learn limits, derivatives, and their applications.', 'level' => 'intermediate', 'skills' => ['Calculus', 'Limits', 'Derivatives', 'Integrals']],
                ['title' => 'Linear Algebra', 'description' => 'Study vectors, matrices, linear transformations, and their applications in computer science.', 'level' => 'intermediate', 'skills' => ['Vectors', 'Matrices', 'Linear Transformations', 'Eigenvalues']],
                ['title' => 'Statistics and Probability', 'description' => 'Learn statistical analysis, probability theory, distributions, and hypothesis testing.', 'level' => 'intermediate', 'skills' => ['Statistics', 'Probability', 'Distributions', 'Hypothesis Testing']],
                ['title' => 'Discrete Mathematics', 'description' => 'Explore logic, set theory, combinatorics, and graph theory essential for computer science.', 'level' => 'advanced', 'skills' => ['Logic', 'Set Theory', 'Combinatorics', 'Graph Theory']],
            ],
            'Business' => [
                ['title' => 'Introduction to Marketing', 'description' => 'Learn marketing fundamentals, consumer behavior, branding, and digital marketing strategies.', 'level' => 'beginner', 'skills' => ['Marketing', 'Branding', 'Consumer Behavior', 'Digital Marketing']],
                ['title' => 'Financial Accounting', 'description' => 'Understand financial statements, accounting principles, and business financial management.', 'level' => 'beginner', 'skills' => ['Accounting', 'Financial Statements', 'Bookkeeping', 'Financial Analysis']],
                ['title' => 'Project Management Basics', 'description' => 'Learn project planning, execution, risk management, and team leadership skills.', 'level' => 'intermediate', 'skills' => ['Project Management', 'Agile', 'Scrum', 'Risk Management']],
                ['title' => 'Entrepreneurship Essentials', 'description' => 'Discover how to start and grow a business. Learn business models, funding, and scaling strategies.', 'level' => 'intermediate', 'skills' => ['Entrepreneurship', 'Business Planning', 'Funding', 'Scaling']],
                ['title' => 'Strategic Management', 'description' => 'Master business strategy, competitive analysis, and organizational leadership at executive level.', 'level' => 'advanced', 'skills' => ['Strategy', 'Competitive Analysis', 'Leadership', 'Decision Making']],
            ],
            'Design' => [
                ['title' => 'Graphic Design Basics', 'description' => 'Learn design principles, color theory, typography, and layout fundamentals.', 'level' => 'beginner', 'skills' => ['Graphic Design', 'Color Theory', 'Typography', 'Layout']],
                ['title' => 'UI Design with Figma', 'description' => 'Create user interfaces using Figma. Learn prototyping, design systems, and collaboration tools.', 'level' => 'beginner', 'skills' => ['Figma', 'UI Design', 'Prototyping', 'Design Systems']],
                ['title' => 'UX Research and Testing', 'description' => 'Master user research methods, usability testing, and data-driven design decisions.', 'level' => 'intermediate', 'skills' => ['UX Research', 'Usability Testing', 'User Interviews', 'Data Analysis']],
                ['title' => 'Motion Graphics', 'description' => 'Learn animation principles and create engaging motion graphics for web and mobile.', 'level' => 'intermediate', 'skills' => ['Animation', 'After Effects', 'Motion Design', 'Keyframes']],
                ['title' => 'Brand Identity Design', 'description' => 'Design complete brand identities including logos, style guides, and brand strategy.', 'level' => 'advanced', 'skills' => ['Branding', 'Logo Design', 'Style Guides', 'Brand Strategy']],
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
                
                // Create skills and attach to course (skills use category color)
                $categoryColor = $categories[$categoryName];
                if (isset($courseData['skills'])) {
                    foreach ($courseData['skills'] as $skillName) {
                        $skill = Skill::firstOrCreate(
                            ['name' => $skillName],
                            ['color_code' => $categoryColor]
                        );
                        $course->skills()->attach($skill->id);
                    }
                }
                
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
