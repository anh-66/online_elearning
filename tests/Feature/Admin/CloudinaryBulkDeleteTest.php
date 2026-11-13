<?php

namespace Tests\Feature\Admin;

use App\Models\Banner;
use App\Models\CloudinaryTrash;
use App\Models\Course;
use App\Models\User;
use App\Services\CloudinaryTrashBin;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\Client\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class CloudinaryBulkDeleteTest extends TestCase
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
                $this->resource('avatars/AnhLe'),
            ]]),
            'api.cloudinary.com/v1_1/demo/usage' => Http::response([]),
            self::DESTROY_URL => $destroyStatus === 200
                ? Http::response(['result' => 'ok'])
                : Http::response(['error' => ['message' => 'Cloudinary lỗi']], $destroyStatus),
        ]);
    }

    private function trashItem(string $path, array $places = []): CloudinaryTrash
    {
        return CloudinaryTrash::create([
            'path' => $path,
            'name' => basename($path),
            'folder' => dirname($path),
            'format' => 'JPG',
            'url' => 'https://res.cloudinary.com/demo/image/upload/v1/online-elearning/'.$path,
            'thumb_url' => 'https://res.cloudinary.com/demo/image/upload/v1/online-elearning/'.$path,
            'places' => $places,
            'purge_at' => now()->addDays(5),
        ]);
    }

    private function destroyCalls(): int
    {
        return count(Http::recorded(fn (Request $request) => str_contains($request->url(), '/image/destroy')));
    }

    public function test_library_page_shows_checkboxes_and_sends_keys_for_select_all(): void
    {
        $this->fake();

        $response = $this->actingAs($this->admin)
            ->get(route('admin.cloudinary.index'))
            ->assertOk()
            ->assertSee('Tích vào ô ở góc mỗi ảnh để chọn nhiều ảnh rồi xóa cùng lúc.')
            ->assertSee('Chọn tất cả')
            ->assertSee('aria-label="Chọn ảnh AnhLe.jpg"', false);

        $selection = $response->viewData('selection');
        $this->assertCount(4, $selection['pageKeys']);
        $this->assertEqualsCanonicalizing($selection['pageKeys'], $selection['allKeys']);
        $this->assertEqualsCanonicalizing(['banners/AnhBanner.jpg', 'courses/thumbnails/AnhBia.jpg'], $selection['usedKeys']);
    }

    public function test_select_all_covers_images_on_other_pages_too(): void
    {
        $resources = collect(range(1, 30))->map(fn (int $i) => $this->resource('avatars/Anh'.str_pad((string) $i, 2, '0', STR_PAD_LEFT)))->all();
        Http::fake([
            self::LIST_URL => Http::response(['resources' => $resources]),
            'api.cloudinary.com/v1_1/demo/usage' => Http::response([]),
        ]);

        $response = $this->actingAs($this->admin)
            ->get(route('admin.cloudinary.index', ['folder' => 'avatars']))
            ->assertOk()
            ->assertSee('Chọn luôn cả 30 ảnh ở mọi trang');

        $selection = $response->viewData('selection');
        $this->assertCount(24, $selection['pageKeys']);
        $this->assertCount(30, $selection['allKeys']);
    }

    public function test_bulk_delete_moves_selected_images_to_trash_and_unlinks_them(): void
    {
        $this->fake();

        $this->actingAs($this->admin)
            ->post(route('admin.cloudinary.trash.bulk-store'), [
                'paths' => json_encode(['banners/AnhBanner.jpg', 'courses/thumbnails/AnhBia.jpg', 'avatars/AnhLe.jpg']),
            ])
            ->assertSessionHas('success', 'Đã chuyển 3 ảnh vào thùng rác và tháo khỏi 2 chỗ đang dùng. Ảnh sẽ bị xóa vĩnh viễn sau 7 ngày.');

        $this->assertNull($this->banner->fresh()->image_path);
        $this->assertNull($this->course->fresh()->thumbnail);
        $this->assertEqualsCanonicalizing(
            ['banners/AnhBanner.jpg', 'courses/thumbnails/AnhBia.jpg', 'avatars/AnhLe.jpg'],
            CloudinaryTrash::query()->pluck('path')->all(),
        );
        $this->assertSame($this->admin->id, CloudinaryTrash::query()->where('path', 'avatars/AnhLe.jpg')->value('deleted_by'));
        $this->assertSame(0, $this->destroyCalls());

        $response = $this->actingAs($this->admin)->get(route('admin.cloudinary.index'))->assertOk();
        $this->assertSame(['courses/gallery/AnhGallery.jpg'], collect($response->viewData('images')->items())->pluck('path')->all());
    }

    public function test_bulk_delete_skips_images_that_are_gone_or_already_in_trash(): void
    {
        $this->fake();
        $this->trashItem('avatars/AnhLe.jpg');

        $this->actingAs($this->admin)
            ->post(route('admin.cloudinary.trash.bulk-store'), [
                'paths' => json_encode(['courses/gallery/AnhGallery.jpg', 'avatars/AnhLe.jpg', 'banners/KhongCo.jpg']),
            ])
            ->assertSessionHas('success', 'Đã chuyển 1 ảnh vào thùng rác. Ảnh sẽ bị xóa vĩnh viễn sau 7 ngày. 2 ảnh không còn trên Cloudinary hoặc đã ở trong thùng rác nên bỏ qua.');

        $this->assertSame(2, CloudinaryTrash::count());
    }

    public function test_bulk_delete_without_a_selection_shows_an_error(): void
    {
        $this->fake();

        $this->actingAs($this->admin)
            ->post(route('admin.cloudinary.trash.bulk-store'), ['paths' => '[]'])
            ->assertSessionHas('error', 'Bạn chưa chọn ảnh nào.');

        $this->actingAs($this->admin)
            ->post(route('admin.cloudinary.trash.bulk-store'), ['paths' => 'khong-phai-json'])
            ->assertSessionHas('error', 'Bạn chưa chọn ảnh nào.');

        $this->assertSame(0, CloudinaryTrash::count());
        $this->assertSame('banners/AnhBanner.jpg', $this->banner->fresh()->image_path);
    }

    public function test_trash_page_shows_checkboxes(): void
    {
        $this->fake();
        $first = $this->trashItem('avatars/AnhLe.jpg');
        $second = $this->trashItem('courses/gallery/AnhGallery.jpg');

        $response = $this->actingAs($this->admin)
            ->get(route('admin.cloudinary.trash.index'))
            ->assertOk()
            ->assertSee('Tích vào ô ở góc mỗi ảnh để khôi phục hoặc xóa vĩnh viễn nhiều ảnh cùng lúc.')
            ->assertSee('aria-label="Chọn ảnh AnhLe.jpg"', false);

        $this->assertEqualsCanonicalizing([(string) $first->id, (string) $second->id], $response->viewData('selection')['allKeys']);
    }

    public function test_bulk_restore_brings_back_only_the_selected_images(): void
    {
        $this->fake();
        $this->actingAs($this->admin)->post(route('admin.cloudinary.trash.bulk-store'), [
            'paths' => json_encode(['banners/AnhBanner.jpg', 'courses/thumbnails/AnhBia.jpg', 'avatars/AnhLe.jpg']),
        ]);
        $ids = CloudinaryTrash::query()->whereIn('path', ['banners/AnhBanner.jpg', 'courses/thumbnails/AnhBia.jpg'])->pluck('id')->all();

        $this->actingAs($this->admin)
            ->post(route('admin.cloudinary.trash.bulk-restore'), ['ids' => json_encode(array_map('strval', $ids))])
            ->assertSessionHas('success', 'Đã khôi phục 2 ảnh. Đã gắn lại vào 2 chỗ cũ.');

        $this->assertSame('banners/AnhBanner.jpg', $this->banner->fresh()->image_path);
        $this->assertSame('courses/thumbnails/AnhBia.jpg', $this->course->fresh()->thumbnail);
        $this->assertSame(['avatars/AnhLe.jpg'], CloudinaryTrash::query()->pluck('path')->all());
    }

    public function test_bulk_delete_forever_removes_only_the_selected_images_on_cloudinary(): void
    {
        $this->fake();
        $first = $this->trashItem('banners/AnhBanner.jpg');
        $second = $this->trashItem('avatars/AnhLe.jpg');
        $kept = $this->trashItem('courses/gallery/AnhGallery.jpg');

        $this->actingAs($this->admin)
            ->delete(route('admin.cloudinary.trash.bulk-destroy'), ['ids' => json_encode([(string) $first->id, (string) $second->id])])
            ->assertSessionHas('success', 'Đã xóa vĩnh viễn 2 ảnh trên Cloudinary.');

        $this->assertModelMissing($first);
        $this->assertModelMissing($second);
        $this->assertModelExists($kept);
        $this->assertSame(2, $this->destroyCalls());
        Http::assertSent(fn (Request $request) => str_contains($request->url(), '/image/destroy')
            && $request['public_id'] === 'online-elearning/avatars/AnhLe');
        Http::assertNotSent(fn (Request $request) => str_contains($request->url(), '/image/destroy')
            && $request['public_id'] === 'online-elearning/courses/gallery/AnhGallery');
    }

    public function test_failed_bulk_delete_forever_keeps_images_in_trash(): void
    {
        $this->fake(500);
        $item = $this->trashItem('banners/AnhBanner.jpg');

        $this->actingAs($this->admin)
            ->delete(route('admin.cloudinary.trash.bulk-destroy'), ['ids' => json_encode([(string) $item->id])])
            ->assertSessionHas('error', 'Đã xóa vĩnh viễn 0 ảnh trên Cloudinary. 1 ảnh chưa xóa được, thử lại sau.');

        $this->assertModelExists($item);
    }

    public function test_purging_stops_when_time_runs_out_and_keeps_the_rest_for_next_time(): void
    {
        $this->fake();
        $items = collect([
            $this->trashItem('banners/AnhBanner.jpg'),
            $this->trashItem('avatars/AnhLe.jpg'),
        ]);

        $result = app(CloudinaryTrashBin::class)->purgeMany($items, 0);

        $this->assertSame(['purged' => 0, 'failed' => 0, 'remaining' => 2], $result);
        $this->assertSame(2, CloudinaryTrash::count());
        $this->assertSame(0, $this->destroyCalls());
    }

    public function test_only_admin_can_bulk_delete(): void
    {
        $this->fake();
        $instructor = User::factory()->create(['role' => User::ROLE_INSTRUCTOR]);
        $item = $this->trashItem('avatars/AnhLe.jpg');

        $this->actingAs($instructor)->post(route('admin.cloudinary.trash.bulk-store'), ['paths' => json_encode(['banners/AnhBanner.jpg'])])->assertForbidden();
        $this->actingAs($instructor)->post(route('admin.cloudinary.trash.bulk-restore'), ['ids' => json_encode([(string) $item->id])])->assertForbidden();
        $this->actingAs($instructor)->delete(route('admin.cloudinary.trash.bulk-destroy'), ['ids' => json_encode([(string) $item->id])])->assertForbidden();

        $this->assertModelExists($item);
        $this->assertSame('banners/AnhBanner.jpg', $this->banner->fresh()->image_path);
        $this->assertSame(0, $this->destroyCalls());
    }
}
