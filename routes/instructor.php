<?php

use App\Http\Controllers\Instructor\ChapterController;
use App\Http\Controllers\Instructor\CourseController;
use App\Http\Controllers\Instructor\DashboardController;
use App\Http\Controllers\Instructor\LessonController;
use App\Http\Controllers\Instructor\QuestionController;
use App\Http\Controllers\Instructor\QuizController;
use App\Http\Controllers\Instructor\StudentController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Instructor Routes (Dành riêng cho Thành viên 5)
|--------------------------------------------------------------------------
| Tất cả các route trong file này tự động:
| - Có tiền tố: /instructor (ví dụ: /instructor/dashboard)
| - Có name prefix: instructor. (ví dụ: route('instructor.dashboard'))
| - Đã được bảo vệ qua middleware: ['web', 'auth', 'role:instructor,admin']
| Quyền sở hữu khóa học được kiểm tra trong App\Policies\CoursePolicy.
*/

Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

// Khóa học: CRUD, gửi duyệt (draft -> pending), rút lại yêu cầu duyệt, xóa ảnh thư viện
Route::resource('courses', CourseController::class);
Route::post('/courses/{course}/submit', [CourseController::class, 'submit'])->name('courses.submit');
Route::post('/courses/{course}/withdraw', [CourseController::class, 'withdraw'])->name('courses.withdraw');
Route::delete('/courses/{course}/images/{image}', [CourseController::class, 'destroyImage'])
    ->scopeBindings()
    ->name('courses.images.destroy');

// Chương
Route::resource('courses.chapters', ChapterController::class)->shallow()->only(['store', 'update', 'destroy']);
Route::patch('/chapters/{chapter}/move', [ChapterController::class, 'move'])->name('chapters.move');

// Bài học
Route::resource('chapters.lessons', LessonController::class)->shallow()->except(['index', 'show']);
Route::patch('/lessons/{lesson}/move', [LessonController::class, 'move'])->name('lessons.move');

// Quiz và câu hỏi trắc nghiệm
Route::resource('courses.quizzes', QuizController::class)->shallow()->except(['index']);
Route::resource('quizzes.questions', QuestionController::class)->shallow()->only(['store', 'edit', 'update', 'destroy']);

// Học viên theo khóa
Route::get('/students', [StudentController::class, 'index'])->name('students.index');
