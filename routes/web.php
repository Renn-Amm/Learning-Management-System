<?php

use App\Http\Controllers\CategoryController;
use App\Http\Controllers\CourseController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\EnrollmentController;
use App\Http\Controllers\FileController;
use App\Http\Controllers\LessonController;
use App\Http\Controllers\MessageController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\SkillController;
use Illuminate\Support\Facades\Route;

// Cached welcome page - cache for 1 hour
Route::get('/', function () {
    return response()
        ->view('welcome-lms')
        ->header('Cache-Control', 'public, max-age=3600'); // Cache for 1 hour
})->name('welcome');

Route::middleware(['auth'])->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::patch('/profile/name', [ProfileController::class, 'updateName'])->name('profile.updateName');
    Route::post('/profile/image', [ProfileController::class, 'updateProfileImage'])->name('profile.updateImage');
    Route::delete('/profile/image', [ProfileController::class, 'deleteProfileImage'])->name('profile.deleteImage');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    // Teacher routes MUST come BEFORE the resource routes
    Route::middleware(['ensureTeacher'])->group(function () {
        Route::get('/courses/create', [CourseController::class, 'create'])->name('courses.create');
        Route::post('/courses', [CourseController::class, 'store'])->name('courses.store');
        Route::get('/courses/{course}/edit', [CourseController::class, 'edit'])->name('courses.edit');
        Route::put('/courses/{course}', [CourseController::class, 'update'])->name('courses.update');
        Route::delete('/courses/{course}', [CourseController::class, 'destroy'])->name('courses.destroy');
        
        // Non-CRUD actions for courses
        Route::patch('/courses/{course}/publish', [CourseController::class, 'publish'])->name('courses.publish');
        Route::patch('/courses/{course}/unpublish', [CourseController::class, 'unpublish'])->name('courses.unpublish');

        Route::get('/courses/{course}/lessons/create', [LessonController::class, 'create'])->name('lessons.create');
        Route::post('/courses/{course}/lessons', [LessonController::class, 'store'])->name('lessons.store');
        Route::get('/lessons/{lesson}/edit', [LessonController::class, 'edit'])->name('lessons.edit');
        Route::put('/lessons/{lesson}', [LessonController::class, 'update'])->name('lessons.update');
        Route::delete('/lessons/{lesson}', [LessonController::class, 'destroy'])->name('lessons.destroy');

        Route::resource('categories', CategoryController::class);
        Route::resource('skills', SkillController::class);
    });

    // Messages (accessible by all authenticated users)
    Route::get('/messages', [MessageController::class, 'index'])->name('messages.index');
    Route::get('/messages/create', [MessageController::class, 'create'])->name('messages.create');
    Route::delete('/messages/conversation/{user}', [MessageController::class, 'deleteConversation'])->name('messages.conversation.delete');
    Route::get('/messages/{user}', [MessageController::class, 'conversation'])->name('messages.conversation');
    Route::post('/messages/{user}', [MessageController::class, 'store'])->name('messages.store');

    // Secure file downloads
    Route::get('/download/{file}', [FileController::class, 'download'])->name('file.download');

    // Public course routes (after teacher routes to avoid conflicts)
    Route::resource('courses', CourseController::class)->only(['index', 'show']);

    Route::get('/courses/{course}/lessons', [LessonController::class, 'index'])->name('lessons.index');
    Route::get('/lessons/{lesson}', [LessonController::class, 'show'])->name('lessons.show');

    Route::middleware(['ensureStudent'])->group(function () {
        Route::post('/courses/{course}/enroll', [EnrollmentController::class, 'enroll'])->name('courses.enroll');
        Route::post('/lessons/{lesson}/mark-done', [LessonController::class, 'markDone'])->name('lessons.markDone');
    });
});

require __DIR__.'/auth.php';
