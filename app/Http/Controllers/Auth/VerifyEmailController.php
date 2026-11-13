<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Auth\Concerns\RedirectsAfterEmailVerification;
use App\Http\Controllers\Controller;
use App\Models\EmailOtp;
use App\Services\EmailOtpResult;
use App\Services\EmailOtpService;
use Illuminate\Auth\Events\Verified;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class VerifyEmailController extends Controller
{
    use RedirectsAfterEmailVerification;

    /**
     * Kiểm tra mã OTP người dùng nhập. Đúng thì đánh dấu email đã xác thực.
     */
    public function __invoke(Request $request, EmailOtpService $otp): RedirectResponse
    {
        $user = $request->user();

        if ($user->hasVerifiedEmail()) {
            return redirect()->intended($this->homeAfterVerification($user));
        }

        $length = (int) config('otp.length', 6);
        $validated = $request->validate([
            'code' => ['required', 'digits:'.$length],
        ], [
            'code.required' => 'Bạn chưa nhập mã.',
            'code.digits' => "Mã gồm đúng {$length} chữ số.",
        ]);

        $result = $otp->check($user, EmailOtp::PURPOSE_VERIFY_EMAIL, $validated['code']);

        if (! $result->is(EmailOtpResult::VALID)) {
            return back()->withErrors(['code' => $result->message()]);
        }

        if ($user->markEmailAsVerified()) {
            event(new Verified($user));
        }

        return redirect()->intended($this->homeAfterVerification($user))
            ->with('status', 'Xác thực email thành công!');
    }
}
