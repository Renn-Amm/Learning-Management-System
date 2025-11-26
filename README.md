# Mini Learning Management System

This LMS provides two user roles, Teacher and Student. Teachers can create courses, add lessons, assign categories and skills, upload private lesson files, publish or unpublish courses, and track student progress. Students can browse available courses, enroll instantly, view lessons, have their progress update automatically, see recently viewed lessons, receive course suggestions, and message teachers. The system includes named routes, caching on key pages, private storage for files, and full authorization to ensure each user only accesses the correct content.

## Requirements
This project uses:

- Laravel 12  
- Laravel Breeze for authentication  
- Tailwind CSS and Alpine.js via CDN (no Vite)  
- SQLite by default, with optional MySQL support  
- Laravel Debugbar in development

## Environment Variables
Only standard Laravel environment variables are required  
(APP_NAME, APP_URL, APP_KEY, database settings).  
Everything is documented in `.env.example`.

## Development Installation

1. Create a project folder and open it in your terminal.  
2. Clone the repository:  
   `git clone https://github.com/Renn-Amm/Learning-Management-System.git .`  
3. Install dependencies:  
   `composer install`  
4. Create your environment file:  
   `cp .env.example .env`  
5. Generate the app key:  
   `php artisan key:generate`  
6. If using SQLite:  
   `touch database/database.sqlite`  
7. Run migrations:  
   `php artisan migrate`  
8. Seed demo data:  
   `php artisan db:seed`  
9. Link storage:  
   `php artisan storage:link`  
10. Start the server:  
    `php artisan serve`

Visit: http://localhost:8000

## Demo Accounts

### Teacher
Email: teacher@example.com  
Password: password

### Student
Email: student1@example.com  
Password: password


Update `.env`:
```env
APP_ENV=production
APP_DEBUG=false
```
