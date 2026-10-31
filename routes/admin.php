<?php

use App\Http\Controllers\Admin\InstructorController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Admin Routes (Dành riêng cho Thành viên 6)
|--------------------------------------------------------------------------
| Tất cả các route trong file này tự động:
| - Có tiền tố: /admin (ví dụ: /admin/dashboard)
| - Có name prefix: admin. (ví dụ: route('admin.dashboard'))
| - Đã được bảo vệ qua middleware: ['web', 'auth', 'role:admin']
*/

Route::get('/dashboard', function () {
    return view('admin.dashboard');
})->name('dashboard');

Route::get('/instructors/create', [InstructorController::class, 'create'])->name('instructors.create');
Route::post('/instructors', [InstructorController::class, 'store'])->name('instructors.store');

// TV6 sẽ tiếp tục khai báo các Controller và routes cho:
// - DashboardController (Thống kê Chart.js)
// - UserController (Quản lý User & phân vai trò)
// - CourseApprovalController (Duyệt/từ chối/khóa khóa học)
// - CategoryController (CRUD danh mục)
// - ReviewModerationController (Xóa đánh giá/bình luận vi phạm)
// - EnrollmentController (Quản lý lượt ghi danh)
// - NotificationController (CRUD thông báo hệ thống)
// - BannerController (CRUD banner trang chủ)
