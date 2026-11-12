<?php

use App\Http\Controllers\Admin\BannerController;
use App\Http\Controllers\Admin\CategoryController;
use App\Http\Controllers\Admin\CloudinaryController;
use App\Http\Controllers\Admin\CourseController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\EnrollmentController;
use App\Http\Controllers\Admin\FeedbackController;
use App\Http\Controllers\Admin\InstructorController;
use App\Http\Controllers\Admin\NotificationController;
use App\Http\Controllers\Admin\PayoutController;
use App\Http\Controllers\Admin\ReviewController;
use App\Http\Controllers\Admin\UserController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Admin Routes (Thành viên 6)
|--------------------------------------------------------------------------
| Tất cả routes trong file này tự động:
| - Prefix: /admin
| - Name prefix: admin.
| - Middleware: ['web', 'auth', 'role:admin'] (cấu hình tại bootstrap/app.php)
*/

// Dashboard
Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

// Users
Route::get('/users', [UserController::class, 'index'])->name('users.index');
Route::get('/users/{user}/edit', [UserController::class, 'edit'])->name('users.edit');
Route::put('/users/{user}', [UserController::class, 'update'])->name('users.update');
Route::delete('/users/{user}', [UserController::class, 'destroy'])->name('users.destroy');
Route::patch('/users/{user}/role', [UserController::class, 'updateRole'])->name('users.update-role');
Route::patch('/users/{user}/toggle-status', [UserController::class, 'toggleStatus'])->name('users.toggle-status');

// Tạo tài khoản giảng viên
Route::get('/instructors/create', [InstructorController::class, 'create'])->name('instructors.create');
Route::post('/instructors', [InstructorController::class, 'store'])->name('instructors.store');

// Quản lý & Duyệt Khóa học
Route::get('/courses', [CourseController::class, 'index'])->name('courses.index');
Route::patch('/courses/{course}/approve', [CourseController::class, 'approve'])->name('courses.approve');
Route::patch('/courses/{course}/reject', [CourseController::class, 'reject'])->name('courses.reject');
Route::patch('/courses/{course}/hide', [CourseController::class, 'hide'])->name('courses.hide');
Route::delete('/courses/{course}', [CourseController::class, 'destroy'])->name('courses.destroy');

// Danh mục
Route::resource('categories', CategoryController::class);

// Lượt ghi danh
Route::get('/enrollments', [EnrollmentController::class, 'index'])->name('enrollments.index');
Route::delete('/enrollments/{enrollment}', [EnrollmentController::class, 'destroy'])->name('enrollments.destroy');

// Kiểm duyệt Đánh giá & Bình luận
Route::get('/reviews', [ReviewController::class, 'index'])->name('reviews.index');
Route::delete('/reviews/{review}', [ReviewController::class, 'destroy'])->name('reviews.destroy');
Route::delete('/comments/{comment}', [ReviewController::class, 'destroyComment'])->name('reviews.destroy-comment');

// Thông báo hệ thống
Route::resource('notifications', NotificationController::class);

// Banners
Route::resource('banners', BannerController::class);
Route::patch('/banners/{banner}/toggle', [BannerController::class, 'toggle'])->name('banners.toggle');

// Thư viện ảnh đã lưu trên Cloudinary
Route::get('/cloudinary', [CloudinaryController::class, 'index'])->name('cloudinary.index');
Route::post('/cloudinary/assign', [CloudinaryController::class, 'assign'])->name('cloudinary.assign');

// Phản hồi & Báo cáo sự cố
Route::get('/feedbacks', [FeedbackController::class, 'index'])->name('feedbacks.index');
Route::get('/feedbacks/{feedback}', [FeedbackController::class, 'show'])->name('feedbacks.show');
Route::patch('/feedbacks/{feedback}', [FeedbackController::class, 'update'])->name('feedbacks.update');
Route::delete('/feedbacks/{feedback}', [FeedbackController::class, 'destroy'])->name('feedbacks.destroy');

// Quản lý Chi trả (Payouts) cho Giảng viên (Tỉ lệ 70% Giảng viên - 30% Sàn)
Route::get('/payouts', [PayoutController::class, 'index'])->name('payouts.index');
Route::patch('/payouts/{payout}/approve', [PayoutController::class, 'approve'])->name('payouts.approve');
Route::patch('/payouts/{payout}/reject', [PayoutController::class, 'reject'])->name('payouts.reject');
