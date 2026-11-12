<?php

namespace App\Http\Controllers;

use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class OnboardingController extends Controller
{
    /**
     * Hiển thị màn hình chào mừng tài khoản mới với Mascot0.
     */
    public function show(Request $request): View|RedirectResponse
    {
        $user = Auth::user();

        // Nếu user đã hoàn thành onboarding và không có param preview, chuyển hướng về dashboard
        if ($user && $user->onboarding_completed && ! $request->boolean('preview')) {
            return redirect()->route($user->dashboardRouteName());
        }

        // Lấy câu chào theo yêu cầu:
        // Đăng ký thường có nhập họ tên -> "Xin chào [Họ tên]!"
        // Đăng nhập bằng Google -> "Xin chào!"
        $greeting = 'Xin chào!';
        if ($user) {
            if ($user->google_id) {
                $greeting = 'Xin chào!';
            } elseif (! empty($user->name)) {
                $greeting = 'Xin chào '.$user->name.'!';
            }
        } else {
            $greeting = 'Xin chào!';
        }

        return view('onboarding.welcome', [
            'user' => $user,
            'greeting' => $greeting,
            'mascotUrl' => asset('animations/mascots/mascot0.json'),
        ]);
    }

    /**
     * Xác nhận hoàn thành hoặc bỏ qua bước chào mừng.
     */
    public function complete(Request $request): RedirectResponse
    {
        $user = Auth::user();

        if ($user) {
            $user->update([
                'onboarding_completed' => true,
            ]);

            return redirect()->route($user->dashboardRouteName())
                ->with('status', 'Chào mừng bạn đến với EduLearn!');
        }

        return redirect()->route('home');
    }
}
