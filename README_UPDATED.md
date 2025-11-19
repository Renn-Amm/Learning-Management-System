# Mini Learning Management System

A simple Learning Management System built with Laravel 11 and Breeze. Teachers create courses and lessons. Students enroll and learn.

## Quick Start

```bash
composer install
npm install
cp .env.example .env
php artisan key:generate
php artisan migrate:fresh --seed
php artisan storage:link
npm run dev
```

Then open `http://mini-lms.test` in your browser.

## Login Credentials

**Teacher:**
- Email: teacher@example.com
- Password: password

**Students:**
- Email: student1@example.com / Password: password  
- Email: student2@example.com / Password: password

## Installation Steps

### 1. Install Dependencies

```bash
composer install
npm install
```

### 2. Setup Environment

```bash
cp .env.example .env
php artisan key:generate
```

### 3. Configure Database

The project uses SQLite by default. No configuration needed. If you want MySQL:

```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=mini_lms
DB_USERNAME=root
DB_PASSWORD=your_password
```

### 4. Run Migrations and Seeders

```bash
php artisan migrate:fresh --seed
```

This creates:
- 4 categories (Programming, Math, Business, Design)
- 20 courses (5 per category)
- 60 lessons (3 per course)
- 3 users (1 teacher, 2 students)
- 2 sample enrollments

### 5. Create Storage Link

```bash
php artisan storage:link
```

### 6. Start Development Server

If using Laravel Herd, just run:
```bash
npm run dev
```

If not using Herd:
```bash
php artisan serve
npm run dev
```

## User Roles

### Student (Default)
- All new registrations are students
- Can browse all courses
- Can enroll in any course
- Can view lessons
- Progress updates automatically when viewing lessons
- Cannot create or edit courses

### Teacher (Manual Assignment)
- Must be set manually in database
- Can create, edit, and delete courses
- Can add, edit, and delete lessons
- Can assign courses to categories
- Can see student enrollments and progress
- Cannot enroll in courses

### How to Make a User a Teacher

**Method 1: Database GUI**
```sql
UPDATE users SET role = 'teacher' WHERE email = 'user@example.com';
```

**Method 2: Tinker**
```bash
php artisan tinker
```
```php
$user = App\Models\User::where('email', 'user@example.com')->first();
$user->role = 'teacher';
$user->save();
```

## Categories and Courses

The system includes 4 categories:
1. **Programming** - 5 courses
2. **Math** - 5 courses
3. **Business** - 5 courses
4. **Design** - 5 courses

Students see ALL 20 courses mixed together on the courses page. They do NOT see category filters.

Teachers assign a category when creating a course.

## How Progress Works

Progress is calculated automatically:

```
progress = (viewed lessons / total lessons) * 100
```

When a student views a lesson for the first time:
1. The lesson ID is added to `viewed_lessons` array in enrollments table
2. Progress percentage is calculated
3. If all lessons viewed, `is_completed = true`

This happens automatically. No buttons to click.

## For Teachers

### Create a Course
1. Log in as teacher
2. Go to Dashboard
3. Click "Create New Course"
4. Fill in:
   - Title
   - Description  
   - Category (Programming, Math, Business, Design)
   - Level (beginner, intermediate, advanced)
   - Thumbnail (optional)
5. Click "Create Course"

### Add Lessons
1. Go to your course page
2. Click "Add Lesson"
3. Fill in:
   - Title
   - Content
   - Order Number (1, 2, 3...)
   - Duration (minutes)
4. Click "Create Lesson"

### Edit/Delete
- Click "Edit" on any course or lesson you created
- Click "Delete" to remove (will ask for confirmation)
- Deleting a course deletes all its lessons

### View Student Progress
- On course page, see who is enrolled
- See their progress percentage
- See if they completed the course

## For Students

### Browse Courses
1. Log in as student
2. Click "Courses" in navigation
3. See all 20 courses mixed together

### Enroll in Course
1. Click any course
2. Click "Enroll Now"
3. You're enrolled

### View Lessons
1. Go to your Dashboard
2. Click enrolled course
3. Click any lesson to view
4. Progress updates automatically

### Track Progress
- Dashboard shows all enrolled courses
- Progress bar shows completion percentage
- When you view all lessons, course is marked complete

## Common Laravel Errors Fixed

### Error: Vite manifest not found
**Fix:** Run `npm run dev`

### Error: SQLSTATE access denied  
**Fix:** Check database credentials in `.env`

### Error: No encryption key
**Fix:** Run `php artisan key:generate`

### Error: Storage link not found
**Fix:** Run `php artisan storage:link`

### Error: Class Alpine not found
**Fix:** Run `npm install alpinejs` then `npm run dev`

### Error: Unable to locate component
**Fix:** Run `php artisan view:clear`

### Error: CSRF token mismatch
**Fix:** Clear browser cache or restart server

### Error: Migration failed
**Fix:** Run `php artisan migrate:fresh --seed`

### Error: Permission denied (Windows)
**Fix:**
```bash
icacls storage /grant Everyone:F /t
icacls bootstrap/cache /grant Everyone:F /t
```

### Error: Access denied (not teacher)
**Fix:** Update user role in database (see "How to Make a User a Teacher")

## Project Structure

```
mini-lms/
├── app/
│   ├── Http/
│   │   ├── Controllers/
│   │   │   ├── Auth/              # Login, register, password reset
│   │   │   ├── CategoryController # Category CRUD
│   │   │   ├── CourseController   # Course CRUD
│   │   │   ├── DashboardController # Role-based dashboards
│   │   │   ├── EnrollmentController # Enroll and progress
│   │   │   ├── LessonController   # Lesson CRUD + auto-progress
│   │   │   └── ProfileController  # User profile
│   │   ├── Middleware/
│   │   │   ├── EnsureTeacher      # Teacher-only routes
│   │   │   └── EnsureStudent      # Student-only routes
│   │   └── Requests/
│   └── Models/
│       ├── Category   # hasMany Courses
│       ├── Course     # belongsTo Category, User, hasMany Lessons
│       ├── Enrollment # belongsTo User, Course, tracks progress
│       ├── Lesson     # belongsTo Course
│       └── User       # hasMany Courses, belongsToMany through Enrollment
├── database/
│   ├── migrations/    # All table structures
│   └── seeders/       # 4 categories, 20 courses, 60 lessons
├── resources/
│   └── views/         # All Blade templates
└── routes/
    ├── web.php        # Main routes
    └── auth.php       # Auth routes
```

## Routes

### Public
- `GET /` - Landing page

### Authentication
- `GET /login` - Login form
- `POST /login` - Authenticate
- `GET /register` - Register form
- `POST /register` - Create account
- `POST /logout` - Logout

### Student Routes (auth + ensureStudent)
- `POST /courses/{id}/enroll` - Enroll in course
- `GET /lessons/{id}` - View lesson (auto-progress)

### Teacher Routes (auth + ensureTeacher)
- `GET /courses/create` - Create course form
- `POST /courses` - Store course
- `GET /courses/{id}/edit` - Edit course form
- `PUT /courses/{id}` - Update course
- `DELETE /courses/{id}` - Delete course
- `GET /courses/{course}/lessons/create` - Add lesson form
- `POST /courses/{course}/lessons` - Store lesson
- `GET /lessons/{id}/edit` - Edit lesson form
- `PUT /lessons/{id}` - Update lesson
- `DELETE /lessons/{id}` - Delete lesson
- `GET /categories` - List categories
- `POST /categories` - Create category
- `PUT /categories/{id}` - Update category
- `DELETE /categories/{id}` - Delete category

### All Authenticated Users
- `GET /dashboard` - Role-based dashboard
- `GET /courses` - Browse courses
- `GET /courses/{id}` - View course details

## Database Tables

### users
- id, name, email, password, role (student/teacher), timestamps

### categories  
- id, name, timestamps

### courses
- id, title, description, thumbnail, level, teacher_id, category_id, timestamps

### lessons
- id, course_id, title, content, order_number, duration, timestamps

### enrollments
- id, user_id, course_id, progress, is_completed, viewed_lessons (JSON), timestamps

## Troubleshooting

### Alpine.js errors in console
Install it: `npm install alpinejs`

### Thumbnails not showing
Run: `php artisan storage:link`

### Can't access teacher features
Update role: `UPDATE users SET role = 'teacher' WHERE id = YOUR_ID;`

### Progress not updating
Check that you're viewing lessons as an enrolled student. Teachers don't get progress.

### Categories not showing for teachers
They should appear in course create/edit forms. Check migrations ran correctly.

### Courses not showing on student dashboard
Enroll in a course first. Empty state shows if no enrollments.

## Building for Production

```bash
npm run build
php artisan config:cache
php artisan route:cache
php artisan view:cache
```

Update `.env`:
```env
APP_ENV=production
APP_DEBUG=false
```

## License

Open source project built with Laravel 11 and Breeze.
