<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\Chapter;
use App\Models\Course;
use App\Models\Enrollment;
use App\Models\Lesson;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CourseEnrollmentTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->withoutVite();
    }

    public function test_instructor_a_cannot_freely_enroll_in_paid_course_of_instructor_b(): void
    {
        $instructorA = User::factory()->create(['role' => User::ROLE_INSTRUCTOR, 'name' => 'Thầy Giáo A']);
        $instructorB = User::factory()->create(['role' => User::ROLE_INSTRUCTOR, 'name' => 'Thầy Giáo B']);
        $category = Category::create(['name' => 'Lập trình', 'slug' => 'lap-trinh']);

        $paidCourse = Course::create([
            'instructor_id' => $instructorB->id,
            'category_id' => $category->id,
            'title' => 'Khóa học Python 799k',
            'slug' => 'khoa-hoc-python-799k',
            'price' => 799000,
            'status' => Course::STATUS_PUBLISHED,
        ]);

        $chapter = Chapter::create([
            'course_id' => $paidCourse->id,
            'title' => 'Chương 1',
            'order' => 1,
        ]);

        Lesson::create([
            'chapter_id' => $chapter->id,
            'title' => 'Bài 1',
            'order' => 1,
            'content' => 'Nội dung bài học',
        ]);

        // 1. Thầy giáo A xem trang khóa học của Thầy giáo B -> Thấy nút Mua khóa học 799.000 ₫
        $this->actingAs($instructorA)
            ->get(route('courses.show', $paidCourse->slug))
            ->assertOk()
            ->assertSee('Mua khóa học ngay · 799.000 ₫')
            ->assertSee(route('courses.checkout', $paidCourse->slug));

        // 2. Thầy giáo A cố tình gọi POST enroll miễn phí -> Bị từ chối và redirect sang checkout
        $this->actingAs($instructorA)
            ->post(route('courses.enroll', $paidCourse->id))
            ->assertRedirect(route('courses.checkout', $paidCourse->slug))
            ->assertSessionHas('error');

        // Đảm bảo không có bản ghi enrollment miễn phí nào được tạo
        $this->assertDatabaseMissing('enrollments', [
            'user_id' => $instructorA->id,
            'course_id' => $paidCourse->id,
        ]);

        // 3. Thầy giáo A cố tình vào thẳng phòng học -> Bị chặn
        $this->actingAs($instructorA)
            ->get(route('learning.player', $paidCourse->slug))
            ->assertRedirect(route('courses.show', $paidCourse->slug))
            ->assertSessionHas('error');

        // 4. Thầy giáo A vào trang thanh toán và hoàn tất mua khóa học
        $this->actingAs($instructorA)
            ->get(route('courses.checkout', $paidCourse->slug))
            ->assertOk()
            ->assertSee('799.000 ₫')
            ->assertSee('Thanh toán học phí');

        $this->actingAs($instructorA)
            ->post(route('courses.checkout.process', $paidCourse->slug))
            ->assertRedirect(route('learning.player', $paidCourse->slug))
            ->assertSessionHas('success');

        // 5. Sau khi thanh toán, Thầy giáo A đã sở hữu và vào học bình thường
        $this->assertDatabaseHas('enrollments', [
            'user_id' => $instructorA->id,
            'course_id' => $paidCourse->id,
            'status' => Enrollment::STATUS_PAID,
        ]);

        $this->actingAs($instructorA)
            ->get(route('learning.player', $paidCourse->slug))
            ->assertOk()
            ->assertSee('Bài 1');
    }

    public function test_course_owner_cannot_enroll_or_checkout_own_course(): void
    {
        $instructor = User::factory()->create(['role' => User::ROLE_INSTRUCTOR]);
        $category = Category::create(['name' => 'Web', 'slug' => 'web']);

        $course = Course::create([
            'instructor_id' => $instructor->id,
            'category_id' => $category->id,
            'title' => 'Khóa của chính tôi',
            'slug' => 'khoa-cua-chinh-toi',
            'price' => 500000,
            'status' => Course::STATUS_PUBLISHED,
        ]);

        // Thấy nút Vào phòng học (Xem trước) và Studio
        $this->actingAs($instructor)
            ->get(route('courses.show', $course->slug))
            ->assertOk()
            ->assertSee('Vào phòng học (Xem trước)')
            ->assertSee('Quản lý nội dung (Studio)')
            ->assertDontSee('Mua khóa học ngay');

        // Cố tình ghi danh -> Bị chặn
        $this->actingAs($instructor)
            ->post(route('courses.enroll', $course->id))
            ->assertRedirect(route('courses.show', $course->slug))
            ->assertSessionHas('error');

        // Cố tình checkout -> Bị chặn
        $this->actingAs($instructor)
            ->get(route('courses.checkout', $course->slug))
            ->assertRedirect(route('courses.show', $course->slug));
    }

    public function test_free_course_allows_direct_enrollment(): void
    {
        $student = User::factory()->create();
        $instructor = User::factory()->create(['role' => User::ROLE_INSTRUCTOR]);
        $category = Category::create(['name' => 'AI', 'slug' => 'ai']);

        $freeCourse = Course::create([
            'instructor_id' => $instructor->id,
            'category_id' => $category->id,
            'title' => 'Khóa miễn phí',
            'slug' => 'khoa-mien-phi',
            'price' => 0,
            'status' => Course::STATUS_PUBLISHED,
        ]);

        $chapter = Chapter::create([
            'course_id' => $freeCourse->id,
            'title' => 'Chương 1',
            'order' => 1,
        ]);

        Lesson::create([
            'chapter_id' => $chapter->id,
            'title' => 'Bài học mở đầu',
            'order' => 1,
            'content' => 'Nội dung bài học',
        ]);

        // Học viên thấy nút Ghi danh học miễn phí
        $this->actingAs($student)
            ->get(route('courses.show', $freeCourse->slug))
            ->assertOk()
            ->assertSee('Ghi danh học miễn phí');

        // Ghi danh thành công
        $this->actingAs($student)
            ->post(route('courses.enroll', $freeCourse->id))
            ->assertRedirect(route('learning.player', $freeCourse->slug))
            ->assertSessionHas('success');

        $this->assertDatabaseHas('enrollments', [
            'user_id' => $student->id,
            'course_id' => $freeCourse->id,
            'status' => Enrollment::STATUS_FREE,
        ]);
    }

    public function test_checkout_saves_payment_method_and_reuses_it_in_profile_and_next_checkout(): void
    {
        $student = User::factory()->create(['name' => 'Nguyễn Văn Học Viên']);
        $instructor = User::factory()->create(['role' => User::ROLE_INSTRUCTOR]);
        $category = Category::create(['name' => 'Thiết kế', 'slug' => 'thiet-ke']);

        $course1 = Course::create([
            'instructor_id' => $instructor->id,
            'category_id' => $category->id,
            'title' => 'Khóa UI/UX Pro',
            'slug' => 'khoa-ui-ux-pro',
            'price' => 1990000,
            'status' => Course::STATUS_PUBLISHED,
        ]);

        $course2 = Course::create([
            'instructor_id' => $instructor->id,
            'category_id' => $category->id,
            'title' => 'Khóa Figma Master',
            'slug' => 'khoa-figma-master',
            'price' => 890000,
            'status' => Course::STATUS_PUBLISHED,
        ]);

        // 1. Lần đầu checkout khóa 1 với MoMo
        $this->actingAs($student)
            ->post(route('courses.checkout.process', $course1->slug), [
                'payment_type' => 'momo',
                'momo_phone' => '0988 123 456',
            ])
            ->assertRedirect(route('learning.player', $course1->slug))
            ->assertSessionHas('success');

        // Bản ghi payment_methods được tạo
        $this->assertDatabaseHas('payment_methods', [
            'user_id' => $student->id,
            'type' => 'momo',
            'is_default' => true,
        ]);

        // 2. Vào trang Cài đặt tài khoản (Profile Billing) -> Thấy thông tin MoMo hiển thị động
        $this->actingAs($student)
            ->get(route('profile.edit'))
            ->assertOk()
            ->assertSee('Ví điện tử MoMo')
            ->assertSee('0988***456');

        // 3. Đến trang checkout khóa 2 -> Thấy phương thức đã lưu và thanh toán trực tiếp
        $this->actingAs($student)
            ->get(route('courses.checkout', $course2->slug))
            ->assertOk()
            ->assertSee('Đã lưu trong tài khoản')
            ->assertSee('Ví điện tử MoMo')
            ->assertSee('0988***456');

        $this->actingAs($student)
            ->post(route('courses.checkout.process', $course2->slug), [
                'payment_type' => 'saved',
            ])
            ->assertRedirect(route('learning.player', $course2->slug))
            ->assertSessionHas('success');

        $this->assertDatabaseHas('enrollments', [
            'user_id' => $student->id,
            'course_id' => $course2->id,
            'status' => Enrollment::STATUS_PAID,
        ]);
    }
}
