<?php

namespace App\Services;

use Illuminate\Support\Facades\Cache;

/**
 * Đếm số lần gửi mã OTP cho một "chủ thể" (một tài khoản, hoặc một email khi quên mật khẩu).
 *
 * - Sau mỗi lần gửi phải chờ, thời gian chờ tăng dần (config otp.resend_wait_seconds).
 * - Tối đa otp.max_sends_per_hour lần trong một giờ, tính từ lần gửi đầu tiên của giờ đó.
 */
class OtpSendThrottle
{
    private const WINDOW_SECONDS = 3600;

    private function __construct(private readonly string $key) {}

    public static function for(string $subject): self
    {
        return new self('otp-send-throttle:'.$subject);
    }

    /**
     * Số mã đã gửi trong giờ hiện tại.
     */
    public function sends(): int
    {
        return (int) ($this->state()['count'] ?? 0);
    }

    public function remaining(): int
    {
        return max(0, $this->maxPerHour() - $this->sends());
    }

    public function hourlyLimitReached(): bool
    {
        return $this->sends() >= $this->maxPerHour();
    }

    /**
     * Còn bao nhiêu giây nữa mới được gửi mã tiếp (0 là gửi được ngay).
     */
    public function secondsUntilNext(): int
    {
        $state = $this->state();
        if ($state === null) {
            return 0;
        }

        $now = now()->getTimestamp();

        if ($this->hourlyLimitReached()) {
            return max(1, $state['window_until'] - $now);
        }

        return max(0, $state['last_at'] + $this->waitAfter($state['count']) - $now);
    }

    public function hit(): void
    {
        $now = now()->getTimestamp();
        $state = $this->state() ?? ['count' => 0, 'window_until' => $now + self::WINDOW_SECONDS];

        $state['count']++;
        $state['last_at'] = $now;

        Cache::put($this->key, $state, now()->addSeconds(max(1, $state['window_until'] - $now)));
    }

    public function clear(): void
    {
        Cache::forget($this->key);
    }

    /**
     * Thời gian chờ sau mã thứ $sends trong giờ.
     */
    public function waitAfter(int $sends): int
    {
        $steps = array_values(array_map('intval', (array) config('otp.resend_wait_seconds', [60])));
        if ($steps === []) {
            return 0;
        }

        return $steps[min(max($sends, 1), count($steps)) - 1];
    }

    /**
     * @return array{count: int, window_until: int, last_at: int}|null
     */
    private function state(): ?array
    {
        $state = Cache::get($this->key);

        if (! is_array($state) || ($state['window_until'] ?? 0) <= now()->getTimestamp()) {
            return null;
        }

        return $state;
    }

    private function maxPerHour(): int
    {
        return max(1, (int) config('otp.max_sends_per_hour', 5));
    }
}
