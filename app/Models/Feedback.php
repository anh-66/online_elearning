<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\Storage;

class Feedback extends Model
{
    use HasFactory;

    protected $table = 'feedbacks';

    public const STATUS_PENDING = 'pending';

    public const STATUS_IN_PROGRESS = 'in_progress';

    public const STATUS_RESOLVED = 'resolved';

    public const STATUS_CLOSED = 'closed';

    public const CATEGORY_BUG = 'bug';

    public const CATEGORY_FEATURE = 'feature';

    public const CATEGORY_QUESTION = 'question';

    public const CATEGORY_OTHER = 'other';

    public const PRIORITY_LOW = 'low';

    public const PRIORITY_MEDIUM = 'medium';

    public const PRIORITY_HIGH = 'high';

    protected $fillable = [
        'user_id',
        'sender_name',
        'sender_email',
        'title',
        'category',
        'priority',
        'description',
        'attachment',
        'status',
        'admin_notes',
        'resolved_at',
    ];

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'resolved_at' => 'datetime',
        ];
    }

    /**
     * The user who submitted the feedback, if authenticated.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Vietnamese label for category.
     */
    public function getCategoryLabelAttribute(): string
    {
        return match ($this->category) {
            self::CATEGORY_BUG => 'Lỗi kỹ thuật',
            self::CATEGORY_FEATURE => 'Góp ý tính năng',
            self::CATEGORY_QUESTION => 'Câu hỏi',
            default => 'Khác',
        };
    }

    /**
     * Vietnamese label for priority.
     */
    public function getPriorityLabelAttribute(): string
    {
        return match ($this->priority) {
            self::PRIORITY_HIGH => 'Cao',
            self::PRIORITY_LOW => 'Thấp',
            default => 'Trung bình',
        };
    }

    /**
     * Priority badge classes for UI.
     */
    public function getPriorityBadgeClassAttribute(): string
    {
        return match ($this->priority) {
            self::PRIORITY_HIGH => 'bg-red-50 text-red-700 border-red-200 dark:bg-red-950/40 dark:text-red-400 dark:border-red-800',
            self::PRIORITY_LOW => 'bg-gray-50 text-gray-700 border-gray-200 dark:bg-neutral-800 dark:text-gray-300 dark:border-neutral-700',
            default => 'bg-amber-50 text-amber-700 border-amber-200 dark:bg-amber-950/40 dark:text-amber-400 dark:border-amber-800',
        };
    }

    /**
     * Vietnamese label for status.
     */
    public function getStatusLabelAttribute(): string
    {
        return match ($this->status) {
            self::STATUS_IN_PROGRESS => 'Đang xử lý',
            self::STATUS_RESOLVED => 'Đã giải quyết',
            self::STATUS_CLOSED => 'Đã đóng',
            default => 'Chờ xử lý',
        };
    }

    /**
     * Status badge classes for UI.
     */
    public function getStatusBadgeClassAttribute(): string
    {
        return match ($this->status) {
            self::STATUS_IN_PROGRESS => 'bg-blue-50 text-blue-700 border-blue-200 dark:bg-blue-950/40 dark:text-blue-400 dark:border-blue-800',
            self::STATUS_RESOLVED => 'bg-emerald-50 text-emerald-700 border-emerald-200 dark:bg-emerald-950/40 dark:text-emerald-400 dark:border-emerald-800',
            self::STATUS_CLOSED => 'bg-gray-100 text-gray-700 border-gray-200 dark:bg-neutral-800 dark:text-gray-400 dark:border-neutral-700',
            default => 'bg-amber-50 text-amber-700 border-amber-200 dark:bg-amber-950/40 dark:text-amber-400 dark:border-amber-800',
        };
    }

    /**
     * URL for the uploaded attachment, if present.
     */
    public function getAttachmentUrlAttribute(): ?string
    {
        if (! $this->attachment) {
            return null;
        }

        return Storage::disk('public')->url($this->attachment);
    }
}
