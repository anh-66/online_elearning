<?php

namespace Tests\Feature;

use App\Enums\UserRole;
use App\Models\Category;
use App\Models\Course;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class CourseManagementTest extends TestCase
{
    use RefreshDatabase;

    private function image(string $name): UploadedFile
    {
        // 1x1 PNG, so the tests do not depend on the GD extension.
        $png = base64_decode('iVBORw0KGgoAAAANSUhEUgAAAAEAAAABCAYAAAAfFcSJAAAADUlEQVR42mNk+M9QDwADhgGAWjR9awAAAABJRU5ErkJggg==');

        return UploadedFile::fake()->createWithContent($name, $png);
    }

    public function test_admin_can_create_course_with_thumbnail_and_multiple_images(): void
    {
        Storage::fake('public');
        $admin = User::factory()->create(['role' => UserRole::Admin]);
        $teacher = User::factory()->create(['role' => UserRole::Teacher]);
        $category = Category::factory()->create();

        $this->actingAs($admin)->post(route('admin.courses.store'), [
            'title' => 'Lập trình PHP cơ bản',
            'summary' => 'Khoá học nhập môn PHP',
            'description' => '<p>Nội dung <strong>chi tiết</strong></p>',
            'category_id' => $category->id,
            'instructor_id' => $teacher->id,
            'price' => 0,
            'level' => 'beginner',
            'status' => 'published',
            'thumbnail' => $this->image('cover.png'),
            'images' => [$this->image('a.png'), $this->image('b.png'), $this->image('c.png')],
        ])->assertRedirect()->assertSessionHasNoErrors();

        $course = Course::firstOrFail();
        $this->assertSame('lap-trinh-php-co-ban', $course->slug);
        $this->assertTrue($course->instructor->is($teacher));
        $this->assertNotNull($course->published_at);
        $this->assertCount(3, $course->images);
        Storage::disk('public')->assertExists([$course->thumbnail, ...$course->images->pluck('path')]);
    }

    public function test_images_can_be_removed_when_updating(): void
    {
        Storage::fake('public');
        $admin = User::factory()->create(['role' => UserRole::Admin]);
        $course = Course::factory()->create();
        $image = $course->images()->create(['path' => $this->image('old.png')->store('courses', 'public'), 'sort_order' => 1]);

        $this->actingAs($admin)->put(route('admin.courses.update', $course), [
            'title' => 'Tên mới',
            'summary' => $course->summary,
            'instructor_id' => $course->instructor_id,
            'price' => 100000,
            'level' => 'advanced',
            'status' => 'draft',
            'delete_images' => [$image->id],
            'images' => [$this->image('new.png')],
        ])->assertSessionHasNoErrors();

        $course->refresh();
        $this->assertSame('Tên mới', $course->title);
        $this->assertCount(1, $course->images);
        $this->assertFalse($course->images->contains($image));
        Storage::disk('public')->assertMissing($image->path);
    }

    public function test_teacher_can_only_manage_own_courses(): void
    {
        $teacher = User::factory()->create(['role' => UserRole::Teacher]);
        $ownCourse = Course::factory()->create(['instructor_id' => $teacher->id]);
        $otherCourse = Course::factory()->create();

        $this->actingAs($teacher);

        $this->get(route('admin.courses.index'))->assertSee($ownCourse->title)->assertDontSee($otherCourse->title);
        $this->get(route('admin.courses.edit', $ownCourse))->assertOk();
        $this->get(route('admin.courses.edit', $otherCourse))->assertForbidden();
        $this->delete(route('admin.courses.destroy', $otherCourse))->assertForbidden();
        $this->assertModelExists($otherCourse);
    }

    public function test_students_cannot_open_admin_area(): void
    {
        $student = User::factory()->create(['role' => UserRole::Student]);

        $this->actingAs($student)->get(route('admin.courses.index'))->assertForbidden();
        $this->post(route('admin.courses.store'), [])->assertForbidden();
    }

    public function test_rich_text_is_sanitized_on_display(): void
    {
        $course = Course::factory()->create(['description' => '<p onclick="alert(1)">An toàn</p><script>alert(1)</script>']);

        $this->get(route('courses.show', $course))
            ->assertOk()
            ->assertSee('An toàn')
            ->assertDontSee('<script>alert(1)</script>', false)
            ->assertDontSee('onclick', false);
    }

    public function test_lessons_crud(): void
    {
        $admin = User::factory()->create(['role' => UserRole::Admin]);
        $course = Course::factory()->create();

        $this->actingAs($admin)->post(route('admin.courses.lessons.store', $course), [
            'title' => 'Bài 1',
            'duration_minutes' => 15,
            'is_preview' => '1',
        ])->assertRedirect();

        $lesson = $course->lessons()->firstOrFail();
        $this->assertTrue($lesson->is_preview);

        $this->put(route('admin.courses.lessons.update', [$course, $lesson]), ['title' => 'Bài 1 (sửa)', 'duration_minutes' => 20])->assertRedirect();
        $this->assertSame('Bài 1 (sửa)', $lesson->fresh()->title);
        $this->assertFalse($lesson->fresh()->is_preview);

        $this->delete(route('admin.courses.lessons.destroy', [$course, $lesson]))->assertRedirect();
        $this->assertModelMissing($lesson);
    }
}
