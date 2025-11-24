# Mini LMS — Complete Project Documentation

A structured Learning Management System built with **Laravel** and **Breeze**. It provides two roles, **Teacher** and **Student**, and includes course creation, lesson management, progress tracking, private file handling, messaging, categories, skills, and dashboards tailored for each role.

---

## 1. Project Description

This Mini LMS provides a simple and organized environment for online learning. Teachers can create courses, add lessons, assign each course a category and level, attach skills, and monitor student progress. Students can browse courses, enroll, learn at their own pace, access lessons, track their progress automatically, and communicate with teachers through a built-in messaging system.

Courses belong to categories such as *Programming*, *Math*, *Business*, and *Design*. Skills inherit the color of the category they belong to, ensuring consistent labeling. Files uploaded for lessons are stored privately and cannot be accessed from public URLs. Each dashboard displays personalized, relevant data.

---

## 2. Requirements

### Software Needed
- PHP **8.2 or higher**
- Composer
- Node.js and NPM
- Laravel **12.x**
- SQLite (default) or MySQL
- Git
- Local server:
  - Laravel Herd
  - `php artisan serve`
  - XAMPP / MAMP

### Laravel Packages Used
- Laravel Breeze
- Laravel Debugbar
- TailwindCSS + Alpine.js

---

## 3. Installation Guide

Run all commands inside your project folder.

### Step 1: Install Dependencies
```bash
composer install
npm install
```

### Step 2: Environment Setup
```bash
cp .env.example .env
php artisan key:generate
```

### Note: need to run npm run dev before running Herd because without running npm run dev first the web will show errors.

### Step 3: Database Setup

#### SQLite  
No configuration required.

#### MySQL Example  
Update `.env`:
\`\`\`env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_DATABASE=mini_lms
DB_USERNAME=root
DB_PASSWORD=yourpassword
\`\`\`

### Step 4: Run Migrations and Seeders
```bash
php artisan migrate:fresh --seed
```

Populates:
- 4 categories  
- 20 demo courses  
- Demo teacher + students  
- Sample lessons  

### Step 5: Link Storage
```bash
php artisan storage:link
```

### Step 6: Start App
```bash
npm run dev
php artisan serve
```

---

## 4. Demo Accounts

### Teacher
- Email: `teacher@example.com`
- Password: `password`

### Student
- Email: `student1@example.com`
- Password: `password`

---

## 5. Main Features

### Student Features
- Browse all courses  
- Enroll and continue learning  
- Auto-progress tracking  
- Suggested courses by category  
- Recently viewed lessons  
- Achievements  
- Messaging with teachers  
- Private file downloads  

### Teacher Features
- Create, edit and delete **their own courses**  
- Add lessons with content, ordering and private files  
- Skills inherit category color  
- Student progress overview  
- Recent student activity feed  
- Email when a student enrolls  
- Messaging system  

---

## 6. Fixes and Error Solutions

### A. Private File Storage
**Problem:** Direct file access possible.  
**Fix:**  
- Files stored in `storage/app/private`  
- Secure download route  
- Permission checks  
- Block public URL access  

---

### B. Dashboard Fixes

#### Teacher
- Show only teacher’s own courses  
- Added student progress summary  
- Added recent student activity (enrollments, lesson completions)  

#### Student
- Correctly loads enrolled courses  
- Continue Learning button fixed  
- Suggested courses exclude enrolled  
- Recent lessons stored and displayed  
- Achievements tracked  

---

### C. Skill Color Inheritance
- Category defines color  
- Skills inherit color automatically  
- Text color adjusts based on brightness  

---

### D. Messaging Fixes
- Users see only messages they sent or received  
- Added `from_id` / `to_id` authorization  
- Inbox + Sent separated  

---

### E. Authentication / Roles
- `ensureTeacher` and `ensureStudent` middleware added  
- Route protection fixed  

---

### F. Migration & Relationship Fixes
- Fixed foreign key order  
- Added missing constraints  
- Added JSON column for viewed lessons  

---

### G. Debugbar
Installed using:
```bash
composer require barryvdh/laravel-debugbar --dev
```

Used for:
- route debugging  
- queries  
- view rendering  

---

### H. Cache Fixes
Common commands used:
```bash
php artisan route:clear
php artisan config:clear
php artisan view:clear
php artisan cache:clear
```

---

## 7. Common Problems & Solutions

| Issue | Solution |
|-------|----------|
| Storage not working | `php artisan storage:link` |
| Missing key | `php artisan key:generate` |
| Vite not loading assets | `npm run dev` |
| Teacher role missing | Assign via Tinker |
| 404 errors | Clear route/view caches |

---

## 8. Database Schema Overview

### users
- id  
- name  
- email  
- password  
- role  

### categories
- id  
- name  
- color  

### courses
- id  
- title  
- description  
- level  
- teacher_id  
- category_id  

### lessons
- id  
- course_id  
- title  
- content  
- order  
- duration  

### enrollments
- id  
- user_id  
- course_id  
- progress  
- is_completed  
- viewed_lessons (JSON)  

### messages
- id  
- from_id  
- to_id  
- title  
- subject  
- message_text  

---

## 9. Production Build

```bash
npm run build
php artisan config:cache
php artisan route:cache
php artisan view:cache
```

Update `.env`:
\`\`\`env
APP_ENV=production
APP_DEBUG=false
\`\`\`
