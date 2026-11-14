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

        // Giới hạn gửi lại theo email, tính cả email chưa đăng ký để không lộ email nào có tài khoản.
        $throttle = $otp->resetEmailThrottle($email);
        $wait = $throttle->secondsUntilNext();
        if ($wait > 0) {
            $limited = $throttle->hourlyLimitReached()
                ? EmailOtpResult::rateLimited($wait)
                : EmailOtpResult::cooldown($wait);
            // Chưa gửi mã mới nên giữ nguyên số lần thử nếu vẫn là email đó.
            $this->openCodeStep($request, $email, newCode: false);

            return redirect()->route('password.reset')->with('otp_problem', $limited->message());
        }

        if ($otp->dailyLimitReached()) {
            return back()->withInput()->withErrors(['email' => EmailOtpResult::dailyLimit()->message()]);
        }

        $throttle->hit();

        $user = User::query()->where('email', $email)->first();

        $result = null;
        if ($user !== null && $user->isActive()) {
            $result = $otp->send($user, EmailOtp::PURPOSE_RESET_PASSWORD);
        }

        if ($result?->is(EmailOtpResult::SEND_FAILED)) {
            return back()->withInput()->withErrors(['email' => $result->message()]);
        }

        $this->openCodeStep($request, $email);

        if ($result !== null && ! $result->is(EmailOtpResult::SENT)) {
            return redirect()->route('password.reset')->with('otp_problem', $result->message());
        }

        // Luôn trả lời giống nhau dù email có tồn tại hay không, để người lạ không dò được email nào đã đăng ký.
        return redirect()->route('password.reset')->with(
            'status',
            'Nếu email này đã đăng ký, mã gồm 6 số vừa được gửi tới hộp thư (nhớ xem cả mục Spam). Mã có hiệu lực '.config('otp.ttl_minutes', 10).' phút.',
        );
    }

    /**
     * Ghi email vào session để sang bước nhập mã. Xin mã mới thì đóng bước tạo mật khẩu (nếu đang mở).
     */
    private function openCodeStep(Request $request, string $email, bool $newCode = true): void
    {
        if ($newCode || $request->session()->get('password_reset_email') !== $email) {
            $request->session()->forget('password_reset_fake_attempts');
        }

        $request->session()->put('password_reset_email', $email);
        $request->session()->forget(NewPasswordController::SESSION_VERIFIED);
    }
}
