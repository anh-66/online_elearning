<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Auth\Events\PasswordReset;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Illuminate\Validation\Rules;
use Illuminate\View\View;

/**
 * Quên mật khẩu, bước cuối: tạo mật khẩu mới. Chỉ vào được sau khi đã nhập đúng mã.
 */
class NewPasswordController extends Controller
{
    /**
     * Khoá session ghi nhận "đã nhập đúng mã": ['user_id' => ..., 'until' => timestamp].
     */
    public const SESSION_VERIFIED = 'password_reset_verified';

    /**
     * Trang tạo mật khẩu mới.
     */
    public function create(Request $request): View|RedirectResponse
    {
        $user = $this->verifiedUser($request);
        if ($user === null) {
            return $this->backToStart($request);
        }

        return view('auth.new-password', [
            'email' => $user->email,
        ]);
    }

    /**
     * Lưu mật khẩu mới.
     */
    public function store(Request $request): RedirectResponse
    {
        $user = $this->verifiedUser($request);
        if ($user === null) {
            return $this->backToStart($request);
        }

        $validated = $request->validate([
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
        ]);

        $user->forceFill([
            'password' => Hash::make($validated['password']),
            'remember_token' => Str::random(60),
        ])->save();

        // Nhận được mã qua email nghĩa là người này đúng là chủ email.
        if ($user->email_verified_at === null) {
            $user->markEmailAsVerified();
        }

        event(new PasswordReset($user));

        $request->session()->forget([
            self::SESSION_VERIFIED,
            'password_reset_email',
            'password_reset_requested_at',
            'password_reset_fake_attempts',
        ]);

        return redirect()->route('login')->with('status', 'Đổi mật khẩu thành công. Bạn đăng nhập bằng mật khẩu mới nhé.');
    }

    /**
     * Tài khoản đã nhập đúng mã trong session này, nếu còn trong thời gian cho phép.
     */
    private function verifiedUser(Request $request): ?User
    {
        $state = $request->session()->get(self::SESSION_VERIFIED);
        if (! is_array($state) || (int) ($state['until'] ?? 0) < now()->getTimestamp()) {
            return null;
        }

        $user = User::query()->find($state['user_id'] ?? null);

        return $user !== null && $user->isActive() ? $user : null;
    }

    /**
     * Chưa nhập mã (hoặc quá hạn) thì quay lại bước nhập email.
     */
    private function backToStart(Request $request): RedirectResponse
    {
        $message = $request->session()->has(self::SESSION_VERIFIED)
            ? 'Đã quá '.config('otp.reset_window_minutes', 10).' phút kể từ lúc nhập mã. Bạn nhận mã mới để đặt lại mật khẩu nhé.'
            : 'Bạn cần nhập mã xác nhận gửi qua email trước khi tạo mật khẩu mới.';

        $request->session()->forget(self::SESSION_VERIFIED);

        return redirect()->route('password.request')->withErrors(['email' => $message]);
    }
}
