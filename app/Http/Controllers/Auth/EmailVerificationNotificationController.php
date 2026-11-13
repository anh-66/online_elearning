<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Auth\Concerns\RedirectsAfterEmailVerification;
use App\Http\Controllers\Controller;
use App\Models\EmailOtp;
use App\Services\EmailOtpResult;
use App\Services\EmailOtpService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class EmailVerificationNotificationController extends Controller
{
    use RedirectsAfterEmailVerification;

    /**
     * Gửi lại mã OTP xác thực email.
     */
    public function store(Request $request, EmailOtpService $otp): RedirectResponse
    {
        $user = $request->user();

        if ($user->hasVerifiedEmail()) {
            return redirect()->intended($this->homeAfterVerification($user));
        }

        $result = $otp->send($user, EmailOtp::PURPOSE_VERIFY_EMAIL);

        return $result->is(EmailOtpResult::SENT)
            ? back()->with('otp_notice', 'Mã mới gồm 6 số đã được gửi tới email của bạn.')
            : back()->with('otp_problem', $result->message());
    }
}
