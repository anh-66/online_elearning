<?php

namespace Tests\Feature;

use App\Models\Banner;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class SeedIfEmptyCommandTest extends TestCase
{
    use RefreshDatabase;

    public function test_it_seeds_demo_data_when_database_is_empty(): void
    {
        $this->artisan('app:seed-if-empty')->assertSuccessful();

        $this->assertDatabaseHas('users', ['email' => 'admin@gmail.com']);
        $this->assertTrue(Banner::query()->exists());
    }

    public function test_it_keeps_uploaded_images_when_database_already_has_data(): void
    {
        $this->artisan('app:seed-if-empty')->assertSuccessful();

        $banner = Banner::where('title', 'Nâng tầm kỹ năng lập trình cùng chuyên gia')->firstOrFail();
        $banner->update(['image_path' => 'banners/anh-admin-da-tai-len.jpg']);

        $admin = User::where('email', 'admin@gmail.com')->firstOrFail();
        $admin->update(['avatar' => 'avatars/admin.jpg']);

        // Chạy lại lệnh lần nữa: database đã có dữ liệu nên phải bỏ qua.
        $this->artisan('app:seed-if-empty')
            ->expectsOutputToContain('bỏ qua seeder')
            ->assertSuccessful();

        $this->assertSame('banners/anh-admin-da-tai-len.jpg', $banner->fresh()->image_path);
        $this->assertSame('avatars/admin.jpg', $admin->fresh()->avatar);
    }
}
