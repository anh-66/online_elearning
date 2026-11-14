<?php

namespace Tests\Feature;

use App\Models\Banner;
use App\Models\Category;
use App\Models\Course;
use App\Models\Enrollment;
use App\Models\Notification;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PublicPagesTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        $this->withoutVite();
    }

    public function test_public_pages_show_published_courses_only(): void
    {
        $instructor = User::factory()->create(['role' => User::ROLE_INSTRUCTOR]);
        $category = Category::create(['name' => 'Lập trình Web', 'slug' => 'lap-trinh-web']);
        $published = $this->createCourse($instructor, $category, 'Laravel căn bản', Course::STATUS_PUBLISHED);
        $draft = $this->createCourse($instructor, $category, 'Khóa nháp bí mật', Course::STATUS_DRAFT);

        $this->get(route('home'))
            ->assertOk()
            ->assertSee($published->title)
            ->assertSee('cookie-consent')
            ->assertDontSee($draft->title);
        $this->get(route('courses.index'))
            ->assertOk()
            ->assertSee($published->title)
            ->assertDontSee('cookie-consent')
            ->assertDontSee($draft->title);
        $this->get(route('courses.index', ['keyword' => 'Laravel']))->assertOk()->assertSee($published->title);
        $this->get(route('courses.show', $published->slug))->assertOk()->assertSee($published->title);
        $this->get(route('courses.show', $draft->slug))->assertNotFound();
        $this->get(route('instructors.show', $instructor->id))->assertOk()->assertSee($published->title);
    }

    public function test_homepage_shows_banner_image_without_external_notification_panel(): void
    {
        Banner::create([
            'title' => 'Banner khóa học mới',
            'image_path' => 'banners/home.jpg',
            'is_active' => true,
        ]);
        Notification::create([
            'title' => 'Thông báo trang chủ',
            'content' => 'Nội dung thông báo',
            'type' => 'system',
        ]);

        $this->get(route('home'))
            ->assertOk()
            ->assertDontSee('Thông báo từ ban quản trị')
            ->assertSee('storage/banners/home.jpg');

        $this->actingAs(User::factory()->create())
            ->get(route('home'))
            ->assertOk()
            ->assertDontSee('cookie-consent');
    }

    public function test_student_dashboard_lists_enrolled_courses(): void
    {
        $instructor = User::factory()->create(['role' => User::ROLE_INSTRUCTOR]);
        $category = Category::create(['name' => 'Dữ liệu', 'slug' => 'du-lieu']);
        $enrolled = $this->createCourse($instructor, $category, 'Phân tích dữ liệu', Course::STATUS_PUBLISHED);
        $other = $this->createCourse($instructor, $category, 'Khóa chưa ghi danh', Course::STATUS_PUBLISHED);

        $student = User::factory()->create();
        Enrollment::create(['user_id' => $student->id, 'course_id' => $enrolled->id, 'enrolled_at' => now()]);

        $this->actingAs($student)
            ->get(route('dashboard'))
            ->assertOk()
            ->assertSee($enrolled->title)
            ->assertDontSee($other->title);
    }

    public function test_student_dashboard_shows_empty_state(): void
    {
        $this->actingAs(User::factory()->create())
            ->get(route('dashboard'))
            ->assertOk()
            ->assertSee('Bạn chưa ghi danh khóa học nào');
    }

    public function test_student_can_view_all_enrolled_courses_without_external_notification_panel(): void
    {
        $instructor = User::factory()->create(['role' => User::ROLE_INSTRUCTOR]);
        $category = Category::create(['name' => 'Thiết kế', 'slug' => 'thiet-ke']);
        $course = $this->createCourse($instructor, $category, 'Thiết kế giao diện', Course::STATUS_PUBLISHED);
        $student = User::factory()->create();

        Enrollment::create(['user_id' => $student->id, 'course_id' => $course->id, 'enrolled_at' => now()]);
        Notification::create([
            'title' => 'Thông báo học tập',
            'content' => 'Nội dung thông báo mới nhất',
            'type' => 'system',
        ]);

        $this->actingAs($student)
            ->get(route('my-courses'))
            ->assertOk()
            ->assertSee($course->title)
            ->assertSee('Thông báo học tập')
            ->assertDontSee('Thông báo từ ban quản trị');
    }

    public function test_support_center_page_renders_with_categories_and_search(): void
    {
        $this->get(route('support.index'))
            ->assertOk()
            ->assertSee('Hỗ Trợ EduLearn')
            ->assertSee('Tài khoản & Mật khẩu')
            ->assertSee('Gói đăng ký & Học phí')
            ->assertSee('Tìm kiếm các chủ đề khác');
    }

    public function test_support_article_page_renders_with_step_by_step_guide(): void
    {
        $this->get(route('support.show', 'doi-mat-khau'))
            ->assertOk()
            ->assertSee('Đổi mật khẩu tài khoản EduLearn của bạn')
            ->assertSee('Đăng nhập vào tài khoản EduLearn')
            ->assertSee('Cập nhật mật khẩu');

        $this->get(route('support.show', 'dat-lai-mat-khau'))
            ->assertOk()
            ->assertSee('Đặt lại mật khẩu tài khoản khi bị quên');

        $this->get(route('support.show', 'goi-dang-ky'))
            ->assertOk()
            ->assertSee('Quản lý gói đăng ký và thanh toán học phí');
    }

    public function test_support_article_returns_404_for_unknown_slug(): void
    {
        $this->get(route('support.show', 'khong-ton-tai'))
            ->assertNotFound();
    }

    private function createCourse(User $instructor, Category $category, string $title, string $status): Course
    {
        return Course::create([
            'instructor_id' => $instructor->id,
            'category_id' => $category->id,
            'title' => $title,
            'slug' => str($title)->slug().'-'.fake()->unique()->randomNumber(5),
            'price' => 0,
            'level' => Course::LEVEL_BEGINNER,
            'status' => $status,
        ]);
    }
}
