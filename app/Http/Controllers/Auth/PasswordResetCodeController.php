<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\EmailOtp;
use App\Models\User;
use App\Services\EmailOtpResult;
use App\Services\EmailOtpService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

/**
 * Quên mật khẩu, bước nhập mã: chỉ khi mã đúng mới được sang trang tạo mật khẩu mới.
 */
class PasswordResetCodeController extends Controller
{
    /**
     * Trang nhập mã 6 số vừa gửi về email.
     */
    public function create(Request $request, EmailOtpService $otp): View|RedirectResponse
    {
        $email = (string) $request->session()->get('password_reset_email', '');
        if ($email === '') {
            return redirect()->route('password.request');
        }

        $throttle = $otp->resetEmailThrottle($email);

        return view('auth.reset-password', [
            'email' => $email,
            'resendIn' => $throttle->secondsUntilNext(),
            'sendsLeft' => $throttle->remaining(),
        ]);
    }

    /**
     * Kiểm tra mã. Đúng thì ghi nhận vào session và chuyển sang bước tạo mật khẩu mới.
     */
    public function store(Request $request, EmailOtpService $otp): RedirectResponse
    {
        // Email lấy từ session (lúc xin mã), không lấy từ form, để không đổi được sang tài khoản khác.
        $email = (string) $request->session()->get('password_reset_email', '');
        if ($email === '') {
            return redirect()->route('password.request');
        }

        $length = (int) config('otp.length', 6);
        $validated = $request->validate([
            'code' => ['required', 'digits:'.$length],
        ], [
            'code.required' => 'Bạn chưa nhập mã.',
            'code.digits' => "Mã gồm đúng {$length} chữ số.",
        ]);

        $user = User::query()->where('email', $email)->first();

        $result = $user !== null && $user->isActive()
            ? $otp->check($user, EmailOtp::PURPOSE_RESET_PASSWORD, $validated['code'])
            : $this->answerForUnknownEmail($request);

        if (! $result->is(EmailOtpResult::VALID)) {
            return back()->withErrors(['code' => $result->message()]);
        }

        // Mã đã bị xoá sau khi dùng; đổi session id rồi mở bước tạo mật khẩu mới trong một thời gian ngắn.
        $request->session()->regenerate();
        $request->session()->put(NewPasswordController::SESSION_VERIFIED, [
            'user_id' => $user->getKey(),
            'until' => now()->addMinutes((int) config('otp.reset_window_minutes', 10))->getTimestamp(),
        ]);

        return redirect()->route('password.new');
    }

    /**
     * Email chưa đăng ký thì không có mã nào, nhưng vẫn trả lời như nhập sai mã
     * (đếm lùi số lần thử) để người lạ không dò được email nào đã đăng ký.
     */
    private function answerForUnknownEmail(Request $request): EmailOtpResult
    {
        $attempts = (int) $request->session()->get('password_reset_fake_attempts', 0) + 1;
        $request->session()->put('password_reset_fake_attempts', $attempts);

        $attemptsLeft = (int) config('otp.max_attempts', 5) - $attempts;

        return $attemptsLeft > 0
            ? EmailOtpResult::invalid($attemptsLeft)
            : EmailOtpResult::tooManyAttempts();
    }
}
