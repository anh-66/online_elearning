<?php

namespace Tests\Feature\Admin;

use App\Models\Banner;
use App\Models\CloudinaryTrash;
use App\Models\Course;
use App\Models\CourseImage;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\Client\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class CloudinaryTrashTest extends TestCase
{
    use RefreshDatabase;

    private const LIST_URL = 'api.cloudinary.com/v1_1/demo/resources/image/upload*';

    private const DESTROY_URL = 'api.cloudinary.com/v1_1/demo/image/destroy';

    private User $admin;

    private Banner $banner;

    private Course $course;

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
        $this->banner = Banner::create(['title' => 'Banner chính', 'image_path' => 'banners/AnhBanner.jpg', 'is_active' => true]);

        $instructor = User::factory()->create(['role' => User::ROLE_INSTRUCTOR]);
        $this->course = Course::create([
            'instructor_id' => $instructor->id,
            'title' => 'Laravel thực chiến',
            'slug' => 'laravel-thuc-chien',
            'price' => 0,
            'level' => Course::LEVEL_BEGINNER,
            'status' => Course::STATUS_PUBLISHED,
            'thumbnail' => 'courses/thumbnails/AnhBia.jpg',
        ]);
    }

    /**
     * @return array<string, mixed>
     */
    private function resource(string $publicId): array
    {
        return [
            'public_id' => 'online-elearning/'.$publicId,
            'format' => 'jpg',
            'created_at' => '2026-11-12T10:00:00Z',
            'bytes' => 1024,
            'width' => 800,
            'height' => 450,
            'secure_url' => 'https://res.cloudinary.com/demo/image/upload/v1/online-elearning/'.$publicId.'.jpg',
        ];
    }

    private function fake(int $destroyStatus = 200): void
    {
        Http::fake([
            self::LIST_URL => Http::response(['resources' => [
                $this->resource('banners/AnhBanner'),
                $this->resource('courses/thumbnails/AnhBia'),
                $this->resource('courses/gallery/AnhGallery'),
            ]]),
            'api.cloudinary.com/v1_1/demo/usage' => Http::response([]),
            self::DESTROY_URL => $destroyStatus === 200
                ? Http::response(['result' => 'ok'])
                : Http::response(['error' => ['message' => 'Cloudinary lỗi']], $destroyStatus),
        ]);
    }

    private function trashItem(string $path, int $daysAgo = 0, array $places = []): CloudinaryTrash
    {
        return CloudinaryTrash::create([
            'path' => $path,
            'name' => basename($path),
            'folder' => dirname($path),
            'format' => 'JPG',
            'url' => 'https://res.cloudinary.com/demo/image/upload/v1/online-elearning/'.$path,
            'thumb_url' => 'https://res.cloudinary.com/demo/image/upload/v1/online-elearning/'.$path,
            'places' => $places,
            'purge_at' => now()->addDays(7 - $daysAgo),
        ]);
    }

    private function destroyCalls(): int
    {
        return count(Http::recorded(fn (Request $request) => str_contains($request->url(), '/image/destroy')));
    }

    public function test_unlink_empties_the_place_but_keeps_the_file_on_cloudinary(): void
    {
        $this->fake();

        $this->actingAs($this->admin)
            ->post(route('admin.cloudinary.unlink'), ['path' => 'banners/AnhBanner.jpg', 'type' => 'banner', 'id' => $this->banner->id])
            ->assertSessionHas('success', 'Đã tháo ảnh khỏi Banner: Banner chính. Ảnh vẫn còn trên Cloudinary.');

        $this->assertNull($this->banner->fresh()->image_path);
        $this->assertSame(0, $this->destroyCalls());
    }

    public function test_unlink_refuses_a_place_that_uses_another_image(): void
    {
        $this->fake();

        $this->actingAs($this->admin)
            ->post(route('admin.cloudinary.unlink'), ['path' => 'banners/AnhKhac.jpg', 'type' => 'banner', 'id' => $this->banner->id])
            ->assertSessionHas('error');

        $this->assertSame('banners/AnhBanner.jpg', $this->banner->fresh()->image_path);
    }

    public function test_delete_moves_image_to_trash_and_unlinks_it_everywhere(): void
    {
        $this->fake();
        $this->course->update(['thumbnail' => 'banners/AnhBanner.jpg']);

        $this->actingAs($this->admin)
            ->post(route('admin.cloudinary.trash.store'), ['path' => 'banners/AnhBanner.jpg'])
            ->assertSessionHas('success', 'Đã chuyển ảnh vào thùng rác và tháo khỏi 2 chỗ đang dùng. Ảnh sẽ bị xóa vĩnh viễn sau 7 ngày.');

        $this->assertNull($this->banner->fresh()->image_path);
        $this->assertNull($this->course->fresh()->thumbnail);
        $this->assertSame(0, $this->destroyCalls());

        $item = CloudinaryTrash::firstOrFail();
        $this->assertSame('banners/AnhBanner.jpg', $item->path);
        $this->assertCount(2, $item->places);
        $this->assertTrue($item->purge_at->between(now()->addDays(7)->subMinute(), now()->addDays(7)->addMinute()));

        $response = $this->actingAs($this->admin)->get(route('admin.cloudinary.index'))->assertOk();
        $this->assertNotContains('banners/AnhBanner.jpg', collect($response->viewData('images')->items())->pluck('path'));
        $response->assertViewHas('trashCount', 1);
    }

    public function test_restore_relinks_only_places_that_are_still_empty(): void
    {
        $this->fake();

        $this->actingAs($this->admin)->post(route('admin.cloudinary.trash.store'), ['path' => 'banners/AnhBanner.jpg']);
        $this->actingAs($this->admin)->post(route('admin.cloudinary.trash.store'), ['path' => 'courses/thumbnails/AnhBia.jpg']);
        $this->course->update(['thumbnail' => 'courses/thumbnails/AnhMoi.jpg']);

        $banner = CloudinaryTrash::where('path', 'banners/AnhBanner.jpg')->firstOrFail();
        $cover = CloudinaryTrash::where('path', 'courses/thumbnails/AnhBia.jpg')->firstOrFail();

        $this->actingAs($this->admin)->post(route('admin.cloudinary.trash.restore', $banner))
            ->assertSessionHas('success', 'Đã khôi phục ảnh AnhBanner.jpg. Đã gắn lại vào 1 chỗ cũ.');
        $this->actingAs($this->admin)->post(route('admin.cloudinary.trash.restore', $cover))
            ->assertSessionHas('success', 'Đã khôi phục ảnh AnhBia.jpg. 1 chỗ đã có ảnh khác hoặc không còn nên không gắn lại.');

        $this->assertSame('banners/AnhBanner.jpg', $this->banner->fresh()->image_path);
        $this->assertSame('courses/thumbnails/AnhMoi.jpg', $this->course->fresh()->thumbnail);
        $this->assertSame(0, CloudinaryTrash::count());
    }

    public function test_gallery_image_row_is_removed_and_recreated_on_restore(): void
    {
        $this->fake();
        CourseImage::create(['course_id' => $this->course->id, 'image_path' => 'courses/gallery/AnhGallery.jpg']);

        $this->actingAs($this->admin)->post(route('admin.cloudinary.trash.store'), ['path' => 'courses/gallery/AnhGallery.jpg']);
        $this->assertSame(0, CourseImage::count());

        $this->actingAs($this->admin)->post(route('admin.cloudinary.trash.restore', CloudinaryTrash::firstOrFail()));
        $this->assertDatabaseHas('course_images', ['course_id' => $this->course->id, 'image_path' => 'courses/gallery/AnhGallery.jpg']);
    }

    public function test_delete_forever_removes_the_image_on_cloudinary(): void
    {
        $this->fake();
        $item = $this->trashItem('banners/AnhBanner.jpg');

        $this->actingAs($this->admin)
            ->delete(route('admin.cloudinary.trash.destroy', $item))
            ->assertSessionHas('success', 'Đã xóa vĩnh viễn ảnh AnhBanner.jpg trên Cloudinary.');

        $this->assertModelMissing($item);
        Http::assertSent(fn (Request $request) => str_contains($request->url(), '/image/destroy')
            && $request['public_id'] === 'online-elearning/banners/AnhBanner'
            && filled($request['signature']));
    }

    public function test_failed_cloudinary_delete_keeps_the_image_in_trash(): void
    {
        $this->fake(500);
        $item = $this->trashItem('banners/AnhBanner.jpg');

        $this->actingAs($this->admin)
            ->delete(route('admin.cloudinary.trash.destroy', $item))
            ->assertSessionHas('error');

        $this->assertModelExists($item);
    }

    public function test_images_older_than_seven_days_are_purged_when_admin_opens_the_trash(): void
    {
        $this->fake();
        $expired = $this->trashItem('banners/AnhBanner.jpg', daysAgo: 8);
        $fresh = $this->trashItem('courses/thumbnails/AnhBia.jpg', daysAgo: 2);

        $this->actingAs($this->admin)
            ->get(route('admin.cloudinary.trash.index'))
            ->assertOk()
            ->assertSee('Vừa tự xóa vĩnh viễn 1 ảnh')
            ->assertSee('Còn 5 ngày')
            ->assertDontSee('AnhBanner.jpg');

        $this->assertModelMissing($expired);
        $this->assertModelExists($fresh);
        $this->assertSame(1, $this->destroyCalls());
    }

    public function test_purge_command_removes_expired_images_on_server_start(): void
    {
        $this->fake();
        $expired = $this->trashItem('banners/AnhBanner.jpg', daysAgo: 7);
        $fresh = $this->trashItem('courses/thumbnails/AnhBia.jpg', daysAgo: 1);

        $this->artisan('cloudinary:purge-trash')
            ->expectsOutputToContain('Đã xóa vĩnh viễn 1 ảnh')
            ->assertSuccessful();

        $this->assertModelMissing($expired);
        $this->assertModelExists($fresh);
    }

    public function test_empty_trash_deletes_everything_forever(): void
    {
        $this->fake();
        $this->trashItem('banners/AnhBanner.jpg');
        $this->trashItem('courses/thumbnails/AnhBia.jpg');

        $this->actingAs($this->admin)
            ->delete(route('admin.cloudinary.trash.empty'))
            ->assertSessionHas('success', 'Đã xóa vĩnh viễn 2 ảnh trên Cloudinary.');

        $this->assertSame(0, CloudinaryTrash::count());
        $this->assertSame(2, $this->destroyCalls());
    }

    public function test_only_admin_can_manage_the_trash(): void
    {
        $this->fake();
        $instructor = User::factory()->create(['role' => User::ROLE_INSTRUCTOR]);
        $item = $this->trashItem('banners/AnhBanner.jpg');

        $this->actingAs($instructor)->get(route('admin.cloudinary.trash.index'))->assertForbidden();
        $this->actingAs($instructor)->post(route('admin.cloudinary.trash.store'), ['path' => 'banners/AnhBanner.jpg'])->assertForbidden();
        $this->actingAs($instructor)->delete(route('admin.cloudinary.trash.destroy', $item))->assertForbidden();
        $this->actingAs($instructor)->post(route('admin.cloudinary.unlink'), ['path' => 'banners/AnhBanner.jpg', 'type' => 'banner', 'id' => $this->banner->id])->assertForbidden();

        $this->assertModelExists($item);
        $this->assertSame('banners/AnhBanner.jpg', $this->banner->fresh()->image_path);
    }
}
