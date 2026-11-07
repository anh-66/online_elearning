<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOneThrough;

#[Fillable([
    'chapter_id',
    'title',
    'content',
    'video_url',
    'attachment_path',
    'order',
])]
class Lesson extends Model
{
    use HasFactory;

    protected static function booted(): void
    {
        static::saving(function (Lesson $lesson) {
            // Tự động trích xuất link video YouTube nếu giảng viên chèn vào CKEditor mà chưa điền ô video_url
            if (empty($lesson->video_url) && ! empty($lesson->content)) {
                $extracted = static::extractYouTubeUrl($lesson->content);
                if ($extracted) {
                    $lesson->video_url = $extracted;
                }
            }
        });
    }

    /**
     * Tự động trích xuất link YouTube từ nội dung HTML nếu có (hỗ trợ oembed, iframe, link thường).
     */
    public static function extractYouTubeUrl(?string $html): ?string
    {
        if (! $html) {
            return null;
        }

        $pattern = '/(?:https?:)?\/\/(?:www\.|m\.)?(?:youtube\.com\/(?:watch\?(?:.*&)?v=|embed\/|shorts\/|v\/)|youtu\.be\/)([\w-]{11})/i';
        if (preg_match($pattern, $html, $matches)) {
            return 'https://www.youtube.com/watch?v='.$matches[1];
        }

        return null;
    }

    /**
     * Accessor cho video_url: nếu cột DB đang null nhưng trong content có chứa video YouTube,
     * tự động trả về link video để các màn hình hiển thị huy hiệu Video và phát video ngay lập tức.
     */
    protected function videoUrl(): Attribute
    {
        return Attribute::make(
            get: function (?string $value) {
                if (! empty($value)) {
                    return $value;
                }

                return static::extractYouTubeUrl($this->content);
            },
            set: fn (?string $value) => $value,
        );
    }

    public function chapter(): BelongsTo
    {
        return $this->belongsTo(Chapter::class);
    }

    public function progress(): HasMany
    {
        return $this->hasMany(LessonProgress::class);
    }

    public function comments(): HasMany
    {
        return $this->hasMany(LessonComment::class);
    }

    public function course(): HasOneThrough
    {
        return $this->hasOneThrough(
            Course::class,
            Chapter::class,
            'id',          // Foreign key on Chapter table (referenced by Course)
            'id',          // Foreign key on Course table
            'chapter_id',  // Local key on Lesson table
            'course_id'    // Local key on Chapter table
        );
    }
}
