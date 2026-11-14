<?php

namespace Tests\Feature\Admin;

use App\Models\Banner;
use App\Models\CloudinaryTrash;
use App\Models\Course;
use App\Models\CourseImage;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Storage;
use Illuminate\Testing\TestResponse;
use Tests\TestCase;

class CloudinaryReplaceImageTest extends TestCase
{
    use RefreshDatabase;

    private const LIST_URL = 'api.cloudinary.com/v1_1/demo/resources/image/upload*';

    private const USAGE_URL = 'api.cloudinary.com/v1_1/demo/usage';

    private User $admin;

    protected function setUp(): void
    {
        parent::setUp();

        $this->withoutVite();
        config(['cache.default' => 'array']);
        config(['filesystems.disks.public' => [
            'driver' => 'cloudinary',
            'cloudinary_url' => 'cloudinary://123456:secret@demo',
            'folder' => 'online-elearning',
            'throw' => false,
            'report' => false,
        ]]);
        Storage::forgetDisk('public');

        $this->admin = User::factory()->create(['role' => User::ROLE_ADMIN, 'status' => User::STATUS_ACTIVE]);

        Http::fake([
            self::LIST_URL => Http::response(['resources' => [
                $this->resource('banners/BannerMoiUpLen', '2026-11-12T10:00:00Z'),
                $this->resource('banners/BannerDangDung', '2026-11-11T10:00:00Z'),
                $this->resource('courses/thumbnails/AnhBiaLaravel', '2026-11-10T10:00:00Z', 'png'),
                $this->resource('courses/gallery/AnhThuVien', '2026-11-09T12:00:00Z'),
                $this->resource('avatars/AvatarAdmin', '2026-11-09T10:00:00Z'),
                $this->resource('editor/AnhTrongBaiViet', '2026-11-08T10:00:00Z'),
            ]]),
            self::USAGE_URL => Http::response([]),
        ]);
    }

    /**
     * @return array<string, mixed>
     */
    private function resource(string $publicId, string $createdAt, string $format = 'jpg'): array
    {
        return [
            'public_id' => 'online-elearning/'.$publicId,
            'format' => $format,
            'created_at' => $createdAt,
            'bytes' => 1024,
            'width' => 800,
            'height' => 600,
            'secure_url' => 'https://res.cloudinary.com/demo/image/upload/v1/online-elearning/'.$publicId.'.'.$format,
        ];
    }

    private function course(string $title, ?string $thumbnail = null): Course
    {
        $instructor = User::factory()->create(['role' => User::ROLE_INSTRUCTOR]);

        return Course::create([
            'instructor_id' => $instructor->id,
            'title' => $title,
            'slug' => str($title)->slug().'-'.fake()->unique()->randomNumber(5),
            'price' => 0,
            'level' => Course::LEVEL_BEGINNER,
            'status' => Course::STATUS_PUBLISHED,
            'thumbnail' => $thumbnail,
        ]);
    }

    /**
     * @param  array<string, mixed>  $overrides
     */
    private function replace(array $overrides = []): TestResponse
    {
        return $this->actingAs($this->admin)
            ->from(route('admin.cloudinary.index'))
            ->post(route('admin.cloudinary.replace'), $overrides);
    }

    private function destroyCalls(): int
    {
        return count(Http::recorded(fn ($request) => str_contains($request->url(), '/image/destroy')));
    }

    public function test_page_offers_replace_for_used_images(): void
    {
        $banner = Banner::create(['title' => 'Nâng tầm kỹ năng lập trình', 'image_path' => 'banners/BannerDangDung.jpg', 'is_active' => true]);
        CloudinaryTrash::create([
            'path' => 'editor/AnhTrongBaiViet.jpg',
            'name' => 'AnhTrongBaiViet.jpg',
            'folder' => 'editor',
            'url' => 'https://res.cloudinary.com/demo/image/upload/v1/online-elearning/editor/AnhTrongBaiViet.jpg',
            'thumb_url' => 'https://res.cloudinary.com/demo/image/upload/v1/online-elearning/editor/AnhTrongBaiViet.jpg',
            'places' => [],
            'purge_at' => now()->addDays(7),
        ]);

        $response = $this->actingAs($this->admin)->get(route('admin.cloudinary.index'))
            ->assertOk()
            ->assertSee('Thay thế')
            ->assertSee('Đã có ảnh (sẽ thay bằng ảnh này)', false);

        $this->assertArrayHasKey('banner', $response->viewData('replaceFolders'));

        // Mỗi mục có kèm ảnh hiện tại để admin xem trước khi thay.
        $item = collect($response->viewData('targets')['banner']['items'])->firstWhere('id', $banner->id);
        $this->assertSame('banners/BannerDangDung.jpg', $item['path']);
        $this->assertSame('ok', $item['status']);
        $this->assertStringContainsString('/c_fill,w_400,h_300/', $item['thumb_url']);

        // Danh sách chọn ảnh thay thế chỉ tải khi cần, và không có ảnh trong thùng rác.
        $pickerPaths = $this->actingAs($this->admin)
            ->getJson(route('admin.cloudinary.picker'))
            ->assertOk()
            ->assertJsonStructure(['images' => [['path', 'folder', 'thumb_url']]])
            ->collect('images')
            ->pluck('path');

        $this->assertCount(5, $pickerPaths);
        $this->assertNotContains('editor/AnhTrongBaiViet.jpg', $pickerPaths);
    }

    public function test_admin_can_replace_the_image_a_banner_uses(): void
    {
        $banner = Banner::create(['title' => 'Nâng tầm kỹ năng lập trình', 'image_path' => 'banners/BannerDangDung.jpg', 'is_active' => true]);

        $this->replace([
            'path' => 'banners/BannerDangDung.jpg',
            'type' => 'banner',
            'id' => $banner->id,
            'new_path' => 'banners/BannerMoiUpLen.jpg',
        ])
            ->assertRedirect(route('admin.cloudinary.index'))
            ->assertSessionHas('success', 'Đã thay ảnh cho Banner: Nâng tầm kỹ năng lập trình. Ảnh cũ vẫn còn trên Cloudinary.');

        $this->assertSame('banners/BannerMoiUpLen.jpg', $banner->fresh()->image_path);
        $this->assertSame(0, CloudinaryTrash::count());
        $this->assertSame(0, $this->destroyCalls());
    }

    public function test_replace_can_move_the_old_image_to_trash_when_nothing_else_uses_it(): void
    {
        $banner = Banner::create(['title' => 'Banner chính', 'image_path' => 'banners/BannerDangDung.jpg', 'is_active' => true]);

        $this->replace([
            'path' => 'banners/BannerDangDung.jpg',
            'type' => 'banner',
            'id' => $banner->id,
            'new_path' => 'banners/BannerMoiUpLen.jpg',
            'trash_old' => '1',
        ])->assertSessionHas('success', 'Đã thay ảnh cho Banner: Banner chính. Ảnh cũ không còn dùng ở đâu nên đã vào thùng rác, khôi phục được trong 7 ngày.');

        $this->assertSame('banners/BannerMoiUpLen.jpg', $banner->fresh()->image_path);
        $this->assertDatabaseHas('cloudinary_trash', ['path' => 'banners/BannerDangDung.jpg', 'deleted_by' => $this->admin->id]);
        // Chỉ vào thùng rác, chưa xóa thật trên Cloudinary.
        $this->assertSame(0, $this->destroyCalls());
    }

    public function test_replace_keeps_the_old_image_when_it_is_still_used_elsewhere(): void
    {
        $first = Banner::create(['title' => 'Banner 1', 'image_path' => 'banners/BannerDangDung.jpg', 'is_active' => true]);
        $second = Banner::create(['title' => 'Banner 2', 'image_path' => 'banners/BannerDangDung.jpg', 'is_active' => true]);

        $this->replace([
            'path' => 'banners/BannerDangDung.jpg',
            'type' => 'banner',
            'id' => $first->id,
            'new_path' => 'banners/BannerMoiUpLen.jpg',
            'trash_old' => '1',
        ])->assertSessionHas('success', 'Đã thay ảnh cho Banner: Banner 1. Ảnh cũ vẫn đang dùng ở chỗ khác nên được giữ lại.');

        $this->assertSame('banners/BannerMoiUpLen.jpg', $first->fresh()->image_path);
        $this->assertSame('banners/BannerDangDung.jpg', $second->fresh()->image_path);
        $this->assertSame(0, CloudinaryTrash::count());
    }

    public function test_replace_never_trashes_old_images_from_the_post_editor(): void
    {
        $course = $this->course('Laravel thực chiến', 'editor/AnhTrongBaiViet.jpg');

        $this->replace([
            'path' => 'editor/AnhTrongBaiViet.jpg',
            'type' => 'course',
            'id' => $course->id,
            'new_path' => 'courses/thumbnails/AnhBiaLaravel.png',
            'trash_old' => '1',
        ])->assertSessionHas('success', 'Đã thay ảnh cho Ảnh bìa: Laravel thực chiến. Ảnh cũ được giữ lại vì có thể đang nằm trong nội dung bài viết.');

        $this->assertSame('courses/thumbnails/AnhBiaLaravel.png', $course->fresh()->thumbnail);
        $this->assertSame(0, CloudinaryTrash::count());
    }

    public function test_admin_can_replace_a_course_gallery_image_and_an_avatar(): void
    {
        $course = $this->course('Laravel thực chiến');
        $galleryImage = CourseImage::create(['course_id' => $course->id, 'image_path' => 'courses/gallery/AnhThuVien.jpg']);
        $student = User::factory()->create(['role' => User::ROLE_STUDENT, 'avatar' => 'avatars/AvatarAdmin.jpg']);

        $this->replace([
            'path' => 'courses/gallery/AnhThuVien.jpg',
            'type' => 'course_image',
            'id' => $galleryImage->id,
            'new_path' => 'courses/thumbnails/AnhBiaLaravel.png',
        ])->assertSessionHas('success', 'Đã thay ảnh cho Thư viện ảnh: Laravel thực chiến. Ảnh cũ vẫn còn trên Cloudinary.');

        $this->replace([
            'path' => 'avatars/AvatarAdmin.jpg',
            'type' => 'user',
            'id' => $student->id,
            'new_path' => 'banners/BannerMoiUpLen.jpg',
        ])->assertSessionHas('success');

        $this->assertSame('courses/thumbnails/AnhBiaLaravel.png', $galleryImage->fresh()->image_path);
        $this->assertSame('banners/BannerMoiUpLen.jpg', $student->fresh()->avatar);
    }

    public function test_replace_refuses_when_the_place_no_longer_uses_the_old_image(): void
    {
        $banner = Banner::create(['title' => 'Banner', 'image_path' => 'banners/BannerMoiUpLen.jpg', 'is_active' => true]);

        $this->replace([
            'path' => 'banners/BannerDangDung.jpg',
            'type' => 'banner',
            'id' => $banner->id,
            'new_path' => 'courses/thumbnails/AnhBiaLaravel.png',
        ])->assertSessionHas('error', 'Chỗ này không còn dùng ảnh đó nữa. Bấm "Làm mới" để xem lại.');

        $this->assertSame('banners/BannerMoiUpLen.jpg', $banner->fresh()->image_path);
    }

    public function test_replace_refuses_new_images_that_are_missing_or_in_the_trash(): void
    {
        $banner = Banner::create(['title' => 'Banner', 'image_path' => 'banners/BannerDangDung.jpg', 'is_active' => true]);
        CloudinaryTrash::create([
            'path' => 'banners/BannerMoiUpLen.jpg',
            'name' => 'BannerMoiUpLen.jpg',
            'folder' => 'banners',
            'url' => 'https://res.cloudinary.com/demo/image/upload/v1/online-elearning/banners/BannerMoiUpLen.jpg',
            'thumb_url' => 'https://res.cloudinary.com/demo/image/upload/v1/online-elearning/banners/BannerMoiUpLen.jpg',
            'places' => [],
            'purge_at' => now()->addDays(7),
        ]);

        foreach (['banners/khong-ton-tai.jpg', 'banners/BannerMoiUpLen.jpg'] as $newPath) {
            $this->replace([
                'path' => 'banners/BannerDangDung.jpg',
                'type' => 'banner',
                'id' => $banner->id,
                'new_path' => $newPath,
            ])->assertSessionHas('error');
        }

        $this->replace([
            'path' => 'banners/BannerDangDung.jpg',
            'type' => 'banner',
            'id' => $banner->id,
            'new_path' => 'banners/BannerDangDung.jpeg',
        ])->assertSessionHas('error', 'Ảnh mới trùng với ảnh đang dùng, hãy chọn ảnh khác.');

        $this->assertSame('banners/BannerDangDung.jpg', $banner->fresh()->image_path);
    }

    public function test_replace_rejects_places_that_are_not_plain_images(): void
    {
        $this->replace([
            'path' => 'lessons/attachments/tai-lieu.pdf',
            'type' => 'lesson',
            'id' => 1,
            'new_path' => 'banners/BannerMoiUpLen.jpg',
        ])->assertSessionHasErrors('type');
    }

    public function test_assigning_to_an_item_that_already_has_an_image_replaces_it(): void
    {
        $course = $this->course('Laravel thực chiến', 'courses/thumbnails/AnhBiaLaravel.png');

        $this->actingAs($this->admin)->post(route('admin.cloudinary.assign'), [
            'path' => 'banners/BannerMoiUpLen.jpg',
            'type' => 'course',
            'target_id' => $course->id,
        ])->assertSessionHas('success', 'Đã thay ảnh cho ảnh bìa khóa học “Laravel thực chiến”. Ảnh cũ vẫn còn trên Cloudinary.');

        $this->assertSame('banners/BannerMoiUpLen.jpg', $course->fresh()->thumbnail);
        $this->assertSame(0, CloudinaryTrash::count());

        // Thay lần nữa và chọn đưa ảnh cũ (giờ không còn dùng ở đâu) vào thùng rác.
        $this->actingAs($this->admin)->post(route('admin.cloudinary.assign'), [
            'path' => 'courses/thumbnails/AnhBiaLaravel.png',
            'type' => 'course',
            'target_id' => $course->id,
            'trash_old' => '1',
        ])->assertSessionHas('success', 'Đã thay ảnh cho ảnh bìa khóa học “Laravel thực chiến”. Ảnh cũ không còn dùng ở đâu nên đã vào thùng rác, khôi phục được trong 7 ngày.');

        $this->assertSame('courses/thumbnails/AnhBiaLaravel.png', $course->fresh()->thumbnail);
        $this->assertDatabaseHas('cloudinary_trash', ['path' => 'banners/BannerMoiUpLen.jpg']);
    }

    public function test_assign_refuses_images_in_the_trash(): void
    {
        $banner = Banner::create(['title' => 'Banner', 'image_path' => null, 'is_active' => true]);
        CloudinaryTrash::create([
            'path' => 'banners/BannerMoiUpLen.jpg',
            'name' => 'BannerMoiUpLen.jpg',
            'folder' => 'banners',
            'url' => 'https://res.cloudinary.com/demo/image/upload/v1/online-elearning/banners/BannerMoiUpLen.jpg',
            'thumb_url' => 'https://res.cloudinary.com/demo/image/upload/v1/online-elearning/banners/BannerMoiUpLen.jpg',
            'places' => [],
            'purge_at' => now()->addDays(7),
        ]);

        $this->actingAs($this->admin)->post(route('admin.cloudinary.assign'), [
            'path' => 'banners/BannerMoiUpLen.jpg',
            'type' => 'banner',
            'target_id' => $banner->id,
        ])->assertSessionHas('error');

        $this->assertNull($banner->fresh()->image_path);
    }

    public function test_only_admin_can_replace_images(): void
    {
        $instructor = User::factory()->create(['role' => User::ROLE_INSTRUCTOR]);
        $banner = Banner::create(['title' => 'Banner', 'image_path' => 'banners/BannerDangDung.jpg', 'is_active' => true]);

        $this->actingAs($instructor)->post(route('admin.cloudinary.replace'), [
            'path' => 'banners/BannerDangDung.jpg',
            'type' => 'banner',
            'id' => $banner->id,
            'new_path' => 'banners/BannerMoiUpLen.jpg',
        ])->assertForbidden();

        $this->actingAs($instructor)->getJson(route('admin.cloudinary.picker'))->assertForbidden();

        $this->assertSame('banners/BannerDangDung.jpg', $banner->fresh()->image_path);
    }
}
