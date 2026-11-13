<?php

namespace App\Mail;

use App\Models\EmailOtp;
use App\Models\User;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;

/**
 * Mail chứa mã OTP 6 số (xác thực email khi đăng ký hoặc đặt lại mật khẩu).
 */
class EmailOtpMail extends Mailable
{
    public function __construct(
        public User $user,
        public string $code,
        public string $purpose,
        public int $ttlMinutes,
    ) {}

    public function envelope(): Envelope
    {
        $appName = config('app.name', 'EduLearn');

        return new Envelope(
            subject: $this->isPasswordReset()
                ? "Mã đặt lại mật khẩu {$appName}: {$this->code}"
                : "Mã xác thực email {$appName}: {$this->code}",
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.otp',
            text: 'emails.otp-text',
            with: [
                'appName' => config('app.name', 'EduLearn'),
                'heading' => $this->isPasswordReset() ? 'Đặt lại mật khẩu' : 'Xác thực địa chỉ email',
                'intro' => $this->isPasswordReset()
                    ? 'Bạn vừa yêu cầu đặt lại mật khẩu. Nhập mã dưới đây ở trang đặt lại mật khẩu:'
                    : 'Cảm ơn bạn đã đăng ký. Nhập mã dưới đây để xác thực email và bắt đầu học:',
            ],
        );
    }

    public function isPasswordReset(): bool
    {
        return $this->purpose === EmailOtp::PURPOSE_RESET_PASSWORD;
    }
}
