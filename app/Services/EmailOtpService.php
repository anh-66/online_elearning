<?php

namespace App\Services;

use App\Mail\EmailOtpMail;
use App\Models\EmailLog;
use App\Models\EmailOtp;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Str;
use Throwable;

/**
 * Tạo, gửi và kiểm tra mã OTP qua email (xác thực email khi đăng ký, quên mật khẩu).
 *
 * - Mã 6 số, hết hạn sau 10 phút, sai 5 lần thì phải xin mã mới (config/otp.php).
 * - Chỉ lưu bản băm của mã. Gửi mã mới thì mã cũ hết hiệu lực.
 * - Thời gian chờ gửi lại tăng dần (60 giây, 2 phút, 5 phút, 10 phút), tối đa 5 mã/giờ cho mỗi tài khoản.
 * - Toàn web tối đa otp.max_sends_per_day mail/ngày để không vượt hạn mức miễn phí của Brevo.
 */
class EmailOtpService
{
    public function enabled(): bool
    {
        return (bool) config('otp.enabled', true);
    }

    public function send(User $user, string $purpose): EmailOtpResult
    {
        $throttle = $this->throttle($user, $purpose);
        $wait = $throttle->secondsUntilNext();
        if ($wait > 0) {
            return $throttle->hourlyLimitReached()
                ? EmailOtpResult::rateLimited($wait)
                : EmailOtpResult::cooldown($wait);
        }

        if ($this->dailyLimitReached()) {
            $maxPerDay = (int) config('otp.max_sends_per_day', 250);
            $this->record($user, $purpose, EmailLog::STATUS_FAILED, error: "Đã gửi đủ {$maxPerDay} mail OTP hôm nay (giới hạn EMAIL_OTP_MAX_PER_DAY), không gửi thêm.");

            return EmailOtpResult::dailyLimit();
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
        $throttle->hit();
        RateLimiter::hit($this->dailyKey(), 86400);

        try {
            $sent = Mail::to($user)->send(new EmailOtpMail($user, $code, $purpose, $ttl));
        } catch (Throwable $exception) {
            // Mã vẫn dùng được: ghi vào log để chủ web vẫn test được khi Brevo lỗi (storage/logs/laravel.log).
            Log::error('Brevo: gửi mail OTP thất bại.', [
                'email' => $user->email,
                'purpose' => $purpose,
                'code' => $code,
                'error' => $exception->getMessage(),
            ]);
            $this->record($user, $purpose, EmailLog::STATUS_FAILED, error: $exception->getMessage());

            return EmailOtpResult::sendFailed();
        }

        $this->record($user, $purpose, EmailLog::STATUS_SENT, messageId: $sent?->getMessageId());

        return EmailOtpResult::sent();
    }

    /**
     * Ghi nhật ký cho trang admin "Email đã gửi" (không lưu mã). Lỗi khi ghi không được làm hỏng việc gửi mã.
     */
    private function record(User $user, string $purpose, string $status, ?string $messageId = null, ?string $error = null): void
    {
        try {
            EmailLog::create([
                'user_id' => $user->getKey(),
                'to_email' => $user->email,
                'purpose' => $purpose,
                'mailer' => (string) config('mail.default'),
                'status' => $status,
                'message_id' => $messageId !== null ? Str::limit($messageId, 250, '') : null,
                'error' => $error !== null ? Str::limit($error, 1000) : null,
            ]);
        } catch (Throwable $exception) {
            Log::warning('Không ghi được nhật ký gửi mail.', ['error' => $exception->getMessage()]);
        }
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
        return $this->throttle($user, $purpose)->secondsUntilNext();
    }

    /**
     * Còn được gửi bao nhiêu mã nữa trong giờ hiện tại.
     */
    public function remainingSends(User $user, string $purpose): int
    {
        return $this->throttle($user, $purpose)->remaining();
    }

    /**
     * Toàn web đã gửi đủ số mail OTP cho phép trong hôm nay chưa (otp.max_sends_per_day, 0 là không giới hạn).
     */
    public function dailyLimitReached(): bool
    {
        $maxPerDay = (int) config('otp.max_sends_per_day', 250);

        return $maxPerDay > 0 && RateLimiter::attempts($this->dailyKey()) >= $maxPerDay;
    }

    /**
     * Giới hạn gửi lại theo email ở trang quên mật khẩu. Áp dụng cho cả email chưa đăng ký
     * để câu trả lời giống nhau, không lộ email nào có tài khoản.
     */
    public function resetEmailThrottle(string $email): OtpSendThrottle
    {
        return OtpSendThrottle::for('reset_email:'.sha1(Str::lower(trim($email))));
    }

    private function throttle(User $user, string $purpose): OtpSendThrottle
    {
        return OtpSendThrottle::for($purpose.':user:'.$user->getKey());
    }

    private function dailyKey(): string
    {
        return 'email-otp-day:'.now()->toDateString();
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
