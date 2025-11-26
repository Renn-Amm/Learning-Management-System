# Mini LMS

A modern Learning Management System built with **Laravel 12** and **Breeze**. Teachers create courses, students learn.

## Tech Stack

- **Laravel 12** (PHP 8.2+)
- **Breeze** (Authentication)
- **SQLite** (Database)
- **Tailwind CSS** (Styling)
- **Alpine.js** (Interactivity)
- **Laravel Debugbar** (Development debugging)

## Demo Accounts

**Teacher:** teacher@example.com / password  
**Student:** student1@example.com / password

## Installation

### Development Setup

```bash
# Clone and install
composer install
npm install

# Environment
cp .env.example .env
php artisan key:generate

# Database
php artisan migrate:fresh --seed
php artisan storage:link

# Run development servers
npm run dev              # Vite (keep running)
php artisan serve       # Laravel server (if not using Herd)
```

Visit: `http://localhost:8000` or `http://mini-lms.test` (if using Herd)

### Database

Uses **SQLite** by default (no configuration needed). For MySQL:

```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_DATABASE=mini_lms
DB_USERNAME=root
DB_PASSWORD=your_password
```

### What Gets Seeded

- 4 categories (Programming, Math, Business, Design)
- 20 courses (5 per category, 3 lessons each)
- 3 users (1 teacher, 2 students)
- Sample enrollments with progress

## Version 2.0 Highlights

- **60-70% Performance Improvement** via caching and N+1 query elimination
- **Complete Authorization** using Laravel Policies for all models
- **Publish/Unpublish Courses** - Teachers control course visibility
- **Thumbnail Fallbacks** - Professional category name display when no image
- **CDN Assets** - Tailwind CSS & Alpine.js via CDN (no build step required)
- **100% Named Routes** - Improved maintainability
- **Full CRUD** on all models with authorization

## Features

### For Students

**Registration & Login**
- Choose "Register as Student" on signup
- Auto-login after registration

**Browse & Enroll**
- View all courses with search and filters
- Search by title, description, category, or skills
- Filter by category
- Two-button system: "View Details" + "Enroll"
- See course overview before enrolling
- Instant enrollment

**Learning**
- View full lessons after enrolling
- Manual progress tracking with "Mark as Done" button
- Download lesson attachments securely (images, PDFs, Word docs)
- Progress bar shows completion percentage
- Course marked complete when all lessons marked done

**Communication**
- Conversation-based messaging with teachers (WhatsApp-style)
- Back-to-back chat interface
- View all conversations in one place
- Reply directly without searching

**Dashboard Features**
- View enrolled courses with progress bars
- Continue button takes you to next unfinished lesson
- Suggested courses grouped by category (Programming, Math, Business, Design)
- Recent lessons viewed history
- Achievements stats (completed courses, lessons viewed)

**What Students See:**
- **Not Enrolled:** Lesson titles only + enroll button
- **Enrolled:** Full lesson content + "View" buttons

### For Teachers

**Registration & Login**
- Choose "Register as Teacher" on signup
- Access teacher dashboard

**Course Management**
- Create courses with title, description, level, category
- Upload course thumbnails (images)
- Create skills inline (comma-separated: "Laravel, Vue.js, MySQL")
- Skills automatically inherit category colors
- **Publish/Unpublish courses** to control student visibility
- Edit/delete only YOUR courses (policy-enforced)
- View student enrollments and progress per course
- Courses without thumbnails show category name as fallback with: title, content, attachments (images/PDF/DOCX), order number, duration
- Upload files with lessons (max 10MB per file, stored privately)
- Edit/delete only YOUR courses
- View student progress per course

**Skills System**
- Skills created inline during course creation (no separate management page)
- Skills automatically inherit category color
- Category Colors (Fixed):
  - Programming: Blue
  - Math: Green
  - Business: Pink
  - Design: Yellow
- Automatic text color contrast (black on light bg, white on dark bg)
- Skills reused across courses when names match

**Category Management**
- View all categories with fixed colors
- Create new categories with auto-generated unique colors
- Edit/delete only YOUR categories
- System categories (no creator) are read-only
- Each category has a unique color for all its course skills
- Colors automatically assigned from pool of 25+ vibrant colors

**Communication**
- Conversation-based messaging with students (WhatsApp-style)
- Back-to-back chat interface
- View all conversations in one place
- Receive email when students enroll
- Email includes student info and enrollment time

**Enhanced Dashboard**
- Total courses, students, and lessons stats
- Recent Student Activity (enrollments and completions)
- Student Progress Summary per course:
  - Total enrolled students
  - Average progress percentage
  - Number of students who completed
- Quick access to manage and edit courses

### Manual Progress System

Progress updates when students mark lessons as done:

```
Progress = (Completed Lessons / Total Lessons) × 100
```

- Student views lesson content
- Student clicks "Mark as Done" button
- Lesson added to `viewed_lessons` array
- Progress percentage updates
- Course completes at 100%
- Student-controlled (no automatic tracking)

### Authorization & Security

**Teachers can:**
-  Create any course with inline skills (inherit category colors)
-  Edit/delete their own courses only
-  Create any category with fixed color
-  Edit/delete their own categories
-  View student progress ONLY on their own courses
-  View recent activity ONLY from their own courses
-  Message students via conversation-based chat
-  Download files from their own courses only

**Students can:**
-  Browse all courses with category-colored skill tags
-  Enroll in any course
-  View lessons in enrolled courses only
-  Continue learning from last unfinished lesson
-  View suggested courses grouped by category
-  Track achievements and recent lessons viewed
-  Message teachers via conversation-based chat
-  Download files from enrolled courses only

**Security Features:**
- **Private File Storage:** Lesson attachments not publicly accessible
- **Download Authorization:** Permission checks before file access
- **Directory Traversal Protection:** basename() prevents path attacks
- **Conversation Privacy:** Users only see their own conversations
- **Dashboard Isolation:** Teachers see only their data, students see only theirs
- **CSRF Protection:** All forms protected
- **Role-Based Access:** Middleware enforces teacher/student separation

**Lesson Order Numbers:**
- Must be unique per course
- Different courses can have same numbers
- Prevents duplicate ordering

## Usage Guide

### Create Course (Teacher)
1. Dashboard → "Create New Course"
2. Fill form → Submit
3. Click "Add Lesson" on course page
4. Add lessons with unique order numbers
5. Students can now enroll

### Enroll & Learn (Student)
1. Browse courses → Find interesting course
2. Click "View Details" (see overview)
3. Click "Enroll" (instant enrollment)
4. Click "View Course" → Access all lessons
5. Click lesson → Read content → Download attachments
6. Click "Mark as Done" → Progress updates

### Manage Categories (Teacher)
1. Navigation → "Categories"
2. Click "Create Category"
3. Enter name → Submit
4. Use in course creation dropdown
5. Edit/Delete: Only categories you created

## Common Issues

**404 on Create Course:**
```bash
php artisan route:clear
php artisan view:clear
php artisan config:clear
```

**Vite manifest not found:**
```bash
npm run dev
```

**Storage/images not showing:**
```bash
php artisan storage:link
```

**No encryption key:**
```bash
php artisan key:generate
```

**Can't access teacher features:**
```bash
php artisan tinker
$user = App\Models\User::find(YOUR_ID);
$user->role = 'teacher';
$user->save();
```

**Windows permissions:**
```bash
icacls storage /grant Everyone:F /t
icacls bootstrap/cache /grant Everyone:F /t
```

## API Endpoints

**Public:**
- `GET /` - Welcome page

**Auth:**
- `GET|POST /login` - Login
- `GET|POST /register` - Register (with role selection)
- `POST /logout` - Logout

**Students:**
- `POST /courses/{id}/enroll` - Enroll
- `GET /lessons/{id}` - View lesson (auto-progress)

**Teachers:**
- `GET|POST /courses/create` - Create course
- `GET|PUT /courses/{id}/edit` - Edit course
- `DELETE /courses/{id}` - Delete course
- `GET|POST /courses/{id}/lessons/create` - Add lesson
- `GET|PUT /lessons/{id}/edit` - Edit lesson
- `DELETE /lessons/{id}` - Delete lesson
- `GET|POST /categories` - Manage categories

**All Users:**
- `GET /dashboard` - Role-based dashboard
- `GET /courses` - Browse courses
- `GET /courses/{id}` - Course details

## Database Schema

**users:** id, name, email, password, role (student/teacher)  
**categories:** id, name, user_id (creator)  
**courses:** id, title, description, thumbnail, level, teacher_id, category_id  
**lessons:** id, course_id, title, content, order_number, duration  
**enrollments:** id, user_id, course_id, progress, is_completed, viewed_lessons (JSON)

## Production Build

```bash
# Build assets
npm run build

# Optimize Laravel
php artisan config:cache
php artisan route:cache
php artisan view:cache

# Update .env
APP_ENV=production
APP_DEBUG=false
```

## Color Scheme

Strict black and white design:
- **Background:** White
- **Text:** Black
- **Buttons:** Black with white text (hover inverts)
- **Borders:** Black
- **Progress bars:** Black fill on white

No colors, gradients, or shadows used.

## Version

**Current:** 1.3.0  
**Laravel:** 12.x  
**PHP:** 8.2+

**Latest Features:**
- Lesson file attachments (images, PDF, DOCX)
- Manual progress with "Mark as Done" button
- Thumbnail image-only validation

## License

MIT License
