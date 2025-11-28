# Changelog

All notable changes to the EduHub LMS project will be documented in this file.

The format is based on [Keep a Changelog](https://keepachangelog.com/en/1.0.0/),
and this project adheres to [Semantic Versioning](https://semver.org/spec/v2.0.0.html).

---

## [1.1.0] - 2025-11-27

### Added
- **Email Notifications System**
  - Email notification when students enroll in courses (sent to teacher)
  - Email notification when users receive new messages
  - NewMessageReceived mailable class with HTML template
  - Error handling and logging for email failures
  - Test script: `test-email.php` for debugging email configuration
- **Authentication Improvements**
  - Role-based login validation (prevents cross-role login)
  - Tab persistence on login/register forms after validation errors
  - Clear error messages for wrong role login attempts
- **Messaging Features**
  - Real-time messaging between users
  - Unread message notifications
  - Email notifications when receiving messages
  - Mark messages as read automatically
- **Documentation**
  - Gmail SMTP configuration instructions in .env.example
  - Comprehensive email debugging guide
  - Complete models and relationships documentation in TECHNICAL_EXPLANATION.md

### Changed
- **BREAKING:** Rebranded from "Mini LMS" to "EduHub LMS" throughout application
- Message notification badge color changed from black to red for better visibility (both navigation and messages page)
- Login page now maintains selected tab (Student/Teacher) after errors
- Register page role description now updates correctly when switching roles
- Updated welcome page with professional messaging
- Enhanced .env.example with detailed email configuration
- Removed demo account credentials from login page (cleaner UI)
- Email sending wrapped in try-catch to prevent failures from breaking app
- Simplified all config file comments to be shorter and more understandable

### Fixed
- Email template variable conflict ($message vs $userMessage)
- Register page role description not updating when switching from Teacher to Student
- Login page tab switching to Student when Teacher credentials failed
- Teachers could log in using Student tab (and vice versa) - now prevented with clear error messages
- Account deletion 419 CSRF error
- Password update double-hashing issue
- Back button access after logout
- Footer branding updated to professional copyright notice
- File download "site not available" error with improved path handling and error messages
- Created missing lessons storage directory
- PreventBackHistory middleware compatibility with file downloads (StreamedResponse)
- File downloads now preserve correct filename and extension (PDF, DOC, DOCX, images)
- Added proper MIME types and Content-Disposition headers to prevent browser security blocks
- Implemented BinaryFileResponse with security headers (X-Content-Type-Options, Content-Security-Policy)
- Automatic filename extension detection and enforcement
- Demo account info removed from login form

### Security
- Added server-side role validation on login
- Implemented PreventBackHistory middleware for authenticated routes
- Users are now logged out immediately if they try to access wrong role
- Email sending failures logged but don't expose sensitive info to users

### Technical Notes
- **Email Configuration:** By default, `MAIL_MAILER=log` - emails saved to `storage/logs/laravel.log`
- To send real emails, configure SMTP in .env (see .env.example for Gmail setup)
- Use `php test-email.php` to test email configuration

---

## [1.0.0] - 2025-11-26

### Initial Release

This is the first stable release of Mini LMS with all core features implemented and tested.

### Added

#### Core Features
- Complete authentication system using Laravel Breeze
- Role-based access control (Teacher and Student roles)
- User profile management with image upload
- Account deletion with automatic file cleanup
- Email verification support

#### Course Management
- Full CRUD operations for courses (teachers only)
- Course publishing/unpublishing workflow
- Draft mode for courses under development
- Course thumbnails with public storage
- Course categorization system
- Skill tagging with automatic color inheritance
- Three difficulty levels (Beginner, Intermediate, Advanced)
- Teacher can only manage their own courses

#### Lesson System
- Complete lesson management within courses
- Rich text content support
- Lesson ordering system
- Duration tracking (in minutes)
- Private file attachments with custom names
- Secure file download with authorization
- Files stored outside public directory

#### Progress Tracking
- Automatic enrollment system
- Real-time progress calculation
- JSON-based viewed lessons tracking
- Completion status indicators
- Continue learning functionality
- Resume from last viewed lesson
- Recently viewed lessons history (last 5)

#### Category and Skills
- Category management with color coding
- Skills inherit colors from categories
- Color-coded skill badges
- Text color auto-adjustment for readability
- Category-based course organization
- Pre-seeded categories (Programming, Math, Business, Design)

#### Messaging System
- User-to-user messaging
- Inbox and Sent folders
- Message creation and replies
- Conversation threading
- Read/unread status tracking
- Authorization checks (can only view own messages)

#### Dashboard
- **Student Dashboard:**
  - Enrolled courses with progress
  - Continue learning section
  - Suggested courses by category (6 newest)
  - Recently viewed lessons
  - Achievement tracking
  - Quick enrollment
- **Teacher Dashboard:**
  - Own courses overview
  - Student enrollment counts
  - Recent student activity feed
  - Progress monitoring
  - Course management shortcuts

#### Email Notifications
- Teacher receives email when student enrolls
- Configurable SMTP settings
- Mailable classes for clean email templates
- Environment-based mail configuration

#### Security
- Role-based middleware (ensureTeacher, ensureStudent)
- Route protection
- File access authorization
- Private file storage
- CSRF protection
- Password hashing
- Session management

#### UI/UX
- Responsive design with TailwindCSS
- Modern, clean interface
- Alpine.js for interactive components
- Modal dialogs with proper z-index handling
- Card-based layouts with consistent heights
- Color-coded categories and skills
- Hover effects and transitions
- Mobile-friendly navigation

#### Developer Tools
- Laravel Debugbar integration (dev only)
- Comprehensive error logging
- Clear cache commands
- Storage linking
- Migration and seeding system

### Fixed

#### Critical Bugs (15 Total)

**Database Errors**
- NOT NULL constraint on skills.color_code
- Missing category colors causing black skill badges
- Missing skills on existing courses

**Route Errors**
- Route name mismatch (messages.new vs messages.create)
- 404 errors from cached routes

**UI/Display Issues**
- Delete account modal too dark (opacity reduced from 75% to 40%)
- Modal z-index preventing input interaction
- Modal auto-closing on overlay click
- Password input visibility in delete modal
- Course cards misalignment with varying content heights
- Button text for completed courses (now shows "Review")
- Favicon not updating (added cache busting)

**Configuration Issues**
- .env file parsing error with unquoted spaces
- APP_NAME requiring quotes for multi-word values

**Feature Issues**
- Email notifications not working (SMTP configuration)
- "New Courses" showing 2 per category instead of 6 newest overall
- Profile management missing features
- Lesson file upload missing name field
- Account deletion leaving orphaned files

**Data Issues**
- Skills without color codes
- Categories without color codes
- Courses without skills attached

### Changed

#### Improvements
- Enhanced course display algorithm for "New Courses" section
- Improved modal overlay darkness (75% to 40%)
- Better z-index layering in modals
- Consistent card heights in grid layouts
- Better color contrast in UI elements
- Optimized database queries with eager loading
- Improved file naming for lesson attachments

#### Refactoring
- Separated profile update into name and email forms
- Moved file cleanup logic to ProfileController
- Centralized color inheritance in CourseController
- Standardized route naming conventions

### Database Changes

#### New Migrations
1. `add_viewed_lessons_to_enrollments_table` - JSON column for lesson tracking
2. `add_user_id_to_categories_table` - Track category creators
3. `add_attachment_to_lessons_table` - File upload support
4. `create_messages_table` - Messaging system
5. `create_skills_table` - Skill management
6. `create_course_skill_table` - Many-to-many relationship
7. `add_color_to_categories_table` - Color coding
8. `add_is_published_to_courses_table` - Publishing workflow
9. `add_attachment_name_to_lessons_table` - Custom file names
10. `add_profile_image_to_users_table` - Profile pictures
11. `add_read_at_to_messages_table` - Message read tracking

#### New Seeders
1. `DatabaseSeeder` - Main seeder with sample data
2. `UpdateCategoryColorsSeeder` - Fix category colors
3. `UpdateSkillColorsSeeder` - Fix skill colors
4. `AddSkillsToExistingCoursesSeeder` - Add skills to 19 courses

### Technical Details

#### Models
- User (with role support)
- Category
- Course
- Lesson
- Enrollment
- Message
- Skill

#### Controllers
- CategoryController (full CRUD)
- CourseController (full CRUD with publish/unpublish)
- DashboardController (role-based views)
- EnrollmentController (enrollment logic)
- FileController (secure downloads)
- LessonController (full CRUD)
- MessageController (messaging system)
- ProfileController (profile management)
- SkillController (full CRUD)

#### Middleware
- EnsureTeacher (teacher-only routes)
- EnsureStudent (student-only routes)

#### Mail
- StudentJoinedCourse (enrollment notification)

### Documentation

#### Created Files
- README.md - Complete project documentation (270 lines)
- PROJECT_ERRORS_AND_SOLUTIONS.md - Error log with 15 fixes (769 lines)
- TODO.md - Development roadmap
- CHANGELOG.md - Version history (this file)

#### Documentation Sections
- Installation guide
- Feature list
- Database schema
- Common problems and solutions
- Production deployment guide
- Troubleshooting guide
- Project structure
- Technical implementations

### Known Limitations

- Email requires manual SMTP configuration
- No course search functionality (planned for v1.1)
- No rating/review system (planned for v1.1)
- No quiz/assignment system (planned for v1.2)
- Single language only (English)

### Dependencies

#### PHP Packages
- laravel/framework: ^12.0
- laravel/breeze: ^2.0
- barryvdh/laravel-debugbar: ^3.13 (dev)

#### JavaScript Packages
- tailwindcss: ^3.4
- alpinejs: ^3.14
- vite: ^5.0

### Deployment

- Tested on PHP 8.2
- Compatible with SQLite and MySQL
- Supports Laravel Herd, php artisan serve, XAMPP, MAMP
- Production-ready with caching support

### Statistics

- 10 Controllers
- 7 Models
- 17 Migrations
- 4 Seeders
- 2 Middleware
- 1 Mail class
- 50+ Blade views
- 79 Routes
- 15 Bugs fixed
- 270 Lines README
- 769 Lines error documentation

---

## [Unreleased]

### Planned for v1.1.0

- Search functionality for courses
- Course reviews and ratings
- Admin role and dashboard
- Notification system
- Course certificates (PDF)
- Video lesson support

### Planned for v1.2.0

- Quiz system
- Assignment/homework feature
- Discussion forums
- Advanced analytics
- Calendar integration

---

## Version History

- **1.0.0** (2025-11-26) - Initial stable release
- **0.9.0** (2025-11-25) - Beta release with all features
- **0.5.0** (2025-11-20) - Alpha release with core features
- **0.1.0** (2025-11-18) - Initial development

---

**Maintained by:** Renn-Amm  
**Repository:** Renn-Amm/Learning-Management-System  
**Last Updated:** November 26, 2025
