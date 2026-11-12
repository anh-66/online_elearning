<?php

namespace App\Providers;

use App\Filesystem\CloudinaryAdapter;
use App\Services\GeminiClient;
use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Filesystem\FilesystemAdapter;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\ServiceProvider;
use Illuminate\Validation\Rules\Password;
use League\Flysystem\Filesystem;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->app->bind(GeminiClient::class, fn () => new GeminiClient(
            config('services.gemini.key'),
            config('services.gemini.model'),
        ));
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        Storage::extend('cloudinary', function (Application $app, array $config): FilesystemAdapter {
            $adapter = CloudinaryAdapter::fromUrl($config['cloudinary_url'], $config['folder'] ?? '');

            return new FilesystemAdapter(new Filesystem($adapter, $config), $adapter, $config);
        });

        // Giới hạn chat AI theo người dùng (hoặc IP nếu chưa đăng nhập) để không hết lượt Gemini miễn phí.
        RateLimiter::for('course-chat', function (Request $request) {
            $visitor = $request->user() ? 'user:'.$request->user()->id : 'ip:'.$request->ip();
            $tooMany = fn (string $message) => fn (Request $request, array $headers) => response()->json(
                ['message' => $message],
                429,
                $headers,
            );

            return [
                Limit::perMinute(5)->by('minute:'.$visitor)
                    ->response($tooMany('Bạn gửi hơi nhanh, đợi khoảng 1 phút rồi hỏi tiếp nhé.')),
                Limit::perDay(30)->by('day:'.$visitor)
                    ->response($tooMany('Bạn đã dùng hết 30 câu hỏi hôm nay, mai quay lại nhé.')),
            ];
        });

        Password::defaults(function () {
            return Password::min(8)
                ->letters()
                ->numbers();
        });
    }
}
