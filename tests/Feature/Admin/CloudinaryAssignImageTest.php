<?php

namespace Tests\Feature\Admin;

use App\Models\Banner;
use App\Models\Course;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class CloudinaryAssignImageTest extends TestCase
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

    public function test_page_marks_images_that_are_not_used_anywhere(): void
    {
        Banner::create(['title' => 'Banner đang chạy', 'image_path' => 'banners/BannerDangDung.jpg', 'is_active' => true]);
        $broken = Banner::create(['title' => 'Nâng tầm kỹ năng lập trình', 'image_path' => 'banners/banner-1.jpg', 'is_active' => true]);
        $course = $this->course('Laravel thực chiến');

        $response = $this->actingAs($this->admin)->get(route('admin.cloudinary.index'));

        $response->assertOk()
            ->assertSee('Banner: Banner đang chạy')
            ->assertSee('Chưa dùng')
            ->assertSee('Gắn ảnh này vào', false)
            ->assertViewHas('unusedCount', 3);

        $targets = $response->viewData('targets');
        $bannerStatuses = collect($targets['banner']['items'])->pluck('status', 'id');
        $this->assertSame('broken', $bannerStatuses[$broken->id]);
        $this->assertSame('empty', collect($targets['course']['items'])->firstWhere('id', $course->id)['status']);
    }

    public function test_unused_filter_lists_only_orphan_images(): void
    {
        Banner::create(['title' => 'Banner đang chạy', 'image_path' => 'banners/BannerDangDung.jpg', 'is_active' => true]);

        $images = $this->actingAs($this->admin)
            ->get(route('admin.cloudinary.index', ['status' => 'unused']))
            ->assertOk()
            ->viewData('images');

        $paths = collect($images->items())->pluck('path')->all();

        $this->assertEqualsCanonicalizing(
            ['banners/BannerMoiUpLen.jpg', 'courses/thumbnails/AnhBiaLaravel.png', 'avatars/AvatarAdmin.jpg'],
            $paths,
        );
    }

    public function test_admin_can_relink_an_existing_image_to_a_banner(): void
    {
        $banner = Banner::create(['title' => 'Nâng tầm kỹ năng lập trình', 'image_path' => 'banners/banner-1.jpg', 'is_active' => true]);

        $this->actingAs($this->admin)
            ->from(route('admin.cloudinary.index'))
            ->post(route('admin.cloudinary.assign'), [
                'path' => 'banners/BannerMoiUpLen.jpg',
                'type' => 'banner',
                'target_id' => $banner->id,
            ])
            ->assertRedirect(route('admin.cloudinary.index'))
            ->assertSessionHas('success', 'Đã gắn ảnh cho banner “Nâng tầm kỹ năng lập trình”.');

        $this->assertSame('banners/BannerMoiUpLen.jpg', $banner->fresh()->image_path);
        Http::assertNotSent(fn ($request) => str_contains($request->url(), '/destroy') || str_contains($request->url(), '/upload') && $request->method() === 'POST');
    }

    public function test_admin_can_relink_course_thumbnail_and_user_avatar(): void
    {
        $course = $this->course('Laravel thực chiến');
        $student = User::factory()->create(['role' => User::ROLE_STUDENT, 'avatar' => null]);

        $this->actingAs($this->admin)->post(route('admin.cloudinary.assign'), [
            'path' => 'courses/thumbnails/AnhBiaLaravel.png',
            'type' => 'course',
            'target_id' => $course->id,
        ])->assertSessionHas('success');

        $this->actingAs($this->admin)->post(route('admin.cloudinary.assign'), [
            'path' => 'avatars/AvatarAdmin.jpg',
            'type' => 'user',
            'target_id' => $student->id,
        ])->assertSessionHas('success');

        $this->assertSame('courses/thumbnails/AnhBiaLaravel.png', $course->fresh()->thumbnail);
        $this->assertSame('avatars/AvatarAdmin.jpg', $student->fresh()->avatar);
        $this->assertSame(
            'https://res.cloudinary.com/demo/image/upload/online-elearning/courses/thumbnails/AnhBiaLaravel.png',
            Storage::disk('public')->url($course->fresh()->thumbnail),
        );
    }

    public function test_it_refuses_images_that_are_not_on_cloudinary(): void
    {
        $banner = Banner::create(['title' => 'Banner', 'image_path' => null, 'is_active' => true]);

        $this->actingAs($this->admin)->post(route('admin.cloudinary.assign'), [
            'path' => 'banners/khong-ton-tai.jpg',
            'type' => 'banner',
            'target_id' => $banner->id,
        ])->assertSessionHas('error');

        $this->assertNull($banner->fresh()->image_path);
    }

    public function test_it_reports_missing_target_instead_of_crashing(): void
    {
        $this->actingAs($this->admin)->post(route('admin.cloudinary.assign'), [
            'path' => 'banners/BannerMoiUpLen.jpg',
            'type' => 'banner',
            'target_id' => 999,
        ])->assertSessionHas('error');
    }

    public function test_only_admin_can_relink_images(): void
    {
        $instructor = User::factory()->create(['role' => User::ROLE_INSTRUCTOR]);
        $course = $this->course('Laravel thực chiến');

        $this->actingAs($instructor)->post(route('admin.cloudinary.assign'), [
            'path' => 'courses/thumbnails/AnhBiaLaravel.png',
            'type' => 'course',
            'target_id' => $course->id,
        ])->assertForbidden();

        $this->assertNull($course->fresh()->thumbnail);
    }
}
