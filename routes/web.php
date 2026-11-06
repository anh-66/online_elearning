<?php

use App\Http\Controllers\Auth\GoogleController;
use App\Http\Controllers\CourseCatalogController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\InstructorProfileController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\UploadController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\QuizController;
use App\Http\Controllers\ReviewController;
use App\Http\Controllers\CommentController;
use App\Http\Controllers\EnrollmentController;
use App\Http\Controllers\MyCourseController;
use App\Http\Controllers\LearningController;
use App\Http\Controllers\WishlistController;
/*
|--------------------------------------------------------------------------
| Public Routes (Thành viên 2: HomeController, CourseCatalogController, InstructorProfileController)
|--------------------------------------------------------------------------
*/
Route::get('/', [HomeController::class, 'index'])->name('home');

Route::get('/courses', [CourseCatalogController::class, 'index'])->name('courses.index');
Route::get('/courses/{slug}', [CourseCatalogController::class, 'show'])->name('courses.show');
Route::get('/instructors/{id}', [InstructorProfileController::class, 'show'])->name('instructors.show');

/*
|--------------------------------------------------------------------------
| Student Routes (Dành cho Thành viên 3 & Thành viên 4)
|--------------------------------------------------------------------------
| Các route yêu cầu đăng nhập và phân quyền học viên (hoặc giảng viên/admin đều có thể học thử).
*/
Route::middleware(['auth'])->group(function () {
    // TV3: Ghi danh & Khóa học của tôi & Phòng học (Learning Player)
     Route::get('/my-courses', [MyCourseController::class, 'index'])->name('my-courses');
     Route::post('/courses/{id}/enroll', [EnrollmentController::class, 'enroll'])->name('courses.enroll');
     Route::get('/learning/{course_slug}/{lesson_id?}', [LearningController::class, 'player'])->name('learning.player');
     Route::post('/learning/lessons/{id}/complete', [LearningController::class, 'markComplete'])->name('learning.complete');
     Route::post('/wishlist/{course_id}', [WishlistController::class, 'toggle'])->name('wishlist.toggle');

    // TV4: Quiz trắc nghiệm & Đánh giá sao & Bình luận hỏi đáp
     Route::get('/quiz/{id}', [App\Http\Controllers\QuizController::class, 'show'])->name('quiz.show');
     Route::post('/quiz/{id}/submit', [App\Http\Controllers\QuizController::class, 'submit'])->name('quiz.submit');
     Route::get('/quiz/{id}/result', [App\Http\Controllers\QuizController::class, 'result'])->name('quiz.result');
    
     Route::post('/courses/{id}/reviews', [ReviewController::class, 'store'])->name('reviews.store');
     Route::post('/lessons/{id}/comments', [CommentController::class, 'store'])->name('comments.store');
});

/*
|--------------------------------------------------------------------------
| Profile & System Upload Routes (Thành viên 1 & Dùng chung)
|--------------------------------------------------------------------------
*/
Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    // Endpoint upload ảnh cho CKEditor 5
    Route::post('/upload/image', [UploadController::class, 'uploadImage'])->name('upload.image');
});

Route::middleware('guest')->group(function () {
    Route::get('auth/google', [GoogleController::class, 'redirectToGoogle'])->name('auth.google');
    Route::get('auth/google/callback', [GoogleController::class, 'handleGoogleCallback'])
        ->name('auth.google.callback');
});
Route::get('/courses/{id}/learn', [App\Http\Controllers\LearningController::class, 'index'])->name('learning.index');
require __DIR__.'/auth.php';
