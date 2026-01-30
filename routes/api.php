<?php

use App\Http\Controllers\Api\CourseApiController;
use App\Http\Controllers\HealthCheckController;
use Illuminate\Support\Facades\Route;

Route::get('/health', [HealthCheckController::class, 'index'])->name('api.health');

Route::prefix('v1')->group(function () {
    Route::get('/courses', [CourseApiController::class, 'index'])->name('api.courses.index');
    Route::get('/courses/{course}', [CourseApiController::class, 'show'])->name('api.courses.show');

    Route::middleware('auth:sanctum')->group(function () {
        Route::post('/courses', [CourseApiController::class, 'store'])->name('api.courses.store');
        Route::put('/courses/{course}', [CourseApiController::class, 'update'])->name('api.courses.update');
        Route::delete('/courses/{course}', [CourseApiController::class, 'destroy'])->name('api.courses.destroy');
    });
});
