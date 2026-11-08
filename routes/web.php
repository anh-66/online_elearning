<?php

use App\Http\Controllers\Auth\GoogleController;
use App\Http\Controllers\CommentController;
use App\Http\Controllers\CourseCatalogController;
use App\Http\Controllers\CourseCheckoutController;
use App\Http\Controllers\EnrollmentController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\InstructorProfileController;
use App\Http\Controllers\LearningController;
use App\Http\Controllers\MyCourseController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\QuizController;
use App\Http\Controllers\ReviewController;
use App\Http\Controllers\SupportController;
use App\Http\Controllers\UploadController;
use App\Http\Controllers\WishlistController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

// use App\Http\Controllers\WishlistController;
/*
|--------------------------------------------------------------------------
| Public Routes (Thành viên 2: HomeController, CourseCatalogController, InstructorProfileController)
|--------------------------------------------------------------------------
*/
Route::get('/', [HomeController::class, 'index'])->name('home');

Route::get('/courses', [CourseCatalogController::class, 'index'])->name('courses.index');
Route::get('/courses/{slug}', [CourseCatalogController::class, 'show'])->name('courses.show');
Route::get('/instructors/{id}', [InstructorProfileController::class, 'show'])->name('instructors.show');

Route::get('/support', [SupportController::class, 'index'])->name('support.index');
Route::get('/support/bao-cao-su-co', [SupportController::class, 'feedback'])->name('support.feedback');
Route::post('/support/bao-cao-su-co', [SupportController::class, 'submitFeedback'])->name('support.feedback.submit');
Route::get('/support/feedback', fn () => redirect()->route('support.feedback'));
Route::get('/support/{slug}', [SupportController::class, 'show'])->name('support.show');

/*
|--------------------------------------------------------------------------
| Student Routes (Dành cho Thành viên 3 & Thành viên 4)
|--------------------------------------------------------------------------
| Các route yêu cầu đăng nhập và phân quyền học viên (hoặc giảng viên/admin đều có thể học thử).
*/
Route::middleware(['auth'])->group(function () {
    // TV3: Ghi danh & Khóa học của tôi & Phòng học (Learning Player)
    Route::get('/my-courses', [MyCourseController::class, 'index'])->name('my-courses');
    Route::post('/courses/{course}/enroll', [EnrollmentController::class, 'store'])->name('courses.enroll');
    Route::get('/courses/{course:slug}/checkout', [CourseCheckoutController::class, 'show'])->name('courses.checkout');
    Route::post('/courses/{course:slug}/checkout', [CourseCheckoutController::class, 'process'])->name('courses.checkout.process');
    Route::get('/learning/{course:slug}/{lesson_id?}', [LearningController::class, 'player'])->name('learning.player');
    Route::post('/learning/lessons/{lesson}/complete', [LearningController::class, 'markComplete'])->name('learning.complete');
    Route::post('/wishlist/{course_id}', [WishlistController::class, 'toggle'])->name('wishlist.toggle');
    // TV4: Quiz trắc nghiệm & Đánh giá sao & Bình luận hỏi đáp
    Route::get('/quiz/{quiz}', [QuizController::class, 'show'])->name('quiz.show');
    Route::post('/quiz/{quiz}/submit', [QuizController::class, 'submit'])->name('quiz.submit');
    Route::get('/quiz/{quiz}/history', [QuizController::class, 'history'])->name('quiz.history');
    Route::get('/quiz/{quiz}/attempts/{attempt}', [QuizController::class, 'result'])->name('quiz.result');

    Route::post('/courses/{course}/reviews', [ReviewController::class, 'store'])->name('reviews.store');
    Route::put('/reviews/{review}', [ReviewController::class, 'update'])->name('reviews.update');
    Route::delete('/reviews/{review}', [ReviewController::class, 'destroy'])->name('reviews.destroy');
    Route::post('/reviews/{review}/reply', [ReviewController::class, 'reply'])->name('reviews.reply');
    Route::delete('/reviews/{review}/reply', [ReviewController::class, 'destroyReply'])->name('reviews.destroy-reply');

    Route::post('/lessons/{lesson}/comments', [CommentController::class, 'store'])->name('comments.store');
    Route::put('/comments/{comment}', [CommentController::class, 'update'])->name('comments.update');
    Route::delete('/comments/{comment}', [CommentController::class, 'destroy'])->name('comments.destroy');
});

/*
|--------------------------------------------------------------------------
| Profile & System Upload Routes (Thành viên 1 & Dùng chung)
|--------------------------------------------------------------------------
*/
Route::get('/dashboard', function (Request $request) {
    $enrolledCourses = $request->user()->enrolledCourses()
        ->with(['instructor', 'category', 'reviews'])
        ->withCount('enrollments')
        ->latest('enrollments.enrolled_at')
        ->take(6)
        ->get();

    return view('dashboard', [
        'enrolledCourses' => $enrolledCourses,
        'enrollmentTotal' => $request->user()->enrollments()->count(),
    ]);
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::view('/avatar-generator', 'avatar-generator')->name('avatar-generator');
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::get('/profile/export-data', [ProfileController::class, 'exportData'])->name('profile.export-data');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
    Route::delete('/profile/payment-methods/{paymentMethod}', [ProfileController::class, 'destroyPaymentMethod'])->name('profile.payment-methods.destroy');

    // Endpoint upload ảnh cho CKEditor 5
    Route::post('/upload/image', [UploadController::class, 'uploadImage'])->name('upload.image');
});

// Fallback phục vụ file public storage trên môi trường thiếu symlink (Windows)
Route::get('/storage/{path}', function (string $path) {
    $filePath = storage_path('app/public/'.$path);
    if (! file_exists($filePath)) {
        abort(404);
    }

    return response()->file($filePath);
})->where('path', '.*');

Route::middleware('guest')->group(function () {
    Route::get('auth/google', [GoogleController::class, 'redirectToGoogle'])->name('auth.google');
    Route::get('auth/google/callback', [GoogleController::class, 'handleGoogleCallback'])
        ->name('auth.google.callback');
});
Route::get('/courses/{course:slug}/learn', [LearningController::class, 'player'])->middleware('auth')->name('learning.index');
require __DIR__.'/auth.php';
