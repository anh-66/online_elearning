<?php

namespace App\Providers;

use App\Filesystem\CloudinaryAdapter;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Filesystem\FilesystemAdapter;
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
        //
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

        Password::defaults(function () {
            return Password::min(8)
                ->letters()
                ->numbers();
        });
    }
}
