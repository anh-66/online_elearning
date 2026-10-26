<?php

namespace App\Providers;

use Closure;
use Illuminate\Support\ServiceProvider;
use Illuminate\Validation\Rules\Password;

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
        // Every password: at least 6 characters, one uppercase letter and one special character.
        Password::defaults(fn () => Password::min(6)->symbols()->rules([
            function (string $attribute, mixed $value, Closure $fail): void {
                if (! preg_match('/\p{Lu}/u', (string) $value)) {
                    $fail(':Attribute phải chứa ít nhất một chữ in hoa.');
                }
            },
        ]));
    }
}
