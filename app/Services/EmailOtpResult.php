<?php

namespace App\Services;

/**
 * Kết quả của một lần gửi mã hoặc kiểm tra mã OTP, kèm câu thông báo tiếng Việt cho người dùng.
 */
final class EmailOtpResult
{
    public const SENT = 'sent';

    public const SEND_FAILED = 'send_failed';

    public const COOLDOWN = 'cooldown';

    public const RATE_LIMITED = 'rate_limited';

    public const VALID = 'valid';

    public const INVALID = 'invalid';

    public const EXPIRED = 'expired';

    public const TOO_MANY_ATTEMPTS = 'too_many_attempts';

    public const MISSING = 'missing';

    public const DAILY_LIMIT = 'daily_limit';

    private function __construct(
        public readonly string $status,
        public readonly int $retryAfter = 0,
        public readonly int $attemptsLeft = 0,
    ) {}

    public static function sent(): self
    {
        return new self(self::SENT);
    }

    public static function sendFailed(): self
    {
        return new self(self::SEND_FAILED);
    }

    public static function cooldown(int $seconds): self
    {
        return new self(self::COOLDOWN, retryAfter: $seconds);
    }

    public static function rateLimited(int $seconds): self
    {
        return new self(self::RATE_LIMITED, retryAfter: $seconds);
    }

    public static function dailyLimit(): self
    {
        return new self(self::DAILY_LIMIT);
    }

    public static function valid(): self
    {
        return new self(self::VALID);
    }

    public static function invalid(int $attemptsLeft): self
    {
        return new self(self::INVALID, attemptsLeft: $attemptsLeft);
    }

    public static function expired(): self
    {
        return new self(self::EXPIRED);
    }

    public static function tooManyAttempts(): self
    {
        return new self(self::TOO_MANY_ATTEMPTS);
    }

    public static function missing(): self
    {
        return new self(self::MISSING);
    }

    public function is(string $status): bool
    {
        return $this->status === $status;
    }

    public function message(): string
    {
        return match ($this->status) {
            self::SENT => 'Mã gồm 6 số đã được gửi tới email của bạn.',
            self::SEND_FAILED => 'Hệ thống chưa gửi được email lúc này. Bạn đợi một chút rồi bấm "Gửi lại mã" nhé.',
            self::COOLDOWN => 'Mã vừa được gửi. Bạn đợi '.self::duration($this->retryAfter).' nữa rồi mới gửi lại được.',
            self::RATE_LIMITED => 'Bạn đã xin quá nhiều mã. Thử lại sau '.self::duration($this->retryAfter).' nữa nhé.',
            self::DAILY_LIMIT => 'Hôm nay hệ thống đã gửi quá nhiều email. Bạn thử lại vào ngày mai hoặc liên hệ quản trị viên nhé.',
            self::VALID => 'Mã chính xác.',
            self::INVALID => "Mã không đúng. Bạn còn {$this->attemptsLeft} lần thử.",
            self::EXPIRED => 'Mã đã hết hạn. Bấm "Gửi lại mã" để nhận mã mới.',
            self::TOO_MANY_ATTEMPTS => 'Bạn đã nhập sai quá nhiều lần. Bấm "Gửi lại mã" để nhận mã mới.',
            default => 'Chưa có mã nào đang chờ. Bấm "Gửi lại mã" để nhận mã mới.',
        };
    }

    /**
     * "45 giây", "2 phút", "1 phút 30 giây".
     */
    public static function duration(int $seconds): string
    {
        $seconds = max(1, $seconds);
        if ($seconds < 60) {
            return $seconds.' giây';
        }

        if ($seconds >= 300) {
            return (int) ceil($seconds / 60).' phút';
        }

        $minutes = intdiv($seconds, 60);
        $rest = $seconds % 60;

        return $minutes.' phút'.($rest > 0 ? ' '.$rest.' giây' : '');
    }
}
