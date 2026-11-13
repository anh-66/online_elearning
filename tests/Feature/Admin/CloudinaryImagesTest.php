<?php

namespace Tests\Feature\Admin;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\Client\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class CloudinaryImagesTest extends TestCase
{
    use RefreshDatabase;

    private const LIST_URL = 'api.cloudinary.com/v1_1/demo/resources/image/upload*';

    private const USAGE_URL = 'api.cloudinary.com/v1_1/demo/usage';

    protected function setUp(): void
    {
        parent::setUp();

        $this->withoutVite();
        config(['cache.default' => 'array']);
    }

    private function useCloudinary(): void
    {
        config(['filesystems.disks.public' => [
            'driver' => 'cloudinary',
            'cloudinary_url' => 'cloudinary://123456:secret@demo',
            'folder' => 'online-elearning',
            'throw' => false,
            'report' => false,
        ]]);
        Storage::forgetDisk('public');
    }

    private function admin(): User
    {
        return User::factory()->create(['role' => User::ROLE_ADMIN, 'status' => User::STATUS_ACTIVE]);
    }

    /**
     * @return array<string, mixed>
     */
    private function resource(string $publicId, string $createdAt, string $format = 'jpg'): array
    {
        return [
            'public_id' => 'online-elearning/'.$publicId,
            'format' => $format,
            'resource_type' => 'image',
            'type' => 'upload',
            'created_at' => $createdAt,
            'bytes' => 204800,
            'width' => 800,
            'height' => 600,
            'secure_url' => 'https://res.cloudinary.com/demo/image/upload/v1/online-elearning/'.$publicId.'.'.$format,
        ];
    }

    private function fakeCloudinary(array $resources, array $usage = []): void
    {
        Http::fake([
            self::LIST_URL => Http::response(['resources' => $resources]),
            self::USAGE_URL => Http::response($usage ?: [
                'plan' => 'Free',
                'credits' => ['usage' => 1.25, 'limit' => 25, 'used_percent' => 5],
                'storage' => ['usage' => 5242880],
                'bandwidth' => ['usage' => 1048576],
            ]),
        ]);
    }

    public function test_only_admin_can_open_the_page(): void
    {
        $student = User::factory()->create(['role' => User::ROLE_STUDENT]);

        $this->actingAs($student)->get(route('admin.cloudinary.index'))->assertForbidden();
    }

    public function test_page_explains_setup_when_cloudinary_is_not_configured(): void
    {
        Http::preventStrayRequests();

        $this->actingAs($this->admin())
            ->get(route('admin.cloudinary.index'))
            ->assertOk()
            ->assertSee('Website chưa kết nối Cloudinary')
            ->assertSee('CLOUDINARY_URL');
    }

    public function test_sidebar_links_to_the_page(): void
    {
        $this->actingAs($this->admin())
            ->get(route('admin.dashboard'))
            ->assertOk()
            ->assertSee(route('admin.cloudinary.index'))
            ->assertSee('Ảnh Cloudinary');
    }

    public function test_admin_sees_project_images_newest_first_with_thumbnails(): void
    {
        $this->useCloudinary();
        $this->fakeCloudinary([
            $this->resource('avatars/old', '2026-11-01T08:00:00Z', 'png'),
            $this->resource('banners/home', '2026-11-10T08:00:00Z'),
        ]);

        $this->actingAs($this->admin())
            ->get(route('admin.cloudinary.index'))
            ->assertOk()
            ->assertSeeInOrder(['home.jpg', 'old.png'])
            ->assertSee('Banner trang chủ')
            ->assertSee('Ảnh đại diện')
            ->assertSee('https://res.cloudinary.com/demo/image/upload/c_fill,w_400,h_300/f_auto,q_auto/v1/online-elearning/banners/home.jpg', false)
            ->assertSee('400 KB')
            ->assertSee('1.25 / 25 credits');

        Http::assertSent(fn (Request $request): bool => str_starts_with($request->url(), 'https://api.cloudinary.com/v1_1/demo/resources/image/upload')
            && $request['prefix'] === 'online-elearning/'
            && (int) $request['max_results'] === 500
            && $request->hasHeader('Authorization', 'Basic '.base64_encode('123456:secret')));
    }

    public function test_all_cursor_pages_are_loaded(): void
    {
        $this->useCloudinary();
        Http::fake([
            self::LIST_URL => Http::sequence()
                ->push(['resources' => [$this->resource('avatars/a', '2026-11-01T08:00:00Z')], 'next_cursor' => 'abc'])
                ->push(['resources' => [$this->resource('avatars/b', '2026-11-02T08:00:00Z')]]),
            self::USAGE_URL => Http::response([]),
        ]);

        $this->actingAs($this->admin())
            ->get(route('admin.cloudinary.index'))
            ->assertOk()
            ->assertSeeInOrder(['b.jpg', 'a.jpg']);

        Http::assertSent(fn (Request $request): bool => ($request->data()['next_cursor'] ?? null) === 'abc');
    }

    public function test_images_can_be_filtered_by_folder_and_keyword(): void
    {
        $this->useCloudinary();
        $this->fakeCloudinary([
            $this->resource('avatars/student-face', '2026-11-01T08:00:00Z'),
            $this->resource('courses/thumbnails/laravel-cover', '2026-11-02T08:00:00Z'),
            $this->resource('courses/thumbnails/php-cover', '2026-11-03T08:00:00Z'),
        ]);
        $admin = $this->admin();

        $this->actingAs($admin)
            ->get(route('admin.cloudinary.index', ['folder' => 'courses/thumbnails']))
            ->assertOk()
            ->assertSee('laravel-cover.jpg')
            ->assertSee('php-cover.jpg')
            ->assertDontSee('student-face.jpg');

        $this->actingAs($admin)
            ->get(route('admin.cloudinary.index', ['q' => 'LARAVEL']))
            ->assertOk()
            ->assertSee('laravel-cover.jpg')
            ->assertDontSee('php-cover.jpg');
    }

    public function test_results_are_cached_until_admin_refreshes(): void
    {
        $this->useCloudinary();
        $this->fakeCloudinary([$this->resource('banners/home', '2026-11-10T08:00:00Z')]);
        $admin = $this->admin();

        $this->actingAs($admin)->get(route('admin.cloudinary.index'))->assertOk();
        $this->actingAs($admin)->get(route('admin.cloudinary.index', ['page' => 2]))->assertOk();
        Http::assertSentCount(2); // 1 lần lấy danh sách + 1 lần lấy mức sử dụng

        $this->actingAs($admin)
            ->get(route('admin.cloudinary.index', ['refresh' => 1, 'folder' => 'banners']))
            ->assertRedirect(route('admin.cloudinary.index', ['folder' => 'banners']))
            ->assertSessionHas('success');

        $this->actingAs($admin)->get(route('admin.cloudinary.index'))->assertOk();
        Http::assertSentCount(4);
    }

    public function test_cloudinary_error_is_shown_instead_of_crashing(): void
    {
        $this->useCloudinary();
        Http::fake([
            self::LIST_URL => Http::response(['error' => ['message' => 'Invalid api_key 123456']], 401),
        ]);

        $this->actingAs($this->admin())
            ->get(route('admin.cloudinary.index'))
            ->assertOk()
            ->assertSee('Không lấy được danh sách ảnh từ Cloudinary')
            ->assertSee('Invalid api_key 123456')
            ->assertDontSee('123456:secret');
    }

    public function test_empty_folder_shows_empty_state(): void
    {
        $this->useCloudinary();
        $this->fakeCloudinary([]);

        $this->actingAs($this->admin())
            ->get(route('admin.cloudinary.index'))
            ->assertOk()
            ->assertSee('Chưa có ảnh nào trên Cloudinary.');
    }
}
