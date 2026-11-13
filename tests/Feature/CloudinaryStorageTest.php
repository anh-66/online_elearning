<?php

namespace Tests\Feature;

use App\Filesystem\CloudinaryAdapter;
use App\Models\Banner;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\Client\Request;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class CloudinaryStorageTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        config(['filesystems.disks.public' => [
            'driver' => 'cloudinary',
            'cloudinary_url' => 'cloudinary://123456:secret@demo',
            'folder' => 'online-elearning',
            'throw' => false,
            'report' => false,
        ]]);
        Storage::forgetDisk('public');
    }

    public function test_signature_matches_cloudinary_documentation_example(): void
    {
        $adapter = new CloudinaryAdapter('demo', 'key', 'abcd');

        $params = $adapter->signedParams([
            'public_id' => 'sample_image',
            'timestamp' => 1315060510,
            'eager' => 'w_400,h_300,c_pad|w_260,h_200,c_crop',
        ]);

        $this->assertSame('bfd09f95f331f558cbd1320e67aa8d488770583e', $params['signature']);
        $this->assertSame('key', $params['api_key']);
    }

    public function test_uploaded_image_is_sent_to_cloudinary_and_keeps_laravel_path(): void
    {
        Http::fake(['api.cloudinary.com/*' => Http::response(['public_id' => 'ok'])]);

        $path = UploadedFile::fake()->image('cover.jpg')->store('courses/thumbnails', 'public');

        $this->assertStringStartsWith('courses/thumbnails/', $path);
        Http::assertSent(function (Request $request) use ($path): bool {
            return $request->url() === 'https://api.cloudinary.com/v1_1/demo/image/upload'
                && $request->hasFile('file')
                && $this->multipartValue($request, 'public_id') === 'online-elearning/'.substr($path, 0, -4)
                && $this->multipartValue($request, 'api_key') === '123456'
                && filled($this->multipartValue($request, 'signature'));
        });
        $this->assertSame(
            'https://res.cloudinary.com/demo/image/upload/online-elearning/'.$path,
            Storage::disk('public')->url($path),
        );
    }

    public function test_documents_are_uploaded_as_raw_files(): void
    {
        Http::fake(['api.cloudinary.com/*' => Http::response(['public_id' => 'ok'])]);

        $path = UploadedFile::fake()->create('slides.pdf', 100, 'application/pdf')
            ->store('lessons/attachments', 'public');

        Http::assertSent(fn (Request $request): bool => $request->url() === 'https://api.cloudinary.com/v1_1/demo/raw/upload'
            && $this->multipartValue($request, 'public_id') === 'online-elearning/'.$path);
        $this->assertSame(
            'https://res.cloudinary.com/demo/raw/upload/online-elearning/'.$path,
            Storage::disk('public')->url($path),
        );
    }

    public function test_deleting_a_file_destroys_it_on_cloudinary(): void
    {
        Http::fake(['api.cloudinary.com/*' => Http::response(['result' => 'ok'])]);

        $this->assertTrue(Storage::disk('public')->delete('banners/home.png'));

        Http::assertSent(fn (Request $request): bool => $request->url() === 'https://api.cloudinary.com/v1_1/demo/image/destroy'
            && $request['public_id'] === 'online-elearning/banners/home');
    }

    public function test_failed_upload_returns_false(): void
    {
        Http::fake(['api.cloudinary.com/*' => Http::response(['error' => ['message' => 'Invalid API key']], 401)]);

        $this->assertFalse(UploadedFile::fake()->image('cover.jpg')->store('courses/thumbnails', 'public'));
    }

    public function test_pages_link_uploaded_images_to_cloudinary_without_api_calls(): void
    {
        Http::preventStrayRequests();
        Banner::create([
            'title' => 'Banner Cloudinary',
            'image_path' => 'banners/home.jpg',
            'is_active' => true,
        ]);
        $user = User::factory()->create(['avatar' => 'avatars/me.png']);

        $this->get(route('home'))
            ->assertOk()
            ->assertSee('https://res.cloudinary.com/demo/image/upload/online-elearning/banners/home.jpg');
        $this->assertSame(
            'https://res.cloudinary.com/demo/image/upload/online-elearning/avatars/me.png',
            $user->avatar_url,
        );
    }

    private function multipartValue(Request $request, string $name): ?string
    {
        $part = collect($request->data())->firstWhere('name', $name);

        return $part === null ? null : (string) $part['contents'];
    }
}
