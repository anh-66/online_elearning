<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Tài khoản đã đăng nhập nhưng chưa nhập mã OTP xác thực email thì vào trang nào
 * cũng bị đưa về trang nhập mã. Khách chưa đăng nhập không bị ảnh hưởng.
 */
class EnsureEmailIsVerifiedWithOtp
{
    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();

        if (! $user instanceof MustVerifyEmail || $user->hasVerifiedEmail() || $this->isAllowed($request)) {
            return $next($request);
        }

        if ($request->expectsJson()) {
            return response()->json(['message' => 'Bạn cần nhập mã xác thực email trước khi tiếp tục.'], 409);
        }

        return redirect()->guest(route('verification.notice'));
    }

    /**
     * Những trang vẫn mở được khi chưa xác thực: trang nhập mã, gửi lại mã, đăng xuất và file ảnh.
     */
    private function isAllowed(Request $request): bool
    {
        return $request->routeIs('verification.*', 'logout') || $request->is('storage/*');
    }
}
