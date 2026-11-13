<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Auth\Concerns\RedirectsAfterEmailVerification;
use App\Http\Controllers\Controller;
use App\Models\EmailOtp;
use App\Services\EmailOtpResult;
use App\Services\EmailOtpService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class EmailVerificationPromptController extends Controller
{
    use RedirectsAfterEmailVerification;

    /**
     * Trang nhập mã OTP xác thực email.
     */
    public function __invoke(Request $request, EmailOtpService $otp): RedirectResponse|View
    {
        $user = $request->user();

        if ($user->hasVerifiedEmail()) {
            return redirect()->intended($this->homeAfterVerification($user));
        }

        // Chưa có mã hoặc mã cũ đã hết hạn (ví dụ quay lại đăng nhập hôm sau): tự gửi mã mới.
        $autoSent = null;
        if ($otp->needsFreshCode($user, EmailOtp::PURPOSE_VERIFY_EMAIL)) {
            $autoSent = $otp->send($user, EmailOtp::PURPOSE_VERIFY_EMAIL);
        }

        return view('auth.verify-email', [
            'email' => $user->email,
            'resendIn' => $otp->secondsUntilResend($user, EmailOtp::PURPOSE_VERIFY_EMAIL),
            'notice' => $autoSent?->is(EmailOtpResult::SENT) ? 'Mã mới gồm 6 số đã được gửi tới email của bạn.' : null,
            'problem' => $autoSent !== null && ! $autoSent->is(EmailOtpResult::SENT) ? $autoSent->message() : null,
        ]);
    }
}
