<?php

namespace Tests\Feature;

use App\Models\Certificate;
use App\Models\Chapter;
use App\Models\Course;
use App\Models\Enrollment;
use App\Models\Lesson;
use App\Models\LessonProgress;
use App\Models\User;
use App\Services\CertificateIssuer;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CertificateTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->withoutVite();
    }

    private function createCourse(User $instructor, float $price = 0, string $title = 'Lập trình PHP & Laravel'): Course
    {
        return Course::create([
            'instructor_id' => $instructor->id,
            'title' => $title,
            'slug' => 'khoa-hoc-'.fake()->unique()->randomNumber(6),
            'price' => $price,
            'level' => Course::LEVEL_INTERMEDIATE,
            'status' => Course::STATUS_PUBLISHED,
        ]);
    }

    /**
     * @return array<int, Lesson>
     */
    private function addLessons(Course $course, int $count = 2): array
    {
        $chapter = Chapter::create(['course_id' => $course->id, 'title' => 'Chương 1', 'order' => 1]);

        return collect(range(1, $count))
            ->map(fn (int $i) => Lesson::create(['chapter_id' => $chapter->id, 'title' => 'Bài '.$i, 'order' => $i]))
            ->all();
    }

    private function enroll(User $student, Course $course, int $progress = 0): Enrollment
    {
        return Enrollment::create([
            'user_id' => $student->id,
            'course_id' => $course->id,
            'status' => Enrollment::STATUS_FREE,
            'enrolled_at' => now(),
            'progress_percent' => $progress,
        ]);
    }

    private function completeLessons(User $student, array $lessons): void
    {
        foreach ($lessons as $lesson) {
            LessonProgress::create([
                'user_id' => $student->id,
                'lesson_id' => $lesson->id,
                'is_completed' => true,
                'completed_at' => now(),
            ]);
        }
    }

    /**
     * @return array{0: User, 1: User, 2: Course, 3: array<int, Lesson>}
     */
    private function finishedCourse(): array
    {
        $instructor = User::factory()->instructor()->create(['name' => 'Trần Minh Khoa']);
        $student = User::factory()->create(['name' => 'Nguyễn Văn An']);
        $course = $this->createCourse($instructor);
        $lessons = $this->addLessons($course, 2);
        $this->enroll($student, $course, 100);
        $this->completeLessons($student, $lessons);

        return [$instructor, $student, $course, $lessons];
    }

    public function test_finishing_the_last_lesson_issues_a_certificate(): void
    {
        $instructor = User::factory()->instructor()->create(['name' => 'Trần Minh Khoa']);
        $student = User::factory()->create(['name' => 'Nguyễn Văn An']);
        $course = $this->createCourse($instructor);
        [$first, $last] = $this->addLessons($course, 2);
        $this->enroll($student, $course);

        $this->actingAs($student)
            ->post(route('learning.complete', $first->id))
            ->assertSessionHas('success', 'Đã đánh dấu hoàn thành bài học.')
            ->assertSessionMissing('certificate_issued');

        $this->assertDatabaseCount('certificates', 0);

        $response = $this->actingAs($student)->post(route('learning.complete', $last->id));

        $certificate = Certificate::query()->sole();
        $response->assertSessionHas('certificate_issued', $certificate->code)
            ->assertSessionHas('success', 'Chúc mừng! Bạn đã hoàn thành khóa học và nhận được chứng chỉ.');

        $this->assertSame($student->id, $certificate->user_id);
        $this->assertSame($course->id, $certificate->course_id);
        $this->assertSame('Nguyễn Văn An', $certificate->recipient_name);
        $this->assertSame('Lập trình PHP & Laravel', $certificate->course_title);
        $this->assertSame('Trần Minh Khoa', $certificate->instructor_name);
        $this->assertSame(2, $certificate->lessons_count);
        $this->assertMatchesRegularExpression('/^EDU-\d{4}-[A-HJKMNP-Z2-9]{4}-[A-HJKMNP-Z2-9]{4}$/', $certificate->code);
    }

    public function test_completing_again_does_not_issue_a_second_certificate(): void
    {
        [, $student, , $lessons] = $this->finishedCourse();

        $this->actingAs($student)->post(route('learning.complete', $lessons[0]->id));
        $this->actingAs($student)
            ->post(route('learning.complete', $lessons[1]->id))
            ->assertSessionMissing('certificate_issued')
            ->assertSessionHas('success', 'Đã đánh dấu hoàn thành bài học.');

        $this->assertDatabaseCount('certificates', 1);
    }

    public function test_json_response_includes_certificate_url(): void
    {
        $instructor = User::factory()->instructor()->create();
        $student = User::factory()->create();
        $course = $this->createCourse($instructor);
        [$lesson] = $this->addLessons($course, 1);
        $this->enroll($student, $course);

        $response = $this->actingAs($student)
            ->postJson(route('learning.complete', $lesson->id))
            ->assertOk()
            ->assertJson(['success' => true, 'progress_percent' => 100]);

        $this->assertSame(Certificate::query()->sole()->url(), $response->json('certificate_url'));
    }

    public function test_player_shows_certificate_banner_after_finishing(): void
    {
        [, $student, $course] = $this->finishedCourse();
        $certificate = app(CertificateIssuer::class)->issue($student, $course);

        $this->actingAs($student)
            ->get(route('learning.player', $course->slug))
            ->assertOk()
            ->assertSee('Bạn đã hoàn thành khóa học này')
            ->assertSee(route('certificates.show', $certificate->code), false);
    }

    public function test_player_has_no_banner_while_course_is_unfinished(): void
    {
        $instructor = User::factory()->instructor()->create();
        $student = User::factory()->create();
        $course = $this->createCourse($instructor);
        [$first] = $this->addLessons($course, 2);
        $this->enroll($student, $course, 50);
        $this->completeLessons($student, [$first]);

        $this->actingAs($student)
            ->get(route('learning.player', $course->slug))
            ->assertOk()
            ->assertDontSee('Xem chứng chỉ');
    }

    public function test_unfinished_course_redirects_back_to_player_with_progress(): void
    {
        $instructor = User::factory()->instructor()->create();
        $student = User::factory()->create();
        $course = $this->createCourse($instructor);
        [$first] = $this->addLessons($course, 3);
        $this->enroll($student, $course, 33);
        $this->completeLessons($student, [$first]);

        $this->actingAs($student)
            ->get(route('courses.certificate', $course->slug))
            ->assertRedirect(route('learning.player', $course->slug))
            ->assertSessionHas('error', 'Học xong tất cả bài học để nhận chứng chỉ nhé (bạn đã xong 1/3 bài).');

        $this->assertDatabaseCount('certificates', 0);
    }

    public function test_course_with_no_lessons_never_issues_a_certificate(): void
    {
        $instructor = User::factory()->instructor()->create();
        $student = User::factory()->create();
        $course = $this->createCourse($instructor);
        $this->enroll($student, $course, 100);

        $this->assertNull(app(CertificateIssuer::class)->issue($student, $course));
    }

    public function test_instructor_does_not_get_a_certificate_for_own_course(): void
    {
        $instructor = User::factory()->instructor()->create();
        $course = $this->createCourse($instructor);
        $lessons = $this->addLessons($course, 1);
        $this->completeLessons($instructor, $lessons);

        $this->actingAs($instructor)
            ->get(route('courses.certificate', $course->slug))
            ->assertRedirect(route('learning.player', $course->slug))
            ->assertSessionHas('error', 'Giảng viên không nhận chứng chỉ cho khóa học của chính mình.');

        $this->assertDatabaseCount('certificates', 0);
    }

    public function test_paid_course_requires_enrollment(): void
    {
        $instructor = User::factory()->instructor()->create();
        $student = User::factory()->create();
        $course = $this->createCourse($instructor, 499000);
        $lessons = $this->addLessons($course, 1);
        $this->completeLessons($student, $lessons);

        $issuer = app(CertificateIssuer::class);
        $this->assertNull($issuer->issue($student, $course));

        $this->enroll($student, $course, 100);
        $this->assertNotNull($issuer->issue($student, $course));
    }

    public function test_view_certificate_button_issues_missing_certificate_and_opens_it(): void
    {
        [, $student, $course] = $this->finishedCourse();

        $response = $this->actingAs($student)->get(route('courses.certificate', $course->slug));

        $certificate = Certificate::query()->sole();
        $response->assertRedirect(route('certificates.show', $certificate->code));
    }

    public function test_my_certificates_page_backfills_and_lists_certificates(): void
    {
        [, $student, $course] = $this->finishedCourse();

        $unfinished = $this->createCourse(User::factory()->instructor()->create(), 0, 'Thiết kế UI/UX căn bản');
        $this->addLessons($unfinished, 4);
        $this->enroll($student, $unfinished, 25);

        $this->actingAs($student)
            ->get(route('certificates.index'))
            ->assertOk()
            ->assertSee('Chứng chỉ của tôi')
            ->assertSee('Lập trình PHP &amp; Laravel', false)
            ->assertSee('Sắp nhận chứng chỉ')
            ->assertSee('Thiết kế UI/UX căn bản');

        $this->assertDatabaseHas('certificates', ['user_id' => $student->id, 'course_id' => $course->id]);
    }

    public function test_my_certificates_page_shows_empty_state(): void
    {
        $student = User::factory()->create();

        $this->actingAs($student)
            ->get(route('certificates.index'))
            ->assertOk()
            ->assertSee('Bạn chưa có chứng chỉ nào');
    }

    public function test_my_certificates_page_requires_login(): void
    {
        $this->get(route('certificates.index'))->assertRedirect(route('login'));
    }

    public function test_guest_can_view_and_verify_a_certificate(): void
    {
        [, $student, $course] = $this->finishedCourse();
        $certificate = app(CertificateIssuer::class)->issue($student, $course);

        $this->get(route('certificates.show', $certificate->code))
            ->assertOk()
            ->assertSee('Chứng chỉ hợp lệ')
            ->assertSee('Nguyễn Văn An')
            ->assertSee('Lập trình PHP &amp; Laravel', false)
            ->assertSee('Trần Minh Khoa')
            ->assertSee($certificate->code)
            ->assertSee('CHỨNG NHẬN')
            ->assertDontSee('Tải PDF / In');
    }

    public function test_owner_sees_download_and_share_actions(): void
    {
        [, $student, $course] = $this->finishedCourse();
        $certificate = app(CertificateIssuer::class)->issue($student, $course);

        $this->actingAs($student)
            ->get(route('certificates.show', $certificate->code))
            ->assertOk()
            ->assertSee('Tải PDF / In')
            ->assertSee('linkedin.com/profile/add', false)
            ->assertDontSee('Chứng chỉ hợp lệ')
            ->assertDontSee('Cập nhật tên trên chứng chỉ');
    }

    public function test_certificate_code_lookup_is_case_insensitive(): void
    {
        [, $student, $course] = $this->finishedCourse();
        $certificate = app(CertificateIssuer::class)->issue($student, $course);

        $this->get(route('certificates.show', strtolower($certificate->code)))->assertOk();

        $this->get(route('certificates.verify', ['code' => ' '.strtolower($certificate->code).' ']))
            ->assertRedirect(route('certificates.show', $certificate->code));
    }

    public function test_unknown_code_shows_not_found(): void
    {
        $this->get(route('certificates.show', 'EDU-2026-AAAA-BBBB'))
            ->assertNotFound()
            ->assertSee('Không tìm thấy chứng chỉ');

        $this->get(route('certificates.verify', ['code' => 'EDU-2026-AAAA-BBBB']))
            ->assertOk()
            ->assertSee('Không tìm thấy chứng chỉ');
    }

    public function test_verify_page_shows_lookup_form(): void
    {
        $this->get(route('certificates.verify'))
            ->assertOk()
            ->assertSee('Tra cứu chứng chỉ')
            ->assertDontSee('Không tìm thấy chứng chỉ');
    }

    public function test_owner_can_refresh_name_after_changing_profile(): void
    {
        [, $student, $course] = $this->finishedCourse();
        $certificate = app(CertificateIssuer::class)->issue($student, $course);
        $student->update(['name' => 'Nguyễn Văn Bình An']);

        $this->actingAs($student)
            ->get(route('certificates.show', $certificate->code))
            ->assertSee('Cập nhật tên trên chứng chỉ');

        $this->actingAs($student)
            ->post(route('certificates.refresh-name', $certificate))
            ->assertRedirect(route('certificates.show', $certificate->code))
            ->assertSessionHas('success', 'Đã cập nhật tên trên chứng chỉ.');

        $this->assertSame('Nguyễn Văn Bình An', $certificate->fresh()->recipient_name);
    }

    public function test_other_users_cannot_refresh_the_name(): void
    {
        [, $student, $course] = $this->finishedCourse();
        $certificate = app(CertificateIssuer::class)->issue($student, $course);
        $stranger = User::factory()->create(['name' => 'Kẻ lạ']);

        $this->actingAs($stranger)
            ->post(route('certificates.refresh-name', $certificate))
            ->assertForbidden();

        $this->assertSame('Nguyễn Văn An', $certificate->fresh()->recipient_name);
    }

    public function test_certificate_keeps_course_title_when_course_is_renamed(): void
    {
        [, $student, $course] = $this->finishedCourse();
        $certificate = app(CertificateIssuer::class)->issue($student, $course);

        $course->update(['title' => 'Tên mới của khóa học']);

        $this->get(route('certificates.show', $certificate->code))
            ->assertSee('Lập trình PHP &amp; Laravel', false)
            ->assertDontSee('Tên mới của khóa học');
    }

    public function test_dashboard_links_to_certificate_for_finished_course(): void
    {
        [, $student, $course] = $this->finishedCourse();

        $this->actingAs($student)
            ->get(route('my-courses'))
            ->assertOk()
            ->assertSee('Xem chứng chỉ')
            ->assertSee(route('courses.certificate', $course->slug), false);
    }
}
