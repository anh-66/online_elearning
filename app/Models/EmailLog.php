<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Str;

/**
 * Một lần gửi mail (thành công hoặc thất bại), hiển thị ở trang admin "Email đã gửi".
 * Không lưu mã OTP hay tiêu đề mail.
 */
#[Fillable(['user_id', 'to_email', 'purpose', 'mailer', 'status', 'message_id', 'error'])]
class EmailLog extends Model
{
    public const STATUS_SENT = 'sent';

    public const STATUS_FAILED = 'failed';

    /**
     * Nhật ký cũ hơn số ngày này được xoá khi admin mở trang (Render Free không có cron).
     */
    public const KEEP_DAYS = 90;

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function isSent(): bool
    {
        return $this->status === self::STATUS_SENT;
    }

    /**
     * Mail chỉ được ghi vào file log (MAIL_MAILER=log), chưa tới hộp thư của ai.
     */
    public function onlyLogged(): bool
    {
        return in_array($this->mailer, ['log', 'array'], true);
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
     * Gợi ý cách sửa cho các lỗi hay gặp khi gửi qua Brevo.
     */
    public function errorHint(): ?string
    {
        if ($this->isSent() || $this->error === null) {
            return null;
        }

        $error = Str::lower($this->error);

        return match (true) {
            str_contains($error, 'brevo_api_key') => 'Chưa có BREVO_API_KEY: thêm vào Render > Environment rồi deploy lại.',
            str_contains($error, 'lỗi 401') => 'API key sai hoặc đã bị tắt, hoặc Brevo đang chặn IP của Render: vào Brevo > Security > Authorized IPs và tắt chặn.',
            str_contains($error, 'lỗi 400') && str_contains($error, 'sender') => 'Địa chỉ gửi (MAIL_FROM_ADDRESS) chưa được xác minh trong Brevo > Senders.',
            str_contains($error, 'lỗi 402') => 'Tài khoản Brevo hết lượt gửi (gói Free 300 mail/ngày). Đợi sang ngày mới.',
            str_contains($error, 'lỗi 429') => 'Gửi quá nhanh nên Brevo tạm chặn. Thử lại sau ít phút.',
            str_contains($error, 'không kết nối được') => 'Không kết nối được tới Brevo. Thường tự hết, thử gửi lại sau.',
            default => null,
        };
    }
}
