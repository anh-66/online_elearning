<?php

use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Instructor Routes (Dành riêng cho Thành viên 5)
|--------------------------------------------------------------------------
| Tất cả các route trong file này tự động:
| - Có tiền tố: /instructor (ví dụ: /instructor/dashboard)
| - Có name prefix: instructor. (ví dụ: route('instructor.dashboard'))
| - Đã được bảo vệ qua middleware: ['web', 'auth', 'role:instructor,admin']
*/

Route::get('/dashboard', function () {
    return view('instructor.dashboard');
})->name('dashboard');

// TV5 sẽ tiếp tục khai báo các Controller và routes cho:
// - CourseController (CRUD khóa học)
// - ChapterController (CRUD chương)
// - LessonController (CRUD bài học)
// - QuizController (CRUD quiz)
// - StudentController (Quản lý học viên)
