<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckRole
{
    /**
     * Handle an incoming request.
     *
     * @param  Closure(Request): (Response)  $next
     */
    public function handle(Request $request, Closure $next, string ...$roles): Response
    {
        // 1. Chưa đăng nhập
        if (! $request->user()) {
            return redirect()->guest(route('login'));
        }

        // 2. Tài khoản bị khóa (status !== 'active')
        if (! $request->user()->isActive()) {
            auth()->logout();
            $request->session()->invalidate();
            $request->session()->regenerateToken();

            return redirect()->route('login')->with('error', 'Tài khoản của bạn đã bị khóa hoặc ngừng hoạt động. Vui lòng liên hệ quản trị viên.');
        }

        // 3. Kiểm tra vai trò (Role)
        // Lưu ý: Tài khoản có role 'admin' có thể truy cập được các route của admin hoặc nếu cần có thể nới lỏng
        if (! in_array($request->user()->role, $roles, true)) {
            abort(403, 'Bạn không có quyền truy cập vào chức năng hoặc trang này.');
        }

        return $next($request);
    }
}
