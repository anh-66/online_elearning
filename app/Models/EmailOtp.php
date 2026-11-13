<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Hidden;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * Mã OTP đang chờ nhập của một tài khoản (đã băm, không lưu mã gốc).
 */
#[Fillable(['user_id', 'purpose', 'code_hash', 'attempts', 'expires_at', 'sent_at'])]
#[Hidden(['code_hash'])]
class EmailOtp extends Model
{
    public const PURPOSE_VERIFY_EMAIL = 'verify_email';

    public const PURPOSE_RESET_PASSWORD = 'reset_password';

    protected function casts(): array
    {
        return [
            'attempts' => 'integer',
            'expires_at' => 'datetime',
            'sent_at' => 'datetime',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function isExpired(): bool
    {
        return $this->expires_at->isPast();
    }
}
