<?php

namespace Tests\Feature\Admin;

use App\Models\Banner;
use App\Models\Category;
use App\Models\Course;
use App\Models\Enrollment;
use App\Models\Notification;
use App\Models\Review;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class AdminModuleTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        $this->withoutVite();
    }

    private function admin(): User
    {
        return User::factory()->create(['role' => User::ROLE_ADMIN, 'status' => User::STATUS_ACTIVE]);
    }

    private function makeCourse(string $status = Course::STATUS_PENDING, ?Category $category = null): Course
    {
        $instructor = User::factory()->create(['role' => User::ROLE_INSTRUCTOR]);

        return Course::create([
            'instructor_id' => $instructor->id,
            'category_id' => $category?->id,
            'title' => 'Khóa học '.uniqid(),
            'slug' => 'khoa-hoc-'.uniqid(),
            'status' => $status,
        ]);
    }

    public function test_non_admin_cannot_access_admin_pages(): void
    {
        $student = User::factory()->create(['role' => User::ROLE_STUDENT]);

        foreach (['dashboard', 'users.index', 'courses.index', 'categories.index', 'reviews.index', 'enrollments.index', 'notifications.index', 'banners.index'] as $route) {
            $this->actingAs($student)->get(route('admin.'.$route))->assertForbidden();
        }
    }

    public function test_all_admin_pages_render(): void
    {
        $this->actingAs($this->admin());
        $course = $this->makeCourse();
        Category::create(['name' => 'Lập trình', 'slug' => 'lap-trinh']);

        foreach (['dashboard', 'users.index', 'courses.index', 'categories.index', 'categories.create', 'reviews.index', 'enrollments.index', 'notifications.index', 'notifications.create', 'banners.index', 'banners.create'] as $route) {
            $this->get(route('admin.'.$route))->assertOk();
        }

        $this->get(route('admin.reviews.index', ['tab' => 'comments']))->assertOk();
        $this->get(route('admin.courses.index', ['status' => 'all']))->assertOk()->assertSee($course->title);
    }

    public function test_dashboard_chart_data_counts_enrollments_by_month(): void
    {
        $this->actingAs($this->admin());
        $course = $this->makeCourse(Course::STATUS_PUBLISHED);
        $students = User::factory()->count(2)->create();

        foreach ($students as $student) {
            Enrollment::create(['user_id' => $student->id, 'course_id' => $course->id, 'status' => 'free', 'enrolled_at' => now()]);
        }

        $response = $this->get(route('admin.dashboard'))->assertOk();

        $chart = $response->viewData('enrollmentChart');
        // Biểu đồ hiển thị đủ 12 tháng (1 -> 12) của năm đang chọn, nên 2 lượt ghi danh nằm ở tháng hiện tại.
        $this->assertCount(12, $chart['labels']);
        $this->assertSame(2, $chart['data'][now()->month - 1]);
        $this->assertSame(2, array_sum($chart['data']));
    }

    public function test_user_search_and_role_filter(): void
    {
        $this->actingAs($this->admin());
        User::factory()->create(['name' => 'Nguyễn Tìm Thấy', 'role' => User::ROLE_STUDENT]);
        User::factory()->create(['name' => 'Giảng Viên Khác', 'role' => User::ROLE_INSTRUCTOR]);

        $this->get(route('admin.users.index', ['keyword' => 'Tìm Thấy']))
            ->assertSee('Nguyễn Tìm Thấy')->assertDontSee('Giảng Viên Khác');

        $this->get(route('admin.users.index', ['role' => 'instructor']))
            ->assertSee('Giảng Viên Khác')->assertDontSee('Nguyễn Tìm Thấy');
    }

    public function test_admin_can_change_role_and_lock_user_but_not_self(): void
    {
        $admin = $this->admin();
        $student = User::factory()->create(['role' => User::ROLE_STUDENT]);

        $this->actingAs($admin);

        $this->patch(route('admin.users.update-role', $student), ['role' => 'instructor'])->assertSessionHas('success');
        $this->assertSame('instructor', $student->fresh()->role);

        $this->patch(route('admin.users.toggle-status', $student))->assertSessionHas('success');
        $this->assertSame(User::STATUS_BLOCKED, $student->fresh()->status);

        $this->patch(route('admin.users.toggle-status', $student));
        $this->assertSame(User::STATUS_ACTIVE, $student->fresh()->status);

        $this->patch(route('admin.users.update-role', $admin), ['role' => 'student'])->assertSessionHas('error');
        $this->patch(route('admin.users.toggle-status', $admin))->assertSessionHas('error');
        $this->assertSame(User::ROLE_ADMIN, $admin->fresh()->role);
        $this->assertSame(User::STATUS_ACTIVE, $admin->fresh()->status);

        $this->patch(route('admin.users.update-role', $student), ['role' => 'hacker'])->assertSessionHasErrors('role');
    }

    public function test_blocked_user_is_logged_out_on_next_request(): void
    {
        $blocked = User::factory()->create(['role' => User::ROLE_ADMIN, 'status' => User::STATUS_BLOCKED]);

        $this->actingAs($blocked)->get(route('admin.dashboard'))->assertRedirect(route('login'));
    }

    public function test_category_crud_generates_unique_slug(): void
    {
        $this->actingAs($this->admin());

        $this->post(route('admin.categories.store'), ['name' => 'Lập trình Web'])->assertRedirect(route('admin.categories.index'));
        $category = Category::firstWhere('name', 'Lập trình Web');
        $this->assertSame('lap-trinh-web', $category->slug);

        $this->post(route('admin.categories.store'), ['name' => 'Lập trình Web'])->assertSessionHasErrors('name');

        $this->post(route('admin.categories.store'), ['name' => 'Lập Trình  Web!']);
        $this->assertSame('lap-trinh-web-2', Category::firstWhere('name', 'Lập Trình  Web!')->slug);

        $this->put(route('admin.categories.update', $category), ['name' => 'Web nâng cao'])->assertRedirect();
        $this->assertSame('web-nang-cao', $category->fresh()->slug);

        // Danh mục còn khóa học thì không cho xóa; chuyển khóa học đi rồi mới xóa được.
        $course = $this->makeCourse(Course::STATUS_PUBLISHED, $category);
        $this->delete(route('admin.categories.destroy', $category))->assertSessionHas('error');
        $this->assertModelExists($category);

        $course->update(['category_id' => null]);
        $this->delete(route('admin.categories.destroy', $category))->assertRedirect(route('admin.categories.index'));
        $this->assertModelMissing($category);
    }

    public function test_admin_can_approve_pending_course(): void
    {
        $this->actingAs($this->admin());
        $course = $this->makeCourse();

        $this->patch(route('admin.courses.approve', $course))->assertSessionHas('success');
        $this->assertSame(Course::STATUS_PUBLISHED, $course->fresh()->status);

        $this->patch(route('admin.courses.approve', $course))->assertSessionHas('error');
    }

    public function test_reject_requires_reason_and_works_for_pending_and_published(): void
    {
        $this->actingAs($this->admin());
        $pending = $this->makeCourse();
        $published = $this->makeCourse(Course::STATUS_PUBLISHED);
        $draft = $this->makeCourse(Course::STATUS_DRAFT);

        $this->patch(route('admin.courses.reject', $pending))->assertSessionHasErrors('reject_reason');
        $this->assertSame(Course::STATUS_PENDING, $pending->fresh()->status);

        $this->patch(route('admin.courses.reject', $pending), ['reject_reason' => 'Nội dung chưa đạt'])->assertSessionHas('success');
        $this->assertSame(Course::STATUS_REJECTED, $pending->fresh()->status);
        $this->assertSame('Nội dung chưa đạt', $pending->fresh()->reject_reason);

        $this->patch(route('admin.courses.reject', $published), ['reject_reason' => 'Vi phạm bản quyền'])->assertSessionHas('success');
        $this->assertSame(Course::STATUS_REJECTED, $published->fresh()->status);

        $this->patch(route('admin.courses.reject', $draft), ['reject_reason' => 'x'])->assertSessionHas('error');
        $this->assertSame(Course::STATUS_DRAFT, $draft->fresh()->status);
    }

    public function test_admin_can_delete_course_and_moderate_reviews_and_enrollments(): void
    {
        $this->actingAs($this->admin());
        $course = $this->makeCourse(Course::STATUS_PUBLISHED);
        $student = User::factory()->create();
        $review = Review::create(['user_id' => $student->id, 'course_id' => $course->id, 'rating' => 1, 'comment' => 'Nội dung vi phạm']);
        $enrollment = Enrollment::create(['user_id' => $student->id, 'course_id' => $course->id, 'status' => 'free']);

        $this->get(route('admin.reviews.index', ['keyword' => 'vi phạm']))->assertSee('Nội dung vi phạm');
        $this->get(route('admin.enrollments.index', ['keyword' => $student->email]))->assertSee($student->name);

        $this->delete(route('admin.reviews.destroy', $review))->assertSessionHas('success');
        $this->assertModelMissing($review);

        $this->delete(route('admin.enrollments.destroy', $enrollment))->assertSessionHas('success');
        $this->assertModelMissing($enrollment);

        $this->delete(route('admin.courses.destroy', $course))->assertSessionHas('success');
        $this->assertModelMissing($course);
    }

    public function test_notification_crud_and_notification_bell_data(): void
    {
        $this->actingAs($this->admin());

        $this->post(route('admin.notifications.store'), ['title' => 'Bảo trì hệ thống', 'content' => 'Tối nay 22h', 'type' => 'system'])
            ->assertRedirect(route('admin.notifications.index'));
        $notification = Notification::firstWhere('title', 'Bảo trì hệ thống');

        $this->post(route('admin.notifications.store'), ['title' => 'x', 'content' => 'y', 'type' => 'khac'])->assertSessionHasErrors('type');

        $this->view('partials.notification-bell')->assertSee('Bảo trì hệ thống');

        $this->put(route('admin.notifications.update', $notification), ['title' => 'Đã đổi tiêu đề', 'content' => 'Nội dung mới', 'type' => 'promotion']);
        $this->assertSame('Đã đổi tiêu đề', $notification->fresh()->title);

        $this->delete(route('admin.notifications.destroy', $notification));
        $this->assertModelMissing($notification);
    }

    public function test_banner_crud_with_image_upload(): void
    {
        Storage::fake('public');
        $this->actingAs($this->admin());

        // Banner không ảnh vẫn hợp lệ (chỉ hiện chữ trên nền màu), không gửi màu thì dùng màu mặc định.
        $this->post(route('admin.banners.store'), ['title' => 'Banner không ảnh'])->assertRedirect(route('admin.banners.index'));
        $textOnly = Banner::firstWhere('title', 'Banner không ảnh');
        $this->assertNull($textOnly->image_path);
        $this->assertSame('#019D91', $textOnly->bg_color);

        $this->post(route('admin.banners.store'), [
            'title' => 'Ảnh sai định dạng',
            'image' => UploadedFile::fake()->create('banner.pdf', 10, 'application/pdf'),
        ])->assertSessionHasErrors('image');

        $this->post(route('admin.banners.store'), [
            'title' => 'Khuyến mãi tháng 11',
            'image' => UploadedFile::fake()->image('banner.jpg', 1200, 400),
            'order' => 1,
            'is_active' => '1',
        ])->assertRedirect(route('admin.banners.index'));

        $banner = Banner::firstWhere('title', 'Khuyến mãi tháng 11');
        $this->assertTrue($banner->is_active);
        Storage::disk('public')->assertExists($banner->image_path);
        $oldPath = $banner->image_path;

        $this->put(route('admin.banners.update', $banner), [
            'title' => 'Khuyến mãi tháng 12',
            'image' => UploadedFile::fake()->image('new.png', 1200, 400),
            'order' => 2,
        ]);
        $banner->refresh();
        $this->assertFalse($banner->is_active);
        Storage::disk('public')->assertMissing($oldPath);
        Storage::disk('public')->assertExists($banner->image_path);

        $path = $banner->image_path;
        $this->delete(route('admin.banners.destroy', $banner));
        $this->assertModelMissing($banner);
        Storage::disk('public')->assertMissing($path);
    }
}
