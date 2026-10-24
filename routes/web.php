<?php

use App\Http\Controllers\Admin;
use App\Http\Controllers\AnnouncementController;
use App\Http\Controllers\CourseController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\EnrollmentController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\LearningController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\ReviewController;
use Illuminate\Support\Facades\Route;

Route::get('/', HomeController::class)->name('home');
Route::get('/khoa-hoc', [CourseController::class, 'index'])->name('courses.index');
Route::get('/khoa-hoc/{course}', [CourseController::class, 'show'])->name('courses.show');
Route::get('/thong-bao', [AnnouncementController::class, 'index'])->name('announcements.index');
Route::get('/thong-bao/{announcement}', [AnnouncementController::class, 'show'])->name('announcements.show');

Route::middleware(['auth', 'verified'])->group(function () {
    Route::get('/dashboard', DashboardController::class)->name('dashboard');

    Route::post('/khoa-hoc/{course}/ghi-danh', [EnrollmentController::class, 'store'])->name('enrollments.store');
    Route::delete('/khoa-hoc/{course}/ghi-danh', [EnrollmentController::class, 'destroy'])->name('enrollments.destroy');
    Route::post('/khoa-hoc/{course}/danh-gia', [ReviewController::class, 'store'])->name('reviews.store');

    Route::get('/hoc/{course}/{lesson?}', [LearningController::class, 'show'])->name('learn.show');
    Route::post('/hoc/{course}/{lesson}/hoan-thanh', [LearningController::class, 'complete'])->name('learn.complete');
});

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

Route::prefix('admin')->name('admin.')->middleware(['auth', 'role:admin,teacher'])->group(function () {
    Route::get('/', Admin\DashboardController::class)->name('dashboard');

    Route::resource('courses', Admin\CourseController::class)->except('show');
    Route::resource('courses.lessons', Admin\LessonController::class)->except('show')->scoped(['course' => 'slug', 'lesson' => 'id']);

    Route::get('reviews', [Admin\ReviewController::class, 'index'])->name('reviews.index');
    Route::patch('reviews/{review}', [Admin\ReviewController::class, 'update'])->name('reviews.update');
    Route::delete('reviews/{review}', [Admin\ReviewController::class, 'destroy'])->name('reviews.destroy');

    Route::get('enrollments', [Admin\EnrollmentController::class, 'index'])->name('enrollments.index');
    Route::delete('enrollments/{enrollment}', [Admin\EnrollmentController::class, 'destroy'])->name('enrollments.destroy');

    Route::post('editor-upload', Admin\EditorUploadController::class)->name('editor.upload');

    Route::middleware('role:admin')->group(function () {
        Route::resource('categories', Admin\CategoryController::class)->except('show');
        Route::resource('users', Admin\UserController::class)->except('show');
        Route::resource('announcements', Admin\AnnouncementController::class)->except('show');
    });
});

require __DIR__.'/auth.php';
