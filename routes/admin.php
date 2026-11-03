<?php

use App\Http\Controllers\Admin\BannerController;
use App\Http\Controllers\Admin\CategoryController;
use App\Http\Controllers\Admin\CourseController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\EnrollmentController;
use App\Http\Controllers\Admin\InstructorController;
use App\Http\Controllers\Admin\NotificationController;
use App\Http\Controllers\Admin\ReviewController;
use App\Http\Controllers\Admin\UserController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Admin Routes (Dành riêng cho Thành viên 6)
|--------------------------------------------------------------------------
| Tất cả route trong file này tự động:
| - Có tiền tố: /admin (ví dụ: /admin/dashboard)
| - Có name prefix: admin. (ví dụ: route('admin.dashboard'))
| - Đã được bảo vệ qua middleware: ['web', 'auth', 'role:admin']
*/

// Dashboard thống kê (Chart.js)
Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

// Tạo tài khoản giảng viên
Route::get('/instructors/create', [InstructorController::class, 'create'])->name('instructors.create');
Route::post('/instructors', [InstructorController::class, 'store'])->name('instructors.store');

// Quản lý người dùng: tìm kiếm/lọc (?keyword=&role=&status=), đổi role, khóa/mở khóa
Route::get('/users', [UserController::class, 'index'])->name('users.index');
Route::patch('/users/{user}/role', [UserController::class, 'updateRole'])->name('users.role');
Route::patch('/users/{user}/status', [UserController::class, 'toggleStatus'])->name('users.status');

// Quản lý danh mục (CRUD)
Route::resource('categories', CategoryController::class)->except(['show']);

// Quản lý & duyệt khóa học (pending -> published / rejected)
Route::get('/courses', [CourseController::class, 'index'])->name('courses.index');
Route::patch('/courses/{course}/approve', [CourseController::class, 'approve'])->name('courses.approve');
Route::patch('/courses/{course}/reject', [CourseController::class, 'reject'])->name('courses.reject');
Route::delete('/courses/{course}', [CourseController::class, 'destroy'])->name('courses.destroy');

// Kiểm duyệt đánh giá & bình luận vi phạm
Route::get('/reviews', [ReviewController::class, 'index'])->name('reviews.index');
Route::delete('/reviews/{review}', [ReviewController::class, 'destroy'])->name('reviews.destroy');
Route::delete('/reviews/comments/{comment}', [ReviewController::class, 'destroyComment'])->name('reviews.comments.destroy');

// Quản lý lượt ghi danh
Route::get('/enrollments', [EnrollmentController::class, 'index'])->name('enrollments.index');
Route::delete('/enrollments/{enrollment}', [EnrollmentController::class, 'destroy'])->name('enrollments.destroy');

// Thông báo hệ thống (CRUD)
Route::resource('notifications', NotificationController::class)->except(['show']);

// Banner trang chủ (CRUD)
Route::resource('banners', BannerController::class)->except(['show']);
