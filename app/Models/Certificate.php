<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * Chứng chỉ hoàn thành khóa học, ai có mã (hoặc quét QR) đều xem và xác minh được.
 */
#[Fillable(['code', 'user_id', 'course_id', 'recipient_name', 'course_title', 'instructor_name', 'lessons_count', 'issued_at'])]
class Certificate extends Model
{
    /**
     * Bỏ các ký tự dễ nhầm (0/O, 1/I/L) để đọc mã qua điện thoại không bị sai.
     */
    private const CODE_ALPHABET = 'ABCDEFGHJKMNPQRSTUVWXYZ23456789';

    public const DISPLAY_TIMEZONE = 'Asia/Ho_Chi_Minh';

    protected function casts(): array
    {
        return [
            'issued_at' => 'datetime',
            'lessons_count' => 'integer',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function course(): BelongsTo
    {
        return $this->belongsTo(Course::class);
    }

    /**
     * Mã dạng EDU-2026-7KQ4-M9XP.
     */
    public static function generateCode(): string
    {
        $part = function (): string {
            $chars = '';

            for ($i = 0; $i < 4; $i++) {
                $chars .= self::CODE_ALPHABET[random_int(0, strlen(self::CODE_ALPHABET) - 1)];
            }

            return $chars;
        };

        return sprintf('EDU-%s-%s-%s', now()->format('Y'), $part(), $part());
    }

    /**
     * Chuẩn hoá mã người dùng gõ vào ô tra cứu: bỏ khoảng trắng, viết hoa.
     */
    public static function normalizeCode(?string $code): string
    {
        return strtoupper(preg_replace('/\s+/', '', (string) $code) ?? '');
    }

    public function url(): string
    {
        return route('certificates.show', $this->code);
    }

    public function isOwnedBy(?User $user): bool
    {
        return $user !== null && (int) $user->getKey() === (int) $this->user_id;
    }

    /**
     * Ngày cấp viết đầy đủ, ví dụ "14 tháng 11 năm 2026".
     */
    public function issuedOnText(): string
    {
        $date = $this->issued_at->copy()->timezone(self::DISPLAY_TIMEZONE);

        return sprintf('%d tháng %d năm %d', $date->day, $date->month, $date->year);
    }

    /**
     * Link "Thêm vào hồ sơ LinkedIn" (mục Licenses & certifications), miễn phí, không cần API.
     */
    public function linkedInUrl(): string
    {
        return 'https://www.linkedin.com/profile/add?'.http_build_query([
            'startTask' => 'CERTIFICATION_NAME',
            'name' => $this->course_title,
            'organizationName' => config('app.name', 'EduLearn'),
            'issueYear' => $this->issued_at->year,
            'issueMonth' => $this->issued_at->month,
            'certUrl' => $this->url(),
            'certId' => $this->code,
        ]);
    }
}
