<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\EmailOtp;
use App\Models\User;
use App\Services\EmailOtpResult;
use App\Services\EmailOtpService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\View\View;

class PasswordResetLinkController extends Controller
{
    /**
     * Trang "Quên mật khẩu": nhập email để nhận mã OTP.
     */
    public function create(): View
    {
        return view('auth.forgot-password');
    }

    /**
     * Gửi mã OTP đặt lại mật khẩu về email (nếu email đó đã đăng ký).
     */
    public function store(Request $request, EmailOtpService $otp): RedirectResponse
    {
        $validated = $request->validate([
            'email' => ['required', 'email'],
        ], [
            'email.required' => 'Bạn chưa nhập email.',
            'email.email' => 'Email không đúng định dạng.',
        ]);

        $email = Str::lower(trim($validated['email']));
        $user = User::query()->where('email', $email)->first();

        $result = null;
        if ($user !== null && $user->isActive()) {
            $result = $otp->send($user, EmailOtp::PURPOSE_RESET_PASSWORD);
        }

        if ($result?->is(EmailOtpResult::SEND_FAILED)) {
            return back()->withInput()->withErrors(['email' => $result->message()]);
        }

        // Luôn trả lời giống nhau dù email có tồn tại hay không, để người lạ không dò được email nào đã đăng ký.
        $request->session()->put('password_reset_email', $email);
        $request->session()->put('password_reset_requested_at', now()->getTimestamp());
        $request->session()->forget(['password_reset_fake_attempts', NewPasswordController::SESSION_VERIFIED]);

        return redirect()->route('password.reset')->with(
            'status',
            'Nếu email này đã đăng ký, mã gồm 6 số vừa được gửi tới hộp thư (nhớ xem cả mục Spam). Mã có hiệu lực '.config('otp.ttl_minutes', 10).' phút.',
        );
    }
}
