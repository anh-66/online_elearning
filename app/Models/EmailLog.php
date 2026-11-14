<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Str;

/**
 * Một lần gửi mail, hiển thị ở trang admin "Email đã gửi". Không lưu mã OTP hay tiêu đề mail.
 *
 * Trạng thái:
 * - sent: Brevo đã nhận yêu cầu, đang chờ Brevo báo đã giao hay chưa (mailer khác Brevo: đã gửi đi).
 * - delivered: đã tới máy chủ hộp thư người nhận (có thể nằm trong Spam). opened_at có giá trị nếu đã mở.
 * - deferred: hộp thư người nhận tạm từ chối, Brevo đang thử gửi lại.
 * - rejected: Brevo không giao được (sai email người nhận, bị chặn, địa chỉ gửi chưa xác minh...).
 * - failed: không gửi được tới Brevo (sai API key, mất mạng, hết lượt...).
 */
#[Fillable(['user_id', 'to_email', 'purpose', 'mailer', 'status', 'message_id', 'error'])]
class EmailLog extends Model
{
    public const STATUS_SENT = 'sent';

    public const STATUS_DELIVERED = 'delivered';

    public const STATUS_DEFERRED = 'deferred';

    public const STATUS_REJECTED = 'rejected';

    public const STATUS_FAILED = 'failed';

    public const STATUSES = [
        self::STATUS_SENT,
        self::STATUS_DELIVERED,
        self::STATUS_DEFERRED,
        self::STATUS_REJECTED,
        self::STATUS_FAILED,
    ];

    /**
     * Nhật ký cũ hơn số ngày này được xoá khi admin mở trang (không cần cron).
     */
    public const KEEP_DAYS = 90;

    /**
     * Chỉ hỏi Brevo trạng thái của mail gửi trong ngần này ngày.
     */
    public const TRACK_DAYS = 3;

    protected function casts(): array
    {
        return [
            'delivered_at' => 'datetime',
            'opened_at' => 'datetime',
            'checked_at' => 'datetime',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function isSent(): bool
    {
        return $this->status === self::STATUS_SENT;
    }

    public function isDelivered(): bool
    {
        return $this->status === self::STATUS_DELIVERED;
    }

    /**
     * Mail không tới được người nhận (lỗi trước hoặc sau khi tới Brevo).
     */
    public function isProblem(): bool
    {
        return in_array($this->status, [self::STATUS_FAILED, self::STATUS_REJECTED], true);
    }

    /**
     * Mail chỉ được ghi vào file log (MAIL_MAILER=log), chưa tới hộp thư của ai.
     */
    public function onlyLogged(): bool
    {
        return in_array($this->mailer, ['log', 'array'], true);
    }

    /**
     * Gửi qua Brevo nên hỏi được Brevo đã giao hay chưa.
     */
    public function isTrackable(): bool
    {
        return $this->mailer === 'brevo' && filled($this->message_id);
    }

    /**
     * Brevo đã nhận nhưng chưa báo kết quả cuối cùng.
     */
    public function isAwaitingDelivery(): bool
    {
        return $this->isTrackable() && in_array($this->status, [self::STATUS_SENT, self::STATUS_DEFERRED], true);
    }

    public function statusLabel(): string
    {
        return match ($this->status) {
            self::STATUS_DELIVERED => $this->opened_at ? 'Đã tới, đã mở' : 'Đã tới hộp thư',
            self::STATUS_DEFERRED => 'Đang thử lại',
            self::STATUS_REJECTED => 'Bị từ chối',
            self::STATUS_FAILED => 'Thất bại',
            default => match (true) {
                $this->onlyLogged() => 'Chỉ ghi log',
                ! $this->isTrackable() => 'Đã gửi đi',
                (bool) $this->created_at?->lt(now()->subDays(self::TRACK_DAYS)) => 'Không rõ',
                default => 'Chờ xác nhận',
            },
        };
    }

    /**
     * Màu của nhãn trạng thái: green, amber, red, sky, gray.
     */
    public function statusTone(): string
    {
        return match ($this->status) {
            self::STATUS_DELIVERED => 'green',
            self::STATUS_DEFERRED => 'amber',
            self::STATUS_REJECTED, self::STATUS_FAILED => 'red',
            default => $this->onlyLogged() ? 'gray' : 'sky',
        };
    }

    public function purposeLabel(): string
    {
        return match ($this->purpose) {
            EmailOtp::PURPOSE_VERIFY_EMAIL => 'Xác thực email',
            EmailOtp::PURPOSE_RESET_PASSWORD => 'Quên mật khẩu',
            default => Str::headline($this->purpose),
        };
    }

    public function mailerLabel(): string
    {
        return match ($this->mailer) {
            'brevo' => 'Brevo',
            'log' => 'Chỉ ghi log',
            'array' => 'Thử nghiệm',
            default => $this->mailer,
        };
    }

    /**
     * Lỗi do cấu hình phía web (địa chỉ gửi, API key, hết lượt...), không phải do email người nhận.
     */
    public function isSenderProblem(): bool
    {
        $error = Str::lower((string) $this->error);

        return $this->status === self::STATUS_FAILED
            || str_contains($error, 'sender');
    }

    /**
     * Gợi ý cách sửa cho các lỗi hay gặp khi gửi qua Brevo.
     */
    public function errorHint(): ?string
    {
        if ($this->error === null || ! in_array($this->status, [self::STATUS_FAILED, self::STATUS_REJECTED, self::STATUS_DEFERRED], true)) {
            return null;
        }

        $error = Str::lower($this->error);

        return match (true) {
            str_contains($error, 'brevo_api_key') => 'Chưa có BREVO_API_KEY: thêm vào Render > Environment rồi deploy lại.',
            str_contains($error, 'lỗi 401') => 'API key sai hoặc đã bị tắt, hoặc Brevo đang chặn IP của Render: vào Brevo > Security > Authorized IPs và tắt chặn.',
            str_contains($error, 'sender') => 'Địa chỉ gửi (MAIL_FROM_ADDRESS trong Render > Environment) chưa được xác minh trong Brevo > Senders. Đặt bằng đúng Gmail đã xác minh rồi deploy lại.',
            str_contains($error, 'lỗi 402') => 'Tài khoản Brevo hết lượt gửi (gói Free 300 mail/ngày). Đợi sang ngày mới.',
            str_contains($error, 'lỗi 429') => 'Gửi quá nhanh nên Brevo tạm chặn. Thử lại sau ít phút.',
            str_contains($error, 'không kết nối được') => 'Không kết nối được tới Brevo. Thường tự hết, thử gửi lại sau.',
            str_contains($error, 'email_otp_max_per_day') => 'Đã chạm giới hạn mail mỗi ngày của web. Tăng EMAIL_OTP_MAX_PER_DAY trong Render > Environment nếu cần (gói Free của Brevo tối đa 300 mail/ngày).',
            str_contains($error, 'blocked') => 'Brevo đang chặn địa chỉ này (từng bị trả về, báo spam hoặc hủy nhận). Gỡ trong danh sách Blocked ở mục Transactional của Brevo rồi gửi lại.',
            str_contains($error, 'hardbounce'), str_contains($error, 'invalid') => 'Email người nhận không tồn tại hoặc gõ sai. Người dùng cần đăng ký lại bằng email đúng.',
            $this->status === self::STATUS_DEFERRED => 'Hộp thư người nhận tạm từ chối (đầy hộp thư hoặc máy chủ bận). Brevo sẽ tự gửi lại, chờ thêm ít phút.',
            default => null,
        };
    }
}
