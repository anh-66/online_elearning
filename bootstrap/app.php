<?php

use App\Http\Middleware\CheckRole;
use App\Http\Middleware\EnsureEmailIsVerifiedWithOtp;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
        then: function () {
            // Nhóm route dành riêng cho Studio Giảng viên (TV5)
            Route::middleware(['web', 'auth', 'role:instructor,admin'])
                ->prefix('instructor')
                ->name('instructor.')
                ->group(base_path('routes/instructor.php'));

            // Nhóm route dành riêng cho Quản trị viên Admin (TV6)
            Route::middleware(['web', 'auth', 'role:admin'])
                ->prefix('admin')
                ->name('admin.')
                ->group(base_path('routes/admin.php'));
        },
    )
    ->withMiddleware(function (Middleware $middleware): void {
        // Render (và các host tương tự) đứng sau proxy HTTPS: tin header X-Forwarded-*
        // để URL, asset và cookie được sinh đúng với https.
        $middleware->trustProxies(at: '*');

        $middleware->alias([
            'role' => CheckRole::class,
        ]);

        // Tài khoản chưa nhập mã OTP xác thực email thì bị đưa về trang nhập mã.
        $middleware->web(append: [
            EnsureEmailIsVerifiedWithOtp::class,
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        $exceptions->shouldRenderJsonWhen(
            fn (Request $request) => $request->is('api/*') || $request->expectsJson(),
        );
    })->create();
