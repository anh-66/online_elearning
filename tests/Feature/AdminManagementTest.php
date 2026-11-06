<?php

namespace Tests\Feature;

use App\Models\Banner;
use App\Models\Category;
use App\Models\Chapter;
use App\Models\Course;
use App\Models\Lesson;
use App\Models\LessonComment;
use App\Models\Review;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Str;
use Tests\TestCase;

class AdminManagementTest extends TestCase
{
    use RefreshDatabase;

    private User $admin;

    private User $instructor;

    private User $student;

    protected function setUp(): void
    {
        parent::setUp();
        $this->withoutVite();

        $this->admin = User::factory()->create(['role' => User::ROLE_ADMIN, 'status' => User::STATUS_ACTIVE]);
        $this->instructor = User::factory()->create(['role' => User::ROLE_INSTRUCTOR, 'status' => User::STATUS_ACTIVE]);
        $this->student = User::factory()->create(['role' => User::ROLE_STUDENT, 'status' => User::STATUS_ACTIVE]);
    }

    private function createCourse(User $instructor, ?Category $category = null, string $title = 'Khóa học mẫu', string $status = Course::STATUS_PUBLISHED): Course
    {
        return Course::create([
            'instructor_id' => $instructor->id,
            'category_id' => $category?->id,
            'title' => $title,
            'slug' => Str::slug($title).'-'.Str::random(5),
            'description' => 'Mô tả khóa học',
            'price' => 0,
            'level' => 'beginner',
            'status' => $status,
        ]);
    }

    // ==========================================
    // 1. Phân quyền và Bảo mật (CheckRole)
    // ==========================================
    public function test_guest_is_redirected_to_login_when_accessing_admin(): void
    {
        $response = $this->get(route('admin.dashboard'));
        $response->assertRedirect(route('login'));
    }

    public function test_student_receives_403_when_accessing_admin(): void
    {
        $response = $this->actingAs($this->student)->get(route('admin.dashboard'));
        $response->assertForbidden();
    }

    public function test_instructor_receives_403_when_accessing_admin(): void
    {
        $response = $this->actingAs($this->instructor)->get(route('admin.dashboard'));
        $response->assertForbidden();
    }

    public function test_admin_can_access_admin_dashboard(): void
    {
        $response = $this->actingAs($this->admin)->get(route('admin.dashboard'));
        $response->assertOk();
        $response->assertViewHas(['stats', 'enrollmentChart', 'courseStatusChart']);
    }

    // ==========================================
    // 2. Quản lý Người Dùng (User Management)
    // ==========================================
    public function test_admin_can_list_and_filter_users(): void
    {
        $response = $this->actingAs($this->admin)->get(route('admin.users.index', [
            'role' => 'student',
            'status' => 'active',
        ]));

        $response->assertOk();
        $response->assertViewHas('users');
    }

    public function test_admin_can_change_user_role(): void
    {
        $response = $this->actingAs($this->admin)->patch(route('admin.users.update-role', $this->student), [
            'role' => User::ROLE_INSTRUCTOR,
        ]);

        $response->assertSessionHas('success');
        $this->assertEquals(User::ROLE_INSTRUCTOR, $this->student->fresh()->role);
    }

    public function test_admin_cannot_change_own_role(): void
    {
        $response = $this->actingAs($this->admin)->patch(route('admin.users.update-role', $this->admin), [
            'role' => User::ROLE_STUDENT,
        ]);

        $response->assertSessionHas('error');
        $this->assertEquals(User::ROLE_ADMIN, $this->admin->fresh()->role);
    }

    public function test_admin_can_toggle_user_status(): void
    {
        $response = $this->actingAs($this->admin)->patch(route('admin.users.toggle-status', $this->student));
        $response->assertSessionHas('success');
        $this->assertEquals(User::STATUS_BLOCKED, $this->student->fresh()->status);

        // Toggle back to active
        $this->actingAs($this->admin)->patch(route('admin.users.toggle-status', $this->student));
        $this->assertEquals(User::STATUS_ACTIVE, $this->student->fresh()->status);
    }

    public function test_admin_cannot_block_own_account(): void
    {
        $response = $this->actingAs($this->admin)->patch(route('admin.users.toggle-status', $this->admin));
        $response->assertSessionHas('error');
        $this->assertEquals(User::STATUS_ACTIVE, $this->admin->fresh()->status);
    }

    public function test_admin_can_update_user_information(): void
    {
        $response = $this->actingAs($this->admin)->put(route('admin.users.update', $this->student), [
            'name' => 'Updated Name',
            'email' => 'updated_student@example.com',
            'role' => User::ROLE_STUDENT,
            'status' => User::STATUS_ACTIVE,
        ]);

        $response->assertRedirect(route('admin.users.index'));
        $this->assertDatabaseHas('users', ['email' => 'updated_student@example.com', 'name' => 'Updated Name']);
    }

    public function test_admin_cannot_delete_own_account(): void
    {
        $response = $this->actingAs($this->admin)->delete(route('admin.users.destroy', $this->admin));
        $response->assertSessionHas('error');
        $this->assertDatabaseHas('users', ['id' => $this->admin->id]);
    }

    public function test_admin_can_delete_other_user(): void
    {
        $targetUser = User::factory()->create();

        $response = $this->actingAs($this->admin)->delete(route('admin.users.destroy', $targetUser));
        $response->assertRedirect(route('admin.users.index'));
        $this->assertDatabaseMissing('users', ['id' => $targetUser->id]);
    }

    // ==========================================
    // 3. Quản lý Khóa Học (Course Moderation)
    // ==========================================
    public function test_admin_can_approve_pending_course(): void
    {
        $course = $this->createCourse($this->instructor, null, 'Khóa chờ duyệt', Course::STATUS_PENDING);
        $course->update(['reject_reason' => 'Some old note']);

        $response = $this->actingAs($this->admin)->patch(route('admin.courses.approve', $course));
        $response->assertSessionHas('success');

        $fresh = $course->fresh();
        $this->assertEquals(Course::STATUS_PUBLISHED, $fresh->status);
        $this->assertNull($fresh->reject_reason);
    }

    public function test_admin_can_reject_pending_course_with_reason(): void
    {
        $course = $this->createCourse($this->instructor, null, 'Khóa cần từ chối', Course::STATUS_PENDING);

        $response = $this->actingAs($this->admin)->patch(route('admin.courses.reject', $course), [
            'reject_reason' => 'Nội dung chưa đầy đủ đề cương chi tiết.',
        ]);

        $response->assertSessionHas('success');
        $fresh = $course->fresh();
        $this->assertEquals(Course::STATUS_REJECTED, $fresh->status);
        $this->assertEquals('Nội dung chưa đầy đủ đề cương chi tiết.', $fresh->reject_reason);
    }

    public function test_admin_can_hide_published_course(): void
    {
        $course = $this->createCourse($this->instructor, null, 'Khóa cần ẩn', Course::STATUS_PUBLISHED);

        $response = $this->actingAs($this->admin)->patch(route('admin.courses.hide', $course));
        $response->assertSessionHas('success');
        $this->assertEquals(Course::STATUS_DRAFT, $course->fresh()->status);
    }

    public function test_admin_can_delete_violating_course(): void
    {
        $course = $this->createCourse($this->instructor, null, 'Khóa vi phạm', Course::STATUS_REJECTED);

        $response = $this->actingAs($this->admin)->delete(route('admin.courses.destroy', $course));
        $response->assertSessionHas('success');
        $this->assertDatabaseMissing('courses', ['id' => $course->id]);
    }

    // ==========================================
    // 4. Quản lý Danh Mục (Category CRUD)
    // ==========================================
    public function test_admin_can_create_category_with_unique_slug(): void
    {
        $response = $this->actingAs($this->admin)->post(route('admin.categories.store'), [
            'name' => 'Lập Trình Di Động',
            'description' => 'Khóa học Flutter & React Native',
        ]);

        $response->assertRedirect(route('admin.categories.index'));
        $this->assertDatabaseHas('categories', [
            'name' => 'Lập Trình Di Động',
            'slug' => 'lap-trinh-di-dong',
        ]);
    }

    public function test_admin_cannot_delete_category_that_has_courses(): void
    {
        $category = Category::create(['name' => 'Frontend', 'slug' => 'frontend']);
        $this->createCourse($this->instructor, $category, 'Khóa Frontend 1');

        $response = $this->actingAs($this->admin)->delete(route('admin.categories.destroy', $category));
        $response->assertSessionHas('error');
        $this->assertDatabaseHas('categories', ['id' => $category->id]);
    }

    // ==========================================
    // 5. Quản lý Đánh Giá & Bình Luận
    // ==========================================
    public function test_admin_can_delete_review(): void
    {
        $course = $this->createCourse($this->instructor, null, 'Khóa review');
        $review = Review::create([
            'user_id' => $this->student->id,
            'course_id' => $course->id,
            'rating' => 1,
            'comment' => 'Review có nội dung spam',
        ]);

        $response = $this->actingAs($this->admin)->delete(route('admin.reviews.destroy', $review));
        $response->assertSessionHas('success');
        $this->assertDatabaseMissing('reviews', ['id' => $review->id]);
    }

    public function test_admin_can_delete_comment(): void
    {
        $course = $this->createCourse($this->instructor, null, 'Khóa comment');
        $chapter = Chapter::create([
            'course_id' => $course->id,
            'title' => 'Chương 1',
            'order' => 1,
        ]);
        $lesson = Lesson::create([
            'chapter_id' => $chapter->id,
            'title' => 'Bài 1',
            'order' => 1,
        ]);
        $comment = LessonComment::create([
            'user_id' => $this->student->id,
            'lesson_id' => $lesson->id,
            'content' => 'Bình luận vi phạm',
        ]);

        $response = $this->actingAs($this->admin)->delete(route('admin.reviews.destroy-comment', $comment));
        $response->assertSessionHas('success');
        $this->assertDatabaseMissing('lesson_comments', ['id' => $comment->id]);
    }

    // ==========================================
    // 6. Quản lý Thông Báo (Notifications)
    // ==========================================
    public function test_admin_can_create_notification(): void
    {
        $response = $this->actingAs($this->admin)->post(route('admin.notifications.store'), [
            'title' => 'Bảo trì máy chủ cuối tuần',
            'type' => 'system',
            'content' => 'Hệ thống sẽ bảo trì từ 23:00 thứ Bảy đến 02:00 Chủ Nhật.',
        ]);

        $response->assertRedirect(route('admin.notifications.index'));
        $this->assertDatabaseHas('notifications', [
            'title' => 'Bảo trì máy chủ cuối tuần',
            'type' => 'system',
        ]);
    }

    // ==========================================
    // 7. Quản lý Banner & Toggle
    // ==========================================
    public function test_admin_can_toggle_banner_status(): void
    {
        $banner = Banner::create([
            'title' => 'Banner Test',
            'image_path' => 'banners/test.jpg',
            'is_active' => true,
            'order' => 1,
        ]);

        $response = $this->actingAs($this->admin)->patch(route('admin.banners.toggle', $banner));
        $response->assertSessionHas('success');
        $this->assertFalse((bool) $banner->fresh()->is_active);

        // Toggle back to active
        $this->actingAs($this->admin)->patch(route('admin.banners.toggle', $banner));
        $this->assertTrue((bool) $banner->fresh()->is_active);
    }
}
