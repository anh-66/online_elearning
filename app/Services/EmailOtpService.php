<?php

namespace App\Services;

use App\Mail\EmailOtpMail;
use App\Models\EmailOtp;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\RateLimiter;
use Throwable;

/**
 * Tạo, gửi và kiểm tra mã OTP qua email (xác thực email khi đăng ký, quên mật khẩu).
 *
 * - Mã 6 số, hết hạn sau 10 phút, sai 5 lần thì phải xin mã mới (config/otp.php).
 * - Chỉ lưu bản băm của mã. Gửi mã mới thì mã cũ hết hiệu lực.
 * - Phải chờ 60 giây giữa hai lần gửi, tối đa 5 mã/giờ cho mỗi tài khoản.
 */
class EmailOtpService
{
    public function enabled(): bool
    {
        return (bool) config('otp.enabled', true);
    }

    public function send(User $user, string $purpose): EmailOtpResult
    {
        $wait = $this->secondsUntilResend($user, $purpose);
        if ($wait > 0) {
            return EmailOtpResult::cooldown($wait);
        }

        $limiterKey = 'email-otp:'.$purpose.':'.$user->getKey();
        if (RateLimiter::tooManyAttempts($limiterKey, (int) config('otp.max_sends_per_hour', 5))) {
            return EmailOtpResult::rateLimited(RateLimiter::availableIn($limiterKey));
        }

        $ttl = (int) config('otp.ttl_minutes', 10);
        $code = $this->generateCode();

        EmailOtp::updateOrCreate(
            ['user_id' => $user->getKey(), 'purpose' => $purpose],
            [
                'code_hash' => Hash::make($code),
                'attempts' => 0,
                'expires_at' => now()->addMinutes($ttl),
                'sent_at' => now(),
            ],
        );
        RateLimiter::hit($limiterKey, 3600);

        try {
            Mail::to($user)->send(new EmailOtpMail($user, $code, $purpose, $ttl));
        } catch (Throwable $exception) {
            // Mã vẫn dùng được: ghi vào log để chủ web vẫn test được khi Brevo lỗi (Render > Logs).
            Log::error('Brevo: gửi mail OTP thất bại.', [
                'email' => $user->email,
                'purpose' => $purpose,
                'code' => $code,
                'error' => $exception->getMessage(),
            ]);

            return EmailOtpResult::sendFailed();
        }

        return EmailOtpResult::sent();
    }

    public function check(User $user, string $purpose, string $code): EmailOtpResult
    {
        $otp = $this->pending($user, $purpose);
        if ($otp === null) {
            return EmailOtpResult::missing();
        }

        $maxAttempts = (int) config('otp.max_attempts', 5);
        if ($otp->attempts >= $maxAttempts) {
            return EmailOtpResult::tooManyAttempts();
        }

        if ($otp->isExpired()) {
            return EmailOtpResult::expired();
        }

        if (! Hash::check($code, $otp->code_hash)) {
            $otp->increment('attempts');
            $attemptsLeft = $maxAttempts - $otp->attempts;

            return $attemptsLeft > 0
                ? EmailOtpResult::invalid($attemptsLeft)
                : EmailOtpResult::tooManyAttempts();
        }

        $otp->delete();

        return EmailOtpResult::valid();
    }

    /**
     * Có mã còn hạn và chưa bị khóa vì nhập sai quá nhiều lần hay không.
     */
    public function hasUsableCode(User $user, string $purpose): bool
    {
        $otp = $this->pending($user, $purpose);

        return $otp !== null
            && ! $otp->isExpired()
            && $otp->attempts < (int) config('otp.max_attempts', 5);
    }

    /**
     * Chưa có mã nào, hoặc mã cũ đã hết hạn: lúc này mới tự gửi mã mới.
     * Mã bị khóa vì nhập sai quá nhiều lần thì người dùng phải tự bấm "Gửi lại mã".
     */
    public function needsFreshCode(User $user, string $purpose): bool
    {
        $otp = $this->pending($user, $purpose);

        return $otp === null || $otp->isExpired();
    }

    public function secondsUntilResend(User $user, string $purpose): int
    {
        $otp = $this->pending($user, $purpose);
        if ($otp === null) {
            return 0;
        }

        $nextAllowed = $otp->sent_at->copy()->addSeconds((int) config('otp.resend_seconds', 60));

        return max(0, (int) ceil(now()->diffInSeconds($nextAllowed, false)));
    }

    public function forget(User $user, string $purpose): void
    {
        EmailOtp::query()->where('user_id', $user->getKey())->where('purpose', $purpose)->delete();
    }

    private function pending(User $user, string $purpose): ?EmailOtp
    {
        return EmailOtp::query()
            ->where('user_id', $user->getKey())
            ->where('purpose', $purpose)
            ->first();
    }

    private function generateCode(): string
    {
        $length = (int) config('otp.length', 6);

        return str_pad((string) random_int(0, 10 ** $length - 1), $length, '0', STR_PAD_LEFT);
    }
}
