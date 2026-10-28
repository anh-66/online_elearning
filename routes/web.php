<?php

use App\Http\Controllers\CourseCatalogController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\InstructorProfileController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\UploadController;
use Illuminate\Support\Facades\Route;

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
    // Route::get('/my-courses', [MyCourseController::class, 'index'])->name('my-courses');
    // Route::post('/courses/{id}/enroll', [EnrollmentController::class, 'enroll'])->name('courses.enroll');
    // Route::get('/learning/{course_slug}/{lesson_id?}', [LearningController::class, 'player'])->name('learning.player');
    // Route::post('/learning/lessons/{id}/complete', [LearningController::class, 'markComplete'])->name('learning.complete');
    // Route::post('/wishlist/{course_id}', [WishlistController::class, 'toggle'])->name('wishlist.toggle');

    // TV4: Quiz trắc nghiệm & Đánh giá sao & Bình luận hỏi đáp
    // Route::get('/quizzes/{id}', [QuizController::class, 'show'])->name('quizzes.show');
    // Route::post('/quizzes/{id}/submit', [QuizController::class, 'submit'])->name('quizzes.submit');
    // Route::post('/courses/{id}/reviews', [ReviewController::class, 'store'])->name('reviews.store');
    // Route::post('/lessons/{id}/comments', [LessonCommentController::class, 'store'])->name('comments.store');
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

require __DIR__.'/auth.php';
