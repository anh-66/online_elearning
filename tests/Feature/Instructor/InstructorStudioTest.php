<?php

namespace Tests\Feature\Instructor;

use App\Models\Chapter;
use App\Models\Course;
use App\Models\Enrollment;
use App\Models\Lesson;
use App\Models\LessonProgress;
use App\Models\Quiz;
use App\Models\Review;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class InstructorStudioTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        $this->withoutVite();
    }

    public function test_student_cannot_access_instructor_studio(): void
    {
        $student = User::factory()->create();

        $this->actingAs($student)->get(route('instructor.courses.index'))->assertForbidden();
    }

    public function test_instructor_studio_pages_render(): void
    {
        $instructor = $this->createInstructor();
        $course = $this->createCourse($instructor);
        $chapter = $this->createChapter($course);
        $lesson = $this->createLesson($chapter);
        $quiz = Quiz::create(['course_id' => $course->id, 'chapter_id' => $chapter->id, 'title' => 'Quiz 1']);

        $this->actingAs($instructor);

        $this->get(route('instructor.dashboard'))->assertOk();
        $this->get(route('instructor.courses.index'))->assertOk()->assertSee($course->title);
        $this->get(route('instructor.courses.create'))->assertOk();
        $this->get(route('instructor.courses.show', $course))->assertOk()->assertSee($lesson->title);
        $this->get(route('instructor.courses.edit', $course))->assertOk();
        $this->get(route('instructor.chapters.lessons.create', $chapter))->assertOk();
        $this->get(route('instructor.lessons.edit', $lesson))->assertOk();
        $this->get(route('instructor.courses.quizzes.create', $course))->assertOk();
        $this->get(route('instructor.quizzes.show', $quiz))->assertOk();
        $this->get(route('instructor.students.index'))->assertOk();
    }

    public function test_instructor_can_create_draft_course_with_thumbnail_and_gallery(): void
    {
        Storage::fake('public');
        $instructor = $this->createInstructor();

        $response = $this->actingAs($instructor)->post(route('instructor.courses.store'), [
            'title' => 'Lập trình Laravel cơ bản',
            'description' => '<p>Mô tả <strong>khóa học</strong></p>',
            'price' => 199000,
            'level' => Course::LEVEL_BEGINNER,
            'thumbnail' => UploadedFile::fake()->image('thumb.jpg'),
            'images' => [UploadedFile::fake()->image('a.jpg'), UploadedFile::fake()->image('b.png')],
        ]);

        $course = Course::firstOrFail();
        $response->assertSessionHasNoErrors()->assertRedirect(route('instructor.courses.show', $course));

        $this->assertSame($instructor->id, $course->instructor_id);
        $this->assertSame(Course::STATUS_DRAFT, $course->status);
        $this->assertSame('lap-trinh-laravel-co-ban', $course->slug);
        Storage::disk('public')->assertExists($course->thumbnail);
        $this->assertCount(2, $course->images);
    }

    public function test_duplicate_course_titles_get_unique_slugs(): void
    {
        $instructor = $this->createInstructor();
        $this->createCourse($instructor, ['title' => 'Khóa học PHP', 'slug' => 'khoa-hoc-php']);

        $this->actingAs($instructor)->post(route('instructor.courses.store'), [
            'title' => 'Khóa học PHP',
            'price' => 0,
            'level' => Course::LEVEL_BEGINNER,
        ])->assertSessionHasNoErrors();

        $this->assertDatabaseHas('courses', ['slug' => 'khoa-hoc-php-2']);
    }

    public function test_instructor_cannot_manage_another_instructors_course(): void
    {
        $owner = $this->createInstructor();
        $otherInstructor = $this->createInstructor();
        $course = $this->createCourse($owner);

        $this->actingAs($otherInstructor);

        $this->get(route('instructor.courses.show', $course))->assertForbidden();
        $this->put(route('instructor.courses.update', $course), [
            'title' => 'Hack',
            'price' => 0,
            'level' => Course::LEVEL_BEGINNER,
        ])->assertForbidden();
        $this->post(route('instructor.courses.chapters.store', $course), ['title' => 'Chương lạ'])->assertForbidden();

        $this->assertDatabaseMissing('courses', ['title' => 'Hack']);
    }

    public function test_course_without_lessons_cannot_be_submitted_for_review(): void
    {
        $instructor = $this->createInstructor();
        $course = $this->createCourse($instructor);

        $this->actingAs($instructor)
            ->post(route('instructor.courses.submit', $course))
            ->assertSessionHas('error');

        $this->assertSame(Course::STATUS_DRAFT, $course->fresh()->status);
    }

    public function test_submitting_rejected_course_moves_it_to_pending_and_locks_editing(): void
    {
        $instructor = $this->createInstructor();
        $course = $this->createCourse($instructor, [
            'status' => Course::STATUS_REJECTED,
            'reject_reason' => 'Thiếu nội dung',
        ]);
        $this->createLesson($this->createChapter($course));

        $this->actingAs($instructor)->post(route('instructor.courses.submit', $course))->assertSessionHas('success');

        $course->refresh();
        $this->assertSame(Course::STATUS_PENDING, $course->status);
        $this->assertNull($course->reject_reason);

        $this->get(route('instructor.courses.edit', $course))->assertForbidden();

        $this->post(route('instructor.courses.withdraw', $course));
        $this->assertSame(Course::STATUS_DRAFT, $course->fresh()->status);
    }

    public function test_course_with_enrollments_cannot_be_deleted(): void
    {
        $instructor = $this->createInstructor();
        $course = $this->createCourse($instructor);
        Enrollment::create(['user_id' => User::factory()->create()->id, 'course_id' => $course->id]);

        $this->actingAs($instructor)
            ->delete(route('instructor.courses.destroy', $course))
            ->assertSessionHas('error');

        $this->assertModelExists($course);
    }

    public function test_instructor_can_manage_chapters_and_lessons(): void
    {
        Storage::fake('public');
        $instructor = $this->createInstructor();
        $course = $this->createCourse($instructor);

        $this->actingAs($instructor);
        $this->post(route('instructor.courses.chapters.store', $course), ['title' => 'Chương 1'])->assertSessionHasNoErrors();
        $this->post(route('instructor.courses.chapters.store', $course), ['title' => 'Chương 2'])->assertSessionHasNoErrors();

        [$firstChapter, $secondChapter] = $course->chapters()->get()->all();
        $this->assertSame([1, 2], [$firstChapter->order, $secondChapter->order]);

        $this->post(route('instructor.chapters.lessons.store', $firstChapter), [
            'title' => 'Bài 1',
            'content' => '<p>Nội dung</p>',
            'video_url' => 'https://www.youtube.com/watch?v=MYyJ4PuL4pY',
            'attachment' => UploadedFile::fake()->create('slide.pdf', 100, 'application/pdf'),
        ])->assertSessionHasNoErrors();

        $lesson = $firstChapter->lessons()->firstOrFail();
        Storage::disk('public')->assertExists($lesson->attachment_path);

        $this->patch(route('instructor.chapters.move', $secondChapter), ['direction' => 'up']);
        $this->assertSame(1, $secondChapter->fresh()->order);
        $this->assertSame(2, $firstChapter->fresh()->order);

        $this->put(route('instructor.lessons.update', $lesson), [
            'title' => 'Bài 1 (đã sửa)',
            'chapter_id' => $secondChapter->id,
            'remove_attachment' => 1,
        ])->assertSessionHasNoErrors();

        $lesson->refresh();
        $this->assertSame('Bài 1 (đã sửa)', $lesson->title);
        $this->assertSame($secondChapter->id, $lesson->chapter_id);
        $this->assertNull($lesson->attachment_path);
    }

    public function test_lesson_video_must_be_a_youtube_link(): void
    {
        $instructor = $this->createInstructor();
        $chapter = $this->createChapter($this->createCourse($instructor));

        $this->actingAs($instructor)
            ->post(route('instructor.chapters.lessons.store', $chapter), [
                'title' => 'Bài 1',
                'video_url' => 'https://vimeo.com/123',
            ])
            ->assertSessionHasErrors('video_url');
    }

    public function test_lesson_cannot_be_moved_to_chapter_of_another_course(): void
    {
        $instructor = $this->createInstructor();
        $lesson = $this->createLesson($this->createChapter($this->createCourse($instructor)));
        $foreignChapter = $this->createChapter($this->createCourse($this->createInstructor()));

        $this->actingAs($instructor)
            ->put(route('instructor.lessons.update', $lesson), [
                'title' => $lesson->title,
                'chapter_id' => $foreignChapter->id,
            ])
            ->assertSessionHasErrors('chapter_id');

        $this->assertNotSame($foreignChapter->id, $lesson->fresh()->chapter_id);
    }

    public function test_instructor_can_create_quiz_with_questions_and_answers(): void
    {
        $instructor = $this->createInstructor();
        $course = $this->createCourse($instructor);
        $chapter = $this->createChapter($course);

        $this->actingAs($instructor)->post(route('instructor.courses.quizzes.store', $course), [
            'title' => 'Quiz chương 1',
            'chapter_id' => $chapter->id,
        ])->assertSessionHasNoErrors();

        $quiz = $course->quizzes()->firstOrFail();

        $this->post(route('instructor.quizzes.questions.store', $quiz), [
            'question_text' => 'Laravel dùng mô hình nào?',
            'answers' => ['MVC', 'MVVM', 'MVP'],
            'correct_answer' => 0,
        ])->assertSessionHasNoErrors();

        $question = $quiz->questions()->with('answers')->firstOrFail();
        $this->assertCount(3, $question->answers);
        $this->assertSame('MVC', $question->correctAnswer->answer_text);

        $this->put(route('instructor.questions.update', $question), [
            'question_text' => 'Laravel dùng mô hình nào?',
            'answers' => ['MVC', 'MVVM'],
            'correct_answer' => 1,
        ])->assertSessionHasNoErrors();

        $question->refresh();
        $this->assertCount(2, $question->answers);
        $this->assertSame('MVVM', $question->correctAnswer->answer_text);
    }

    public function test_question_requires_a_valid_correct_answer(): void
    {
        $instructor = $this->createInstructor();
        $quiz = Quiz::create(['course_id' => $this->createCourse($instructor)->id, 'title' => 'Quiz']);

        $this->actingAs($instructor)
            ->post(route('instructor.quizzes.questions.store', $quiz), [
                'question_text' => 'Câu hỏi',
                'answers' => ['A', 'B'],
                'correct_answer' => 5,
            ])
            ->assertSessionHasErrors('correct_answer');
    }

    public function test_students_page_shows_progress_and_dashboard_shows_average_rating(): void
    {
        $instructor = $this->createInstructor();
        $course = $this->createCourse($instructor);
        $chapter = $this->createChapter($course);
        $completedLesson = $this->createLesson($chapter);
        $this->createLesson($chapter, ['order' => 2]);

        $student = User::factory()->create(['name' => 'Nguyễn Học Viên']);
        Enrollment::create(['user_id' => $student->id, 'course_id' => $course->id]);
        LessonProgress::create([
            'user_id' => $student->id,
            'lesson_id' => $completedLesson->id,
            'is_completed' => true,
            'completed_at' => now(),
        ]);
        Review::create(['user_id' => $student->id, 'course_id' => $course->id, 'rating' => 4]);

        $this->actingAs($instructor);

        $this->get(route('instructor.students.index', ['course_id' => $course->id]))
            ->assertOk()
            ->assertSee('Nguyễn Học Viên')
            ->assertSee('50%')
            ->assertSee('1/2 bài học');

        $this->get(route('instructor.dashboard'))
            ->assertOk()
            ->assertViewHas('stats', fn (array $stats) => $stats['total_students'] === 1 && $stats['average_rating'] === 4.0);
    }

    private function createInstructor(): User
    {
        return User::factory()->create(['role' => User::ROLE_INSTRUCTOR]);
    }

    /**
     * @param  array<string, mixed>  $attributes
     */
    private function createCourse(User $instructor, array $attributes = []): Course
    {
        $title = $attributes['title'] ?? 'Khóa học '.fake()->unique()->word();

        return Course::create(array_merge([
            'instructor_id' => $instructor->id,
            'title' => $title,
            'slug' => str($title)->slug().'-'.fake()->unique()->randomNumber(5),
            'price' => 0,
            'level' => Course::LEVEL_BEGINNER,
            'status' => Course::STATUS_DRAFT,
        ], $attributes));
    }

    private function createChapter(Course $course): Chapter
    {
        return Chapter::create(['course_id' => $course->id, 'title' => 'Chương '.fake()->word(), 'order' => 1]);
    }

    /**
     * @param  array<string, mixed>  $attributes
     */
    private function createLesson(Chapter $chapter, array $attributes = []): Lesson
    {
        return Lesson::create(array_merge([
            'chapter_id' => $chapter->id,
            'title' => 'Bài học '.fake()->unique()->word(),
            'order' => 1,
        ], $attributes));
    }
}
