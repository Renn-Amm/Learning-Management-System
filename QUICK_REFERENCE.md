# Quick Reference Guide - Mini LMS

## For Students

### Browse Courses
1. Login as student
2. Click "Browse Courses" (black button)
3. Filter by category if needed

### Course Actions
**Not Enrolled:**
- See two buttons: "View Details" (white) + "Enroll" (black)
- Click "View Details" → See overview (description, teacher, duration, lesson titles)
- Click "Enroll" → Instantly enrolled

**Enrolled:**
- See "✓ Enrolled" badge
- One button: "View Course" (black)
- Access all lessons with full content
- Progress tracked automatically

### Viewing Lessons
- Click on any lesson → Progress updates automatically
- Complete all lessons → Course marked complete
- Cannot manually edit progress

## For Teachers

### Create Course
1. Dashboard → "Create New Course"
2. Fill form (title, description, category, level, thumbnail)
3. Submit → Redirected to course page

### Add Lessons
1. On course page → "Add Lesson"
2. Fill: Title, Content, Order Number (must be unique!), Duration
3. Submit → Lesson added
4. Repeat for more lessons

### Manage Courses
- Edit: Only YOUR courses
- Delete: Only YOUR courses
- View student progress on your course pages

### Categories
- Create: Any teacher can create
- View: All teachers see all categories
- Edit: Only YOUR categories
- Delete: Only YOUR categories (if no courses)

## Authorization Rules

### Can Edit/Delete
✅ Own courses
✅ Own categories
✅ Own lessons (in own courses)

### Cannot Edit/Delete
❌ Other teachers' courses
❌ Other teachers' categories
❌ Other teachers' lessons
❌ System categories (no creator)

## Course Creation Issue?

Run these commands:
```bash
php artisan route:clear
php artisan view:clear
php artisan config:clear
```

Then try again.

## Demo Accounts

**Student:**
- Email: student1@example.com
- Password: password

**Teacher:**
- Email: teacher@example.com
- Password: password

## Key Features

### Auto Progress
- Progress updates when viewing lessons
- Calculated: (viewed lessons / total lessons) × 100
- Course completed when all lessons viewed

### Unique Lesson Orders
- Each lesson needs unique order number per course
- Course A can have Lesson 1, Course B can also have Lesson 1
- But same course cannot have two Lesson 1s

### Black & White Design
- All buttons: Black or White
- All text: Black
- All backgrounds: White
- All borders: Black
- Progress bars: Black fill on white
- No colors except black and white

## Common Questions

**Q: Why can't I create a course?**
A: Clear caches (see commands above) and make sure you're logged in as teacher.

**Q: Why can't I edit a category?**
A: You can only edit categories YOU created. System categories cannot be edited.

**Q: Why can't I use the same lesson order number?**
A: Each lesson in a course must have a unique order number. Use 1, 2, 3, 4, etc.

**Q: How do students know they're enrolled?**
A: They see "✓ Enrolled" badge and "View Course" button instead of "View Details" + "Enroll".

**Q: Can students see lesson content before enrolling?**
A: No, they only see lesson titles and durations (overview). Must enroll to access content.

## File Structure

```
mini-lms/
├── app/
│   ├── Http/Controllers/
│   │   ├── CourseController.php
│   │   ├── CategoryController.php
│   │   ├── LessonController.php
│   │   └── DashboardController.php
│   └── Models/
│       ├── Course.php
│       ├── Category.php
│       ├── Lesson.php
│       └── User.php
├── resources/views/
│   ├── courses/
│   │   ├── index.blade.php (browse)
│   │   ├── show.blade.php (details)
│   │   ├── create.blade.php
│   │   └── edit.blade.php
│   ├── categories/
│   │   └── index.blade.php
│   └── dashboard/
│       ├── teacher.blade.php
│       └── student.blade.php
└── database/
    └── migrations/
        └── 2025_11_20_125928_add_user_id_to_categories_table.php
```

## Version History

- **v1.2.0** (Nov 20, 2025) - Two-button system, category authorization, complete black/white design
- **v1.1.0** (Nov 19, 2025) - Registration/login role selection, auto progress
- **v1.0.0** (Nov 18, 2025) - Initial release

## Need Help?

Check these files:
- `FINAL_COMPLETE_FIXES_NOV20.md` - Detailed explanation of all fixes
- `CHANGELOG.md` - Version history
- `README.md` - Full documentation
