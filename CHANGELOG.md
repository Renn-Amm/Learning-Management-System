# Changelog

All notable changes to the Mini LMS project will be documented in this file.

## [1.5.1] - 2025-11-24 (Latest)

### Added
- **Auto-Color Generation for Categories:** New categories automatically get unique colors from a pool of 25 vibrant colors
- **Search Functionality:** Search courses by title, description, category name, or skill names
- **Search + Filter Combination:** Search and category filter work together seamlessly

### Improved
- **Category Creation:** No need to manually choose colors anymore
- **Course Discovery:** Faster course finding with powerful search
- **User Experience:** Clear button to reset search, search preserved when filtering

### Verified
- **Student File Downloads:** Confirmed working as implemented in v1.4.0

## [1.5.0] - 2025-11-24

### Added
- **Category Fixed Colors:** Each category now has a fixed color (Programming: Blue, Math: Green, Business: Pink, Design: Yellow)
- **Skills Inherit Category Colors:** Skills automatically use their course's category color
- **Enhanced Teacher Dashboard:** Added Recent Student Activity and Student Progress Summary sections
- **Enhanced Student Dashboard:** Added Continue button, Suggested Courses by category, Recent Lessons, and Achievements
- **Conversation-Based Messaging:** Redesigned messaging to WhatsApp-style back-to-back chat
- **Inline Skills Creation:** Skills created directly in course forms (comma-separated)

### Changed
- **Skills System:** Removed standalone skills management page, integrated into course creation
- **Messaging UI:** Changed from inbox/sent/compose to conversation-based interface
- **Student Dashboard:** Complete redesign with progress tracking and suggestions
- **Teacher Dashboard:** Added activity feed and progress analytics
- **Skills Display:** Skills now show with category color on all course views
- **Continue Learning:** Smart button that finds next unfinished lesson

### Improved
- **Dashboard Performance:** Optimized queries with eager loading and proper relationships
- **Styling Consistency:** Strict black and white theme across all pages (except skill tags)
- **Text Contrast:** Automatic text color adjustment for readability on colored backgrounds
- **Data Isolation:** Teachers see only their data, students see only their data

### Database
- Added `color_code` column to categories table
- Set fixed colors for existing categories (Programming, Math, Business, Design)

## [1.4.0] - 2025-11-24

### Added
- **Student-Teacher Chat System:** Complete messaging system with inbox, sent messages, compose, and message details
- **Skills with Color Highlighting:** Teachers can create skills with custom background colors and automatic text contrast
- **Private File Storage:** All lesson attachments now stored privately with secure download authorization
- **Laravel Debugbar:** Development debugging tool for query and performance monitoring
- **Email Notifications:** Teachers receive emails when students enroll in their courses
- **Messages Navigation:** New messages link in main navigation for all users
- **Skills Navigation:** New skills link for teachers to manage skills

### Changed
- **File Storage:** Moved from public to private disk for lesson attachments
- **Download Links:** Attachments now use secure download route with permission checks
- **Navigation:** Added Messages and Skills links to both desktop and mobile navigation
- **Course Forms:** Added skills selection checkboxes to create/edit course forms
- **Course Display:** Skills tags now displayed on course show page with colors

### Security
- **Directory Traversal Protection:** basename() prevents path manipulation attacks
- **Download Authorization:** Users can only download files they have permission to access
- **Message Privacy:** Users can only view messages they sent or received
- **Role-Based File Access:** Teachers access own course files, students access enrolled course files

## [1.3.0] - 2024-11-20

### Added
- **Lesson file attachments:** Teachers can upload images, PDFs, Word docs (max 10MB)
- **Manual progress tracking:** Students must click "Mark as Done" button
- Download button for lesson attachments
- File upload fields in lesson create/edit forms
- Completion status display for lessons
- Success/info messages after marking lesson done

### Changed
- **Progress system:** From automatic to manual (student-controlled)
- **Thumbnail validation:** Strictly images only (JPEG, PNG, GIF, WEBP)
- **Lesson attachments:** Accept images, PDF, DOC, DOCX files
- Students must actively mark lessons as complete
- All lesson form styling updated to black/white
- Lesson show page completely redesigned with attachment support

### Fixed
- File uploads properly validated and stored
- Old files deleted when uploading replacements
- Attachment storage path correctly configured
- Manual progress prevents accidental double-marking

## [1.2.0] - 2024-11-20

### Added
- Two-button system for students on course browse page: "View Details" + "Enroll"
- Different course views: Overview for non-enrolled, full access for enrolled students
- Lessons overview for non-enrolled students (titles and duration only)
- Total course duration display (sum of all lesson durations)
- Category creator tracking (user_id field in categories table)
- Category authorization - only creator can edit/delete their categories
- "Created by" label on categories showing who created them

### Changed
- Complete black and white color scheme across ALL pages
- Course show page now has conditional display based on enrollment status
- Non-enrolled students see lesson overview without access to content
- Enrolled students see full lessons with view buttons
- Categories index shows Edit/Delete buttons only for category creator
- All buttons, borders, and UI elements now strictly black and white
- Progress bars use black fill on white background with black borders

### Fixed
- All color inconsistencies removed (no more indigo, blue, or gray)
- Category edit/delete authorization properly enforced
- Course creation route verified and caches cleared
- System categories (no creator) protected from editing

## [1.1.0] - 2024-11-19

### Added
- Role selection during registration - users can now choose to register as Student or Teacher
- Login page role selector - separate buttons for "Login as Student" or "Login as Teacher"
- Auto-fill email based on login type selection
- Black and white color scheme applied to welcome page
- Teachers can now view detailed student progress on course pages
- Category filtering for students on courses page

### Changed
- Registration now accepts role field from form instead of defaulting to student
- Login page displays appropriate demo credentials based on selected role
- Welcome page completely redesigned with black and white colors only
- Course show page redesigned with student progress section for teachers
- Removed manual progress update functionality - progress is now automatic only

### Fixed
- Course creation 404 errors resolved
- Route and view cache issues
- Progress tracking now strictly automatic when viewing lessons

## [1.0.0] - 2024-11-18

### Added

**Database & Migrations:**
- Created users table with role column (student/teacher)
- Created categories table for course organization
- Created courses table with thumbnail, level, and foreign keys
- Created lessons table with order_number and duration fields
- Created enrollments table for progress tracking
- Added proper foreign key constraints and cascading deletes

**Models & Relationships:**
- User model with role-based helper methods (isTeacher, isStudent)
- Category model with courses relationship
- Course model with full relationships (teacher, category, lessons, students, enrollments)
- Lesson model with course relationship
- Enrollment model with user and course relationships
- Implemented belongsTo, hasMany, and belongsToMany relationships

**Authentication System:**
- Integrated Laravel Breeze authentication
- Registration with default student role
- Login with remember me functionality
- Password reset functionality
- Email verification support
- Profile management (edit, update, delete account)
- Session management

**Middleware:**
- EnsureTeacher middleware for teacher-only routes
- EnsureStudent middleware for student-only routes
- Middleware aliases registered in bootstrap/app.php

**Controllers:**
- DashboardController with role-based views
- CourseController with full CRUD operations
- LessonController with nested resource handling
- CategoryController with CRUD operations
- EnrollmentController for course enrollment and progress tracking
- ProfileController for user profile management
- Complete Auth controllers (Login, Register, Password Reset, etc.)

**Routes:**
- Public landing page route
- Authentication routes (register, login, logout, password reset)
- Protected dashboard route
- Course browsing routes (public for authenticated users)
- Teacher-protected routes for course/lesson/category management
- Student-protected routes for enrollment and progress
- Profile management routes

**Views - Layouts:**
- app.blade.php - Main application layout with navigation
- guest.blade.php - Guest layout for authentication pages
- navigation.blade.php - Responsive navigation component
- welcome-lms.blade.php - Custom landing page

**Views - Components:**
- nav-link - Navigation link component
- responsive-nav-link - Mobile navigation link
- dropdown - Dropdown menu component
- dropdown-link - Dropdown item component
- input-label - Form label component
- text-input - Form input component
- primary-button - Primary button component
- input-error - Form error display component
- modal - Modal dialog component

**Views - Authentication:**
- login.blade.php - Login form
- register.blade.php - Registration form
- forgot-password.blade.php - Password reset request
- reset-password.blade.php - Password reset form
- verify-email.blade.php - Email verification notice
- confirm-password.blade.php - Password confirmation

**Views - Dashboard:**
- teacher.blade.php - Teacher dashboard with course management
- student.blade.php - Student dashboard with enrolled courses and progress

**Views - Courses:**
- index.blade.php - Browse all courses with pagination
- show.blade.php - Course details with lessons list
- create.blade.php - Create new course form
- edit.blade.php - Edit course form

**Views - Lessons:**
- create.blade.php - Add lesson to course form
- edit.blade.php - Edit lesson form
- show.blade.php - View lesson content with navigation

**Views - Categories:**
- index.blade.php - List all categories
- create.blade.php - Create category form
- edit.blade.php - Edit category form
- show.blade.php - View category with courses

**Views - Profile:**
- edit.blade.php - Profile management page
- partials/update-profile-information-form.blade.php
- partials/update-password-form.blade.php
- partials/delete-user-form.blade.php

**Seeders:**
- DatabaseSeeder with comprehensive sample data
- 5 default categories
- 1 teacher account (teacher@example.com)
- 2 student accounts
- 4 sample courses with descriptions
- 12 lessons across all courses
- 4 enrollments with progress tracking

**Features:**
- Course thumbnail upload and storage
- Progress tracking (0-100%)
- Course completion status
- Role-based dashboards
- Responsive design with TailwindCSS
- Form validation on all inputs
- CSRF protection
- File upload handling
- Error messaging
- Success notifications
- Pagination for course listings

**Documentation:**
- Comprehensive README.md with installation guide
- Troubleshooting section
- Usage guide for teachers and students
- Route documentation
- Project structure overview
- Database setup instructions
- Default login credentials

### Technical Details

**Security:**
- CSRF token protection on all forms
- Password hashing with bcrypt
- Role-based access control
- SQL injection prevention through Eloquent ORM
- XSS protection via Blade templating

**Code Quality:**
- Clean MVC architecture
- No unused imports
- Consistent code formatting
- Meaningful variable names
- Proper error handling
- Validation on all forms
- No commented-out code
- No redundant functions

**Database Design:**
- Normalized database structure
- Proper indexing on foreign keys
- Unique constraints where needed
- Cascading deletes for data integrity
- Timestamps on all tables

**UI/UX:**
- Clean, modern interface
- Responsive mobile-first design
- Intuitive navigation
- Clear feedback messages
- Consistent styling
- Loading states
- Error states
- Empty states

## Future Versions

See TODO.md for planned features and improvements.
