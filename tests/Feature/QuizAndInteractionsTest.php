<?php

namespace Tests\Feature;

use App\Models\Chapter;
use App\Models\Course;
use App\Models\Enrollment;
use App\Models\Lesson;
use App\Models\LessonComment;
use App\Models\Quiz;
use App\Models\QuizAttempt;
use App\Models\Review;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class QuizAndInteractionsTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->withoutVite();
    }

    private function createCourse(User $instructor): Course
    {
        return Course::create([
            'instructor_id' => $instructor->id,
            'title' => 'Khóa học Laravel Master',
            'slug' => 'khoa-hoc-laravel-master-'.fake()->unique()->randomNumber(5),
            'price' => 0,
            'level' => Course::LEVEL_BEGINNER,
            'status' => Course::STATUS_PUBLISHED,
        ]);
    }

    private function enroll(User $student, Course $course): Enrollment
    {
        return Enrollment::create([
            'user_id' => $student->id,
            'course_id' => $course->id,
            'status' => Enrollment::STATUS_FREE,
            'enrolled_at' => now(),
        ]);
    }

    // ==========================================
    // 1. QUIZ TRẮC NGHIỆM & CHẤM ĐIỂM
    // ==========================================

    public function test_unenrolled_student_cannot_take_quiz(): void
    {
        $instructor = User::factory()->instructor()->create();
        $student = User::factory()->create();
        $course = $this->createCourse($instructor);
        $quiz = Quiz::create(['course_id' => $course->id, 'title' => 'Quiz 1']);

        $this->actingAs($student)
            ->get(route('quiz.show', $quiz))
            ->assertForbidden();
    }

    public function test_enrolled_student_can_view_quiz(): void
    {
        $instructor = User::factory()->instructor()->create();
        $student = User::factory()->create();
        $course = $this->createCourse($instructor);
        $this->enroll($student, $course);

        $quiz = Quiz::create(['course_id' => $course->id, 'title' => 'Quiz Chương 1']);
        $question = $quiz->questions()->create(['question_text' => 'Laravel là gì?']);
        $question->answers()->create(['answer_text' => 'PHP Framework', 'is_correct' => true]);
        $question->answers()->create(['answer_text' => 'Python Library', 'is_correct' => false]);

        $this->actingAs($student)
            ->get(route('quiz.show', $quiz))
            ->assertOk()
            ->assertSee('Quiz Chương 1')
            ->assertSee('Laravel là gì?')
            ->assertSee('PHP Framework');
    }

    public function test_student_can_submit_quiz_and_be_graded_automatically(): void
    {
        $instructor = User::factory()->instructor()->create();
        $student = User::factory()->create();
        $course = $this->createCourse($instructor);
        $this->enroll($student, $course);

        $quiz = Quiz::create(['course_id' => $course->id, 'title' => 'Kiểm tra 2 câu']);

        // Câu 1
        $q1 = $quiz->questions()->create(['question_text' => 'Câu 1']);
        $a1Correct = $q1->answers()->create(['answer_text' => 'Đúng 1', 'is_correct' => true]);
        $q1->answers()->create(['answer_text' => 'Sai 1', 'is_correct' => false]);

        // Câu 2
        $q2 = $quiz->questions()->create(['question_text' => 'Câu 2']);
        $a2Correct = $q2->answers()->create(['answer_text' => 'Đúng 2', 'is_correct' => true]);
        $q2->answers()->create(['answer_text' => 'Sai 2', 'is_correct' => false]);

        // Nộp đúng cả 2 câu -> 100% -> Đạt
        $response = $this->actingAs($student)->post(route('quiz.submit', $quiz), [
            'answers' => [
                $q1->id => $a1Correct->id,
                $q2->id => $a2Correct->id,
            ],
        ]);

        $attempt = QuizAttempt::where('user_id', $student->id)->where('quiz_id', $quiz->id)->first();
        $this->assertNotNull($attempt);
        $this->assertSame(100, $attempt->score);
        $this->assertSame(2, $attempt->total_correct);
        $this->assertSame(2, $attempt->total_questions);
        $this->assertTrue($attempt->is_passed);
        $this->assertSame($a1Correct->id, $attempt->selectedAnswerFor($q1));

        $response->assertRedirect(route('quiz.result', [$quiz, $attempt]));
    }

    public function test_student_failing_quiz_when_score_is_under_eighty_percent(): void
    {
        $instructor = User::factory()->instructor()->create();
        $student = User::factory()->create();
        $course = $this->createCourse($instructor);
        $this->enroll($student, $course);

        $quiz = Quiz::create(['course_id' => $course->id, 'title' => 'Kiểm tra 2 câu']);

        $q1 = $quiz->questions()->create(['question_text' => 'Câu 1']);
        $a1Correct = $q1->answers()->create(['answer_text' => 'Đúng 1', 'is_correct' => true]);
        $q1->answers()->create(['answer_text' => 'Sai 1', 'is_correct' => false]);

        $q2 = $quiz->questions()->create(['question_text' => 'Câu 2']);
        $q2->answers()->create(['answer_text' => 'Đúng 2', 'is_correct' => true]);
        $a2Wrong = $q2->answers()->create(['answer_text' => 'Sai 2', 'is_correct' => false]);

        // Đúng 1/2 câu = 50% -> Không đạt
        $this->actingAs($student)->post(route('quiz.submit', $quiz), [
            'answers' => [
                $q1->id => $a1Correct->id,
                $q2->id => $a2Wrong->id,
            ],
        ]);

        $attempt = QuizAttempt::latest('id')->first();
        $this->assertSame(50, $attempt->score);
        $this->assertFalse($attempt->is_passed);
    }

    public function test_student_cannot_view_another_students_attempt_result(): void
    {
        $instructor = User::factory()->instructor()->create();
        $studentA = User::factory()->create();
        $studentB = User::factory()->create();
        $course = $this->createCourse($instructor);
        $this->enroll($studentA, $course);
        $this->enroll($studentB, $course);

        $quiz = Quiz::create(['course_id' => $course->id, 'title' => 'Quiz 1']);
        $attempt = QuizAttempt::create([
            'user_id' => $studentA->id,
            'quiz_id' => $quiz->id,
            'score' => 100,
            'total_correct' => 1,
            'total_questions' => 1,
            'is_passed' => true,
        ]);

        $this->actingAs($studentB)
            ->get(route('quiz.result', [$quiz, $attempt]))
            ->assertForbidden();

        $this->actingAs($studentA)
            ->get(route('quiz.result', [$quiz, $attempt]))
            ->assertOk();
    }

    public function test_student_can_view_quiz_attempts_history(): void
    {
        $instructor = User::factory()->instructor()->create();
        $student = User::factory()->create();
        $course = $this->createCourse($instructor);
        $this->enroll($student, $course);

        $quiz = Quiz::create(['course_id' => $course->id, 'title' => 'Quiz 1']);
        QuizAttempt::create([
            'user_id' => $student->id,
            'quiz_id' => $quiz->id,
            'score' => 70,
            'total_correct' => 7,
            'total_questions' => 10,
            'is_passed' => false,
        ]);
        QuizAttempt::create([
            'user_id' => $student->id,
            'quiz_id' => $quiz->id,
            'score' => 90,
            'total_correct' => 9,
            'total_questions' => 10,
            'is_passed' => true,
        ]);

        $this->actingAs($student)
            ->get(route('quiz.history', $quiz))
            ->assertOk()
            ->assertSee('90%')
            ->assertSee('70%');
    }

    // ==========================================
    // 2. ĐÁNH GIÁ KHÓA HỌC (REVIEWS)
    // ==========================================

    public function test_unenrolled_student_cannot_review_course(): void
    {
        $instructor = User::factory()->instructor()->create();
        $student = User::factory()->create();
        $course = $this->createCourse($instructor);

        $this->actingAs($student)
            ->post(route('reviews.store', $course), [
                'rating' => 5,
                'comment' => 'Khóa học tuyệt vời',
            ])
            ->assertForbidden();
    }

    public function test_enrolled_student_can_review_and_rating_average_is_updated(): void
    {
        $instructor = User::factory()->instructor()->create();
        $student1 = User::factory()->create();
        $student2 = User::factory()->create();
        $course = $this->createCourse($instructor);
        $this->enroll($student1, $course);
        $this->enroll($student2, $course);

        $this->actingAs($student1)->post(route('reviews.store', $course), [
            'rating' => 5,
            'comment' => 'Bài giảng rất hay!',
        ])->assertRedirect(route('courses.show', $course->slug).'#reviews');

        $this->actingAs($student2)->post(route('reviews.store', $course), [
            'rating' => 3,
            'comment' => 'Tạm được.',
        ]);

        $this->assertDatabaseHas('reviews', [
            'course_id' => $course->id,
            'user_id' => $student1->id,
            'rating' => 5,
        ]);

        $this->assertSame(4.0, $course->averageRating());
    }

    public function test_student_cannot_review_more_than_once(): void
    {
        $instructor = User::factory()->instructor()->create();
        $student = User::factory()->create();
        $course = $this->createCourse($instructor);
        $this->enroll($student, $course);

        Review::create([
            'course_id' => $course->id,
            'user_id' => $student->id,
            'rating' => 5,
            'comment' => 'Lần đầu',
        ]);

        // Lần 2 gửi request store -> bị chặn có thông báo lỗi
        $this->actingAs($student)->post(route('reviews.store', $course), [
            'rating' => 4,
            'comment' => 'Lần thứ hai',
        ])->assertSessionHas('error');

        $this->assertCount(1, $course->reviews);
    }

    public function test_student_can_update_and_delete_own_review(): void
    {
        $instructor = User::factory()->instructor()->create();
        $student = User::factory()->create();
        $otherStudent = User::factory()->create();
        $course = $this->createCourse($instructor);
        $this->enroll($student, $course);

        $review = Review::create([
            'course_id' => $course->id,
            'user_id' => $student->id,
            'rating' => 4,
            'comment' => 'Nội dung ổn',
        ]);

        // Người khác không sửa được
        $this->actingAs($otherStudent)
            ->put(route('reviews.update', $review), ['rating' => 1])
            ->assertForbidden();

        // Chủ nhân cập nhật
        $this->actingAs($student)
            ->put(route('reviews.update', $review), [
                'rating' => 5,
                'comment' => 'Đã sửa lại thành 5 sao!',
            ])
            ->assertRedirect(route('courses.show', $course->slug).'#reviews');

        $this->assertSame(5, $review->fresh()->rating);

        // Chủ nhân xóa
        $this->actingAs($student)
            ->delete(route('reviews.destroy', $review))
            ->assertRedirect(route('courses.show', $course->slug).'#reviews');

        $this->assertModelMissing($review);
    }

    // ==========================================
    // 3. HỎI ĐÁP / BÌNH LUẬN BÀI HỌC (COMMENTS)
    // ==========================================

    public function test_enrolled_student_can_comment_and_reply_on_lesson(): void
    {
        $instructor = User::factory()->instructor()->create();
        $student = User::factory()->create();
        $course = $this->createCourse($instructor);
        $this->enroll($student, $course);

        $chapter = Chapter::create(['course_id' => $course->id, 'title' => 'Chương 1', 'order' => 1]);
        $lesson = Lesson::create(['chapter_id' => $chapter->id, 'title' => 'Bài 1', 'order' => 1]);

        // Đăng câu hỏi gốc
        $this->actingAs($student)->post(route('comments.store', $lesson), [
            'content' => 'Làm sao để cấu hình database ạ?',
        ]);

        $comment = LessonComment::where('lesson_id', $lesson->id)->whereNull('parent_id')->first();
        $this->assertNotNull($comment);
        $this->assertSame('Làm sao để cấu hình database ạ?', $comment->content);

        // Giảng viên trả lời
        $this->actingAs($instructor)->post(route('comments.store', $lesson), [
            'parent_id' => $comment->id,
            'content' => 'Em xem trong file .env nhé!',
        ]);

        $reply = LessonComment::where('parent_id', $comment->id)->first();
        $this->assertNotNull($reply);
        $this->assertSame($instructor->id, $reply->user_id);
    }

    public function test_student_can_update_and_delete_own_comment(): void
    {
        $instructor = User::factory()->instructor()->create();
        $student = User::factory()->create();
        $otherStudent = User::factory()->create();
        $course = $this->createCourse($instructor);
        $this->enroll($student, $course);

        $chapter = Chapter::create(['course_id' => $course->id, 'title' => 'Chương 1', 'order' => 1]);
        $lesson = Lesson::create(['chapter_id' => $chapter->id, 'title' => 'Bài 1', 'order' => 1]);

        $comment = LessonComment::create([
            'lesson_id' => $lesson->id,
            'user_id' => $student->id,
            'content' => 'Nội dung ban đầu',
        ]);

        // Người khác không được sửa
        $this->actingAs($otherStudent)
            ->put(route('comments.update', $comment), ['content' => 'Hacker sửa'])
            ->assertForbidden();

        // Chủ nhân sửa
        $this->actingAs($student)
            ->put(route('comments.update', $comment), ['content' => 'Nội dung đã sửa'])
            ->assertSessionHas('success');

        $this->assertSame('Nội dung đã sửa', $comment->fresh()->content);

        // Chủ nhân xóa
        $this->actingAs($student)
            ->delete(route('comments.destroy', $comment))
            ->assertSessionHas('success');

        $this->assertModelMissing($comment);
    }

    // ==========================================
    // 4. TÍCH HỢP PHÒNG HỌC (LEARNING PLAYER INTEGRATION)
    // ==========================================

    public function test_enrolled_student_can_access_learning_player_and_see_quiz_button_and_comments(): void
    {
        $instructor = User::factory()->instructor()->create();
        $student = User::factory()->create();
        $course = $this->createCourse($instructor);
        $this->enroll($student, $course);

        $chapter = Chapter::create(['course_id' => $course->id, 'title' => 'Chương 1', 'order' => 1]);
        $lesson = Lesson::create(['chapter_id' => $chapter->id, 'title' => 'Cài đặt Laravel', 'order' => 1]);
        $quiz = Quiz::create(['course_id' => $course->id, 'chapter_id' => $chapter->id, 'title' => 'Quiz Cuối Chương 1']);

        $this->actingAs($student)
            ->get(route('learning.player', $course->slug))
            ->assertOk()
            ->assertSee('Cài đặt Laravel')
            ->assertSee('Quiz Cuối Chương 1')
            ->assertSee('Hỏi đáp & Thảo luận');
    }

    public function test_enrolled_student_can_toggle_lesson_complete(): void
    {
        $instructor = User::factory()->instructor()->create();
        $student = User::factory()->create();
        $course = $this->createCourse($instructor);
        $this->enroll($student, $course);

        $chapter = Chapter::create(['course_id' => $course->id, 'title' => 'Chương 1', 'order' => 1]);
        $lesson = Lesson::create(['chapter_id' => $chapter->id, 'title' => 'Bài 1', 'order' => 1]);

        $this->actingAs($student)
            ->post(route('learning.complete', $lesson->id))
            ->assertSessionHas('success');

        $this->assertDatabaseHas('lesson_progress', [
            'user_id' => $student->id,
            'lesson_id' => $lesson->id,
            'is_completed' => true,
        ]);
    }

    public function test_course_instructor_can_reply_to_student_review(): void
    {
        $instructor = User::factory()->instructor()->create();
        $student = User::factory()->create();
        $course = $this->createCourse($instructor);
        $this->enroll($student, $course);

        $review = Review::create([
            'user_id' => $student->id,
            'course_id' => $course->id,
            'rating' => 5,
            'comment' => 'Khóa học tuyệt vời!',
        ]);

        $this->actingAs($instructor)
            ->post(route('reviews.reply', $review), [
                'instructor_reply' => 'Cảm ơn em đã ủng hộ khóa học của thầy!',
            ])
            ->assertRedirect();

        $this->assertDatabaseHas('reviews', [
            'id' => $review->id,
            'instructor_reply' => 'Cảm ơn em đã ủng hộ khóa học của thầy!',
            'instructor_id' => $instructor->id,
        ]);
    }

    public function test_another_student_cannot_reply_to_review(): void
    {
        $instructor = User::factory()->instructor()->create();
        $student1 = User::factory()->create();
        $student2 = User::factory()->create();
        $course = $this->createCourse($instructor);
        $this->enroll($student1, $course);
        $this->enroll($student2, $course);

        $review = Review::create([
            'user_id' => $student1->id,
            'course_id' => $course->id,
            'rating' => 5,
            'comment' => 'Khóa học tuyệt vời!',
        ]);

        $this->actingAs($student2)
            ->post(route('reviews.reply', $review), [
                'instructor_reply' => 'Tôi là học viên khác!',
            ])
            ->assertForbidden();
    }

    public function test_instructor_can_delete_reply(): void
    {
        $instructor = User::factory()->instructor()->create();
        $student = User::factory()->create();
        $course = $this->createCourse($instructor);
        $this->enroll($student, $course);

        $review = Review::create([
            'user_id' => $student->id,
            'course_id' => $course->id,
            'rating' => 5,
            'comment' => 'Khóa học tuyệt vời!',
            'instructor_reply' => 'Cảm ơn em!',
            'instructor_id' => $instructor->id,
            'replied_at' => now(),
        ]);

        $this->actingAs($instructor)
            ->delete(route('reviews.destroy-reply', $review))
            ->assertRedirect();

        $this->assertDatabaseHas('reviews', [
            'id' => $review->id,
            'instructor_reply' => null,
            'instructor_id' => null,
        ]);
    }

    public function test_lesson_auto_extracts_youtube_video_from_ckeditor_content(): void
    {
        $instructor = User::factory()->create(['role' => User::ROLE_INSTRUCTOR]);
        $course = $this->createCourse($instructor);
        $chapter = Chapter::create(['course_id' => $course->id, 'title' => 'Chương 1', 'order' => 1]);

        $ckeditorContent = '<p>Giới thiệu bài học</p><figure class="media"><div data-oembed-url="https://www.youtube.com/watch?v=-IkvTMOEZfE"><div><iframe src="https://www.youtube.com/embed/-IkvTMOEZfE" width="1280" height="720"></iframe></div></div></figure><p>Ghi chú sau bài học</p>';

        // Giảng viên tạo bài học chỉ dán video vào CKEditor, để trống video_url
        $this->actingAs($instructor)
            ->post(route('instructor.chapters.lessons.store', $chapter), [
                'title' => 'Bài học cờ vua gf',
                'content' => $ckeditorContent,
                'video_url' => '',
            ])
            ->assertRedirect(route('instructor.courses.show', $course->id));

        $lesson = Lesson::where('title', 'Bài học cờ vua gf')->first();
        $this->assertNotNull($lesson);
        // Kiểm tra video_url được tự động trích xuất
        $this->assertEquals('https://www.youtube.com/watch?v=-IkvTMOEZfE', $lesson->video_url);

        // Học sinh vào xem bài học: video phát trên đầu, không bị ô đen và không bị lặp video dưới nội dung
        $student = User::factory()->create();
        $this->enroll($student, $course);

        $this->actingAs($student)
            ->get(route('learning.player', [$course->slug, $lesson->id]))
            ->assertOk()
            ->assertSee('https://www.youtube.com/embed/-IkvTMOEZfE?rel=0')
            ->assertDontSee('Bài học này cung cấp tài liệu đọc dưới đây')
            ->assertSee('Ghi chú sau bài học');
    }
}
